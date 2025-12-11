<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead as LeadModel;
use App\Models\Communication;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $lead = $user->lead;

        if (!$lead) {
            return redirect()->route('welcome')->with('error', 'Lead profile not found.');
        }

        // Get recent communications
        $recentCommunications = Communication::where('communicable_type', LeadModel::class)
            ->where('communicable_id', $lead->id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('lead.dashboard', compact('lead', 'recentCommunications'));
    }
}
