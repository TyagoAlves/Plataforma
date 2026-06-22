<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudyMaterialController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\OpenCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('study')->name('study.')->group(function () {
        Route::get('/', [StudyController::class, 'index'])->name('dashboard');

        Route::resource('subjects', SubjectController::class)->except(['create', 'edit']);

        Route::get('materials/create', [StudyMaterialController::class, 'create'])->name('materials.create');
        Route::post('materials', [StudyMaterialController::class, 'store'])->name('materials.store');
        Route::get('materials/{material}', [StudyMaterialController::class, 'show'])->name('materials.show');
        Route::delete('materials/{material}', [StudyMaterialController::class, 'destroy'])->name('materials.destroy');

        Route::get('quizzes', [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
        Route::post('quizzes', [QuizController::class, 'store'])->name('quizzes.store');
        Route::post('quizzes/generate', [QuizController::class, 'generateFromMaterial'])->name('quizzes.generate');
        Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
        Route::post('quizzes/{quiz}/answer', [QuizController::class, 'answer'])->name('quizzes.answer');
        Route::get('quizzes/{quiz}/results', [QuizController::class, 'results'])->name('quizzes.results');

        Route::get('slides', [SlideController::class, 'index'])->name('slides.index');
        Route::post('slides/generate', [SlideController::class, 'generateFromMaterial'])->name('slides.generate');
        Route::get('slides/{slide}', [SlideController::class, 'show'])->name('slides.show');

        Route::get('podcasts', [PodcastController::class, 'index'])->name('podcasts.index');
        Route::post('podcasts/generate', [PodcastController::class, 'generateFromMaterial'])->name('podcasts.generate');
        Route::get('podcasts/{podcast}', [PodcastController::class, 'show'])->name('podcasts.show');

        Route::get('opencode', [OpenCodeController::class, 'index'])->name('opencode.index');
        Route::get('opencode/browse', [OpenCodeController::class, 'browse'])->name('opencode.browse');
        Route::post('opencode/save', [OpenCodeController::class, 'save'])->name('opencode.save');
        Route::post('opencode/chat', [OpenCodeController::class, 'chat'])->name('opencode.chat');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
