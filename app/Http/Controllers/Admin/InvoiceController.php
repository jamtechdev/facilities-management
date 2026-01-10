<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Timesheet;
use App\DataTables\InvoiceDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            'hourly_rate' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
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
            return DB::transaction(function() use ($request) {
                $client = Client::findOrFail($request->client_id);

                // Calculate total hours from timesheets in the billing period
                $timesheets = Timesheet::where('client_id', $client->id)
                    ->whereBetween('work_date', [$request->billing_period_start, $request->billing_period_end])
                    ->where('is_approved', true)
                    ->get();

                $totalHours = $timesheets->sum('payable_hours');
                $subtotal = $totalHours * $request->hourly_rate;
                $tax = $request->tax ?? 0;
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
                    'redirect' => route('superadmin.invoices.index')
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
        $invoice->load(['client', 'createdBy']);
        return view('superadmin.invoices.show', compact('invoice'));
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
