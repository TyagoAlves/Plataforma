<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Slide;
use App\Models\Podcast;

class StudyController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('user_id', auth()->id())->withCount('studyMaterials')->get();
        $recentQuizzes = Quiz::where('user_id', auth()->id())->latest()->take(5)->get();
        $recentSlides = Slide::where('user_id', auth()->id())->latest()->take(5)->get();
        $recentPodcasts = Podcast::where('user_id', auth()->id())->latest()->take(5)->get();

        return view('study.dashboard', compact('subjects', 'recentQuizzes', 'recentSlides', 'recentPodcasts'));
    }
}
