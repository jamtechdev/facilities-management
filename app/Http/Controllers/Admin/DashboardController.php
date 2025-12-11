<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('stage', 'new_lead')->count(),
            'qualified_leads' => Lead::where('stage', 'qualified')->count(),
            'total_clients' => Client::where('is_active', true)->count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'total_invoices' => Invoice::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
        ];

        $todayLeads = Lead::whereDate('created_at', today())->latest()->take(5)->get();
        $recentActivity = Lead::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'todayLeads', 'recentActivity'));
    }
}
