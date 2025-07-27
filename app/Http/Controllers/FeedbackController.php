<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // Store feedback from user (authenticated or guest)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow image uploads up to 2MB
        ]);

        // Map comment to content for database storage
        $validated['content'] = $validated['comment'];
        
        // Set user_id to null if user is not authenticated
        $validated['user_id'] = Auth::check() ? Auth::id() : null;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('feedback-photos', $photoName, 'public');
            $validated['photo'] = $photoPath;
        }

        $feedback = Feedback::create($validated);
        
        // Add photo URL if photo was uploaded
        if ($feedback->photo) {
            $feedback->photo_url = asset('storage/' . $feedback->photo);
        }
        
        // Add user type indicator
        $feedback->user_type = $feedback->user_id ? 'Authenticated' : 'Guest';
        
        return response()->json([
            'message' => 'Feedback submitted successfully!',
            'feedback' => $feedback
        ], 201);
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
        $feedbacks = Feedback::latest()->get(['id', 'name', 'rating', 'comment', 'content', 'photo', 'created_at', 'user_id']);
        
        // Add full URL to photo paths and enhance data
        $feedbacks->transform(function ($feedback) {
            if ($feedback->photo) {
                $feedback->photo_url = asset('storage/' . $feedback->photo);
            }
            
            // Add user type indicator
            $feedback->user_type = $feedback->user_id ? 'Authenticated' : 'Guest';
            
            // Ensure comment field is populated
            if (empty($feedback->comment) && !empty($feedback->content)) {
                $feedback->comment = $feedback->content;
            }
            
            return $feedback;
        });
        
        return response()->json(['feedbacks' => $feedbacks]);
    }
}
