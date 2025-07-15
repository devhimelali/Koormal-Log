<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyLabourSupervisorRequest extends FormRequest
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
            'shift' => 'required|in:day,night',
            'copy_for' => 'required|in:supervisor,labour,both',
            'copy_days_date' => 'required|date_format:d-m-Y',
            'end_date' => 'required|date_format:d-m-Y|after:copy_days_date',
            'copy_form_date' => 'required|date_format:d-m-Y',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string> Array of attribute-specific error messages.
     */

    public function messages(): array
    {
        return [
            'shift.required' => 'Please select a shift.',
            'copy_for.required' => 'Please choose the assignment type.',
            'copy_days_date.required' => 'Please select a start date.',
            'end_date.required' => 'Please select an end date.',
        ];
    }
}
