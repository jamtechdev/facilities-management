<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client as ClientModel;
use App\Models\Timesheet;
use App\Models\Feedback;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ensure user has client dashboard permission
        if (!$user->can('view client dashboard')) {
            abort(403, 'You do not have permission to access the client dashboard.');
        }

        // Prevent other role users from accessing client dashboard
        if ($user->can('view admin dashboard') || $user->can('view staff dashboard') || $user->can('view lead dashboard')) {
            abort(403, 'You must use your designated dashboard.');
        }

        $client = $user->client;

        if (!$client) {
            return redirect()->route('welcome')->with('error', 'Client profile not found.');
        }

        // Get recent service history
        $recentServices = Timesheet::where('client_id', $client->id)
            ->with(['staff', 'jobPhotos'])
            ->latest('work_date')
            ->take(10)
            ->get();

        // Get recent feedback
        $recentFeedback = Feedback::where('client_id', $client->id)
            ->latest()
            ->take(5)
            ->get();

        // Get assigned staff with all their details
        $staff = $client->staff()->with([
            'user',
            'timesheets' => function ($query) use ($client) {
                $query->where('client_id', $client->id);
            },
            'jobPhotos' => function ($query) use ($client) {
                $query->where('client_id', $client->id);
            },
            'documents'
        ])->wherePivot('is_active', true)->get();

        return view('client.dashboard', compact('client', 'recentServices', 'recentFeedback', 'staff'));
    }
}
