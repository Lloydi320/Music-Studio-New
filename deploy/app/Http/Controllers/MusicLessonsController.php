<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;

class MusicLessonsController extends Controller
{
    public function index()
    {
        $carouselItems = CarouselItem::active()->ordered()->get();
        
        return view('music-lessons', compact('carouselItems'));
    }
}
