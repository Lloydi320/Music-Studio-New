<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // Store feedback from authenticated user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'photo' => 'nullable|string',
            'name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id(); // Assign current user

        $feedback = Feedback::create($validated);

        return response()->json($feedback, 201);
    }

    // Get feedback for authenticated user
    public function myFeedback()
    {
        $feedbacks = Feedback::where('user_id', Auth::id())->latest()->get();
        return response()->json(['feedbacks' => $feedbacks]);
    }

    // Get all feedback for display
    public function index()
    {
        $feedbacks = Feedback::latest()->get(['name', 'rating', 'comment', 'photo', 'created_at']);
        return response()->json(['feedbacks' => $feedbacks]);
    }
}
