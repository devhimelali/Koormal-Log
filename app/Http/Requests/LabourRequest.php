<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabourRequest extends FormRequest
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
            'name' => 'required|string',
            'crew_id' => 'required|exists:crews,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'crew_id.required' => 'The crew field is required.',
            'crew_id.exists' => 'The selected crew is invalid.',
        ];
    }
}
