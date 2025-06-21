<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandoverCompletionQuestionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string',
            'sort_by' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'sort_by.required' => 'Sorting order number is required',
            'sort_by.integer' => 'Sorting order number must be an integer',
        ];
    }
}
