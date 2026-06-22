<?php

namespace App\Http\Controllers;

use App\Models\ProcessCategory;
use App\Models\Process;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = ProcessCategory::withCount('processes')->get();
        return view('dashboard', compact('categories'));
    }

    public function category(ProcessCategory $category)
    {
        $processes = $category->processes()->paginate(10);
        return view('processes.index', compact('category', 'processes'));
    }
}
