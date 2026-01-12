<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Client;
use App\Models\Timesheet;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return redirect()->to(RouteHelper::url('dashboard'))->with('error', 'Client profile not found.');
        }

        // Get only feedback submitted by this client
        $feedbacks = Feedback::where('client_id', $client->id)
            ->with('timesheet.staff', 'timesheet.client')
            ->latest()
            ->paginate(10);

        // Get completed timesheets for this client (for feedback dropdown)
        $completedTimesheets = Timesheet::where('client_id', $client->id)
            ->whereNotNull('clock_out_time')
            ->where('status', Timesheet::STATUS_COMPLETED)
            ->with('staff')
            ->orderBy('work_date', 'desc')
            ->orderBy('clock_out_time', 'desc')
            ->get();

        return view('client.feedback', compact('feedbacks', 'client', 'completedTimesheets'));
    }

    /**
     * Store new feedback from client
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client profile not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'timesheet_id' => 'required|exists:timesheets,id',
            'message' => 'required|string|min:10',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        // Verify timesheet belongs to this client
        $timesheet = Timesheet::where('id', $request->timesheet_id)
            ->where('client_id', $client->id)
            ->whereNotNull('clock_out_time')
            ->first();

        if (!$timesheet) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid timesheet selected or timesheet is not completed yet.'
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feedback = Feedback::create([
                'email' => $client->email,
                'name' => $client->contact_person ?? $user->name,
                'company' => $client->company_name,
                'message' => $request->message,
                'rating' => $request->rating,
                'client_id' => $client->id,
                'timesheet_id' => $request->timesheet_id,
                'is_processed' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
                'feedback' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback: ' . $e->getMessage()
            ], 500);
        }
    }
}
