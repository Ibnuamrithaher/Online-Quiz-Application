<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\QuizController as ParticipantQuizController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.quizzes.index');
    }
    return redirect()->route('quizzes.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('quizzes', AdminQuizController::class);
    Route::resource('quizzes.questions', AdminQuestionController::class)->except(['index']);
    Route::get('quizzes/{quiz}/attempts', [\App\Http\Controllers\Admin\AttemptController::class, 'index'])->name('quizzes.attempts.index');
    Route::get('quizzes/{quiz}/attempts/{attempt}', [\App\Http\Controllers\Admin\AttemptController::class, 'show'])->name('quizzes.attempts.show');
    Route::put('quizzes/{quiz}/attempts/{attempt}', [\App\Http\Controllers\Admin\AttemptController::class, 'grade'])->name('quizzes.attempts.grade');
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('quizzes', [ParticipantQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/history', [ParticipantQuizController::class, 'history'])->name('quizzes.history');
    Route::get('quizzes/{quiz}', [ParticipantQuizController::class, 'show'])->name('quizzes.show');
    
    // Strict rate limit for quiz submission (Max 10 submissions per minute)
    Route::post('quizzes/{quiz}/attempt', [ParticipantQuizController::class, 'attempt'])
        ->name('quizzes.attempt')
        ->middleware('throttle:10,1');
        
    Route::get('quizzes/{quiz}/result/{attempt}', [ParticipantQuizController::class, 'result'])->name('quizzes.result');
});

require __DIR__.'/auth.php';
