<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;

Route::middleware('auth:sanctum')->get('/my-feedback', [FeedbackController::class, 'myFeedback']);
Route::middleware('auth:sanctum')->post('/feedback', [FeedbackController::class, 'store']);
Route::get('/feedbacks', [FeedbackController::class, 'index']);