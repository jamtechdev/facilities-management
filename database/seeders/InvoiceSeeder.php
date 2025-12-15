<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $adminUser = User::role('Admin')->first() ?? User::first();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Please run ClientSeeder first.');
            return;
        }

        $invoiceNumber = 1000;

        foreach ($clients as $client) {
            // Create invoices for last 3 months
            for ($month = 0; $month < 3; $month++) {
                $billingPeriodStart = Carbon::now()->subMonths($month)->startOfMonth();
                $billingPeriodEnd = Carbon::now()->subMonths($month)->endOfMonth();

                // Get timesheets for this period
                $timesheets = Timesheet::where('client_id', $client->id)
                    ->whereBetween('work_date', [$billingPeriodStart, $billingPeriodEnd])
                    ->where('is_approved', true)
                    ->get();

                if ($timesheets->isEmpty()) {
                    continue;
                }

                // Calculate totals
                $totalHours = $timesheets->sum('payable_hours');
                $hourlyRate = 25.00; // Default rate, can be from staff or client
                $subtotal = $totalHours * $hourlyRate;
                $tax = $subtotal * 0.10; // 10% tax
                $totalAmount = $subtotal + $tax;

                // Determine status
                $statuses = ['draft', 'sent', 'paid', 'unpaid'];
                $status = $statuses[array_rand($statuses)];

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . str_pad($invoiceNumber++, 6, '0', STR_PAD_LEFT),
                    'client_id' => $client->id,
                    'billing_period_start' => $billingPeriodStart,
                    'billing_period_end' => $billingPeriodEnd,
                    'total_hours' => $totalHours,
                    'hourly_rate' => $hourlyRate,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'due_date' => $billingPeriodEnd->copy()->addDays(30),
                    'paid_date' => $status === 'paid' ? $billingPeriodEnd->copy()->addDays(rand(1, 15)) : null,
                    'notes' => rand(1, 3) > 2 ? 'Monthly cleaning services invoice.' : null,
                    'created_by' => $adminUser->id,
                    'created_at' => $billingPeriodEnd->copy()->addDays(1),
                ]);
            }
        }

        $this->command->info('Invoices seeded successfully!');
    }
}

