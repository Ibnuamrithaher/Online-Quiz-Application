<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::select('id', 'title', 'description', 'time_limit', 'is_active', 'created_at');
        if ($request->has('search') && !empty($request->search)) {
            $query->whereFullText('title', $request->search);
        }
        $quizzes = $query->latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(StoreQuizRequest $request)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');
        $quiz = Quiz::create($validated);

        \App\Models\ActivityLog::record('Create Quiz', "Admin created quiz: {$quiz->title} (ID: {$quiz->id})");

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load([
            'questions:id,quiz_id,content,type,category,points,image_path',
            'questions.options:id,question_id,content,points'
        ]);
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');
        $quiz->update($validated);

        \App\Models\ActivityLog::record('Update Quiz', "Admin updated quiz: {$quiz->title} (ID: {$quiz->id})");

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $title = $quiz->title;
        $id = $quiz->id;
        $quiz->delete();

        \App\Models\ActivityLog::record('Delete Quiz', "Admin deleted quiz: {$title} (ID: {$id})");

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully.');
    }
}
