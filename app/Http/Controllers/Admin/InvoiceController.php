<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Timesheet;
use App\Models\Communication;
use App\DataTables\InvoiceDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(InvoiceDataTable $dataTable)
    {
        return $dataTable->render('superadmin.invoices.index');
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $clients = Client::where('is_active', true)->get();
        return view('superadmin.invoices.create', compact('clients'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'hours' => 'nullable|numeric|min:0.1',
            'hourly_rate' => 'required|numeric|min:0.01',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $client = Client::findOrFail($request->client_id);

                // Use provided hours if given, otherwise calculate from timesheets
                if ($request->has('hours') && $request->hours > 0) {
                    $totalHours = $request->hours;
                } else {
                    // Calculate total hours from timesheets in the billing period
                    $timesheets = Timesheet::where('client_id', $client->id)
                        ->whereBetween('work_date', [$request->billing_period_start, $request->billing_period_end])
                        ->where('is_approved', true)
                        ->get();

                    $totalHours = $timesheets->sum('payable_hours');
                }

                $subtotal = $totalHours * $request->hourly_rate;
                $taxRate = $request->tax_rate ?? 0; // Tax rate as percentage
                $tax = $subtotal * ($taxRate / 100); // Calculate tax from percentage
                $totalAmount = $subtotal + $tax;

                // Generate invoice number
                $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Invoice::max('id') + 1, 6, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'client_id' => $client->id,
                    'billing_period_start' => $request->billing_period_start,
                    'billing_period_end' => $request->billing_period_end,
                    'total_hours' => $totalHours,
                    'hourly_rate' => $request->hourly_rate,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_amount' => $totalAmount,
                    'status' => Invoice::STATUS_DRAFT,
                    'due_date' => now()->addDays(30),
                    'notes' => $request->notes,
                    'created_by' => auth()->id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully.',
                    'redirect' => \App\Helpers\RouteHelper::url('invoices.index')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        // Check permission to view invoice details
        if (!auth()->user()->can('view invoice details')) {
            abort(403, 'You do not have permission to view invoice details.');
        }
        $invoice->load(['client', 'createdBy', 'communications.user']);
        return view('superadmin.invoices.show', compact('invoice'));
    }

    /**
     * Update the specified invoice
     */
    // public function update(Request $request, Invoice $invoice): JsonResponse
    // {
    //     // Check permission
    //     if (!auth()->user()->can('edit invoices')) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'You do not have permission to edit invoices.'
    //         ], 403);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'notes' => ['nullable', 'string'],
    //         'hourly_rate' => ['nullable', 'numeric', 'min:0'],
    //         'tax' => ['nullable', 'numeric', 'min:0'],
    //         'total_hours' => ['nullable', 'numeric', 'min:0'],
    //         'invoice_number' => ['nullable', 'string', 'max:255'],
    //         'billing_period_start' => ['nullable', 'date'],
    //         'billing_period_end' => ['nullable', 'date'],
    //         'due_date' => ['nullable', 'date'],
    //         'description' => ['nullable', 'string', 'max:255'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         $data = $validator->validated();

    //         // Recalculate subtotal and total if hourly_rate, tax, or total_hours changed
    //         $hourlyRate = $data['hourly_rate'] ?? $invoice->hourly_rate;
    //         $totalHours = $data['total_hours'] ?? $invoice->total_hours;
    //         $subtotal = $totalHours * $hourlyRate;

    //         // Calculate tax - if tax is provided, use it; otherwise calculate from existing tax
    //         // For now, keep backward compatibility - tax is stored as amount
    //         $tax = $data['tax'] ?? $invoice->tax;

    //         $totalAmount = $subtotal + $tax;

    //         $data['subtotal'] = $subtotal;
    //         $data['total_amount'] = $totalAmount;

    //         // Auto-set status to draft when invoice is edited
    //         if ($invoice->status !== 'draft') {
    //             $data['status'] = Invoice::STATUS_DRAFT;
    //         }

    //         $invoice->update($data);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Invoice updated successfully.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update invoice: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        // Check permission
        if (!auth()->user()->can('edit invoices')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit invoices.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'total_hours' => ['nullable', 'numeric', 'min:0'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'billing_period_start' => ['nullable', 'date'],
            'billing_period_end' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'], // optional, but not auto-overridden
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Recalculate totals if needed
            $hourlyRate = $data['hourly_rate'] ?? $invoice->hourly_rate;
            $totalHours = $data['total_hours'] ?? $invoice->total_hours;
            $subtotal = $totalHours * $hourlyRate;
            $tax = $data['tax'] ?? $invoice->tax;
            $totalAmount = $subtotal + $tax;

            $data['subtotal'] = $subtotal;
            $data['total_amount'] = $totalAmount;

            // ⚡ Remove auto-draft logic here
            // status will only change if 'status' key is explicitly sent
            if (isset($data['status'])) {
                $invoice->update($data);
            } else {
                // Update everything except status
                $invoice->update(array_merge($data, ['status' => $invoice->status]));
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, Invoice $invoice): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:draft,sent,paid,unpaid,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoice->update([
                'status' => $request->status,
                'paid_date' => $request->status === Invoice::STATUS_PAID ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice status updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['client', 'createdBy']);

        $pdf = Pdf::loadView('superadmin.invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(Request $request, Invoice $invoice): JsonResponse
    {
        // Check permission
        if (!auth()->user()->can('edit invoices')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to send invoices.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email_to' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoice->load(['client', 'createdBy']);

            // Generate PDF
            $pdf = Pdf::loadView('superadmin.invoices.pdf', compact('invoice'));
            $pdfContent = $pdf->output();

            // Send email with PDF attachment
            Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'message' => $request->message,
            ], function ($message) use ($request, $invoice, $pdfContent) {
                $message->to($request->email_to)
                    ->subject($request->subject ?? 'Invoice #' . $invoice->invoice_number)
                    ->attachData($pdfContent, 'invoice-' . $invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            // Store communication record
            Communication::create([
                'communicable_type' => Invoice::class,
                'communicable_id' => $invoice->id,
                'type' => Communication::TYPE_EMAIL,
                'subject' => $request->subject ?? 'Invoice #' . $invoice->invoice_number,
                'message' => $request->message ?? '',
                'user_id' => auth()->id(),
                'email_to' => $request->email_to,
                'email_from' => auth()->user()->email,
                'is_sent' => true,
            ]);

            Log::info('Invoice sent via email', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'email_to' => $request->email_to,
                'sent_by' => auth()->id()
            ]);

            // Update invoice status to 'sent'
            $invoice->update(['status' => Invoice::STATUS_SENT]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully. Status updated to Sent.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        try {
            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
