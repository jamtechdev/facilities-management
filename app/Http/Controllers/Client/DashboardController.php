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

        return view('client.dashboard', compact('client', 'recentServices', 'recentFeedback'));
    }
}
