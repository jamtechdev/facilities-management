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

        // Ensure user has lead dashboard permission
        if (!$user->can('view lead dashboard')) {
            abort(403, 'You do not have permission to access the lead dashboard.');
        }

        // Prevent other role users from accessing lead dashboard
        if ($user->can('view admin dashboard') || $user->can('view staff dashboard') || $user->can('view client dashboard')) {
            abort(403, 'You must use your designated dashboard.');
        }

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
