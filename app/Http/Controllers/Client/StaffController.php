<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of staff assigned to the client
     */
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.dashboard')->with('error', 'Client profile not found.');
        }

        // Get assigned staff with basic info
        $staff = $client->staff()
            ->wherePivot('is_active', true)
            ->with('user')
            ->get();

        return view('client.staff.index', compact('staff', 'client'));
    }

    /**
     * Display the specified staff member details
     */
    public function show(Staff $staff)
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.dashboard')->with('error', 'Client profile not found.');
        }

        // Verify staff is assigned to this client
        $isAssigned = $client->staff()
            ->where('staff.id', $staff->id)
            ->wherePivot('is_active', true)
            ->exists();

        if (!$isAssigned) {
            abort(403, 'This staff member is not assigned to your account.');
        }

        // Load staff details with client-specific data
        $staff->load([
            'user',
            'timesheets' => function ($query) use ($client) {
                $query->where('client_id', $client->id)
                    ->latest('work_date');
            },
            'jobPhotos' => function ($query) use ($client) {
                $query->where('client_id', $client->id)
                    ->latest();
            },
            'documents'
        ]);

        // Get the pivot data for this client
        $assignment = $client->staff()
            ->where('staff.id', $staff->id)
            ->first();

        return view('client.staff.show', compact('staff', 'client', 'assignment'));
    }
}
