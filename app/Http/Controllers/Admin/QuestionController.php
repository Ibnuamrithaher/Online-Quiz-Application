<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz)
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question = $quiz->questions()->create([
            'type' => $validated['type'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'Umum',
            'image_path' => $imagePath,
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'] ?? null,
        ]);

        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            foreach ($validated['options'] as $optionData) {
                $question->options()->create([
                    'content' => $optionData['content'],
                    'points' => isset($optionData['points']) ? $optionData['points'] : 0,
                ]);
            }
        }

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Question added successfully.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        $question->load('options:id,question_id,content,points');
        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question)
    {
        $validated = $request->validated();

        $imagePath = $question->image_path;
        
        if ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        } elseif ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question->update([
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'Umum',
            'image_path' => $imagePath,
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'] ?? null,
        ]);

        if ($question->type === 'multiple_choice' && isset($validated['options'])) {
            $existingOptionIds = [];
            foreach ($validated['options'] as $optionData) {
                if (isset($optionData['id'])) {
                    $option = QuestionOption::find($optionData['id']);
                    if ($option && $option->question_id == $question->id) {
                        $option->update([
                            'content' => $optionData['content'],
                            'points' => isset($optionData['points']) ? $optionData['points'] : 0,
                        ]);
                        $existingOptionIds[] = $option->id;
                    }
                } else {
                    $newOption = $question->options()->create([
                        'content' => $optionData['content'],
                        'points' => isset($optionData['points']) ? $optionData['points'] : 0,
                    ]);
                    $existingOptionIds[] = $newOption->id;
                }
            }
            // Delete removed options
            $question->options()->whereNotIn('id', $existingOptionIds)->delete();
        }

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Question updated successfully.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }
        $question->delete();
        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Question deleted successfully.');
    }
}
