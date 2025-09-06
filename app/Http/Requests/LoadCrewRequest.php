<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadCrewRequest extends FormRequest
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
            'crew_id' => 'required|exists:crews,id',
            'labours' => 'required|array',
            'labours.*' => 'required|exists:labours,id',
            'shift' => 'required|in:day,night',
            'date' => 'required|date_format:d-m-Y',
        ];
    }

    public function messages(): array
    {
        return [
            'crew_id.required' => 'The crew field is required.',
            'crew_id.exists' => 'The selected crew is invalid.',
            'labours.required' => 'The labours field is required.',
            'labours.array' => 'The labours field must be an array.',
            'labours.*.required' => 'The labours field is required.',
            'labours.*.exists' => 'The selected labour is invalid.',
        ];
    }
}
