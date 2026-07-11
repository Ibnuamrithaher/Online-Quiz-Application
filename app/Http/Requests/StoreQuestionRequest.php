<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:multiple_choice,essay',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'explanation' => 'nullable|string',
            'points' => 'exclude_if:type,multiple_choice|required_if:type,essay|numeric|min:0',
            'options' => 'exclude_if:type,essay|required_if:type,multiple_choice|array',
            'options.*.content' => 'required_with:options|string',
            'options.*.points' => 'required_with:options|numeric|min:0',
        ];
    }
}
