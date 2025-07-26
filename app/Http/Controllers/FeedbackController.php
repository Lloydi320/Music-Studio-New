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
        $request->validate([
            'content' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('feedback_photos', 'public');
        }

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'photo' => $photoPath,
        ]);

        // For non-AJAX, redirect back
        return redirect()->back()->with('success', 'Feedback submitted!');
    }

    // Get feedback for authenticated user
    public function myFeedback()
    {
        $feedbacks = Feedback::where('user_id', Auth::id())->latest()->get();
        return response()->json(['feedbacks' => $feedbacks]);
    }
}
