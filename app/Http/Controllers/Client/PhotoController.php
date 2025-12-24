<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;

class PhotoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            abort(404, 'Client profile not found.');
        }

        $timesheets = Timesheet::where('client_id', $client->id)
            ->whereHas('jobPhotos', function ($query) {
                $query->where('status', 'approved');
            })
            ->with(['jobPhotos' => function ($query) {
                $query->where('status', 'approved')->orderBy('id');
            }, 'staff'])
            ->latest('work_date')
            ->paginate(12);

        $photoPairs = [];

        foreach ($timesheets as $timesheet) {
            $beforePhotos = $timesheet->jobPhotos
                ->where('photo_type', 'before')
                ->values();

            $afterPhotos = $timesheet->jobPhotos
                ->where('photo_type', 'after')
                ->values();

            if ($beforePhotos->count() > 0 || $afterPhotos->count() > 0) {
                $photoPairs[] = [
                    'timesheet' => $timesheet,
                    'before'    => $beforePhotos,
                    'after'     => $afterPhotos,
                    'date'      => $timesheet->work_date->format('d M Y'),
                    'staff_name' => $timesheet->staff?->name ?? 'Unknown Staff',
                    'total_photos' => $beforePhotos->count() + $afterPhotos->count(),
                ];
            }
        }

        return view('client.photos', compact('client', 'photoPairs', 'timesheets'));
    }
}
