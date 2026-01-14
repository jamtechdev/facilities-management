<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceHistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            abort(404, 'Client profile not found.');
        }

        $serviceHistory = $client->timesheets()
            ->with(['staff', 'jobPhotos', 'feedback'])
            ->latest()
            ->get();

        return view('client.service', compact('serviceHistory', 'client'));
    }
}
