<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
       $feedbacks = Feedback::latest()->paginate(10);
       return view('client.feedback', compact('feedbacks'));
    }
}
