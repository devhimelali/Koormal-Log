<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderMoveRequest extends FormRequest
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
            'shift_log_id' => 'required|exists:shift_logs,id',
            'wo_number' => 'required|string',
            'from_date' => 'required|date_format:d-m-Y',
            'from_shift' => 'required|string|in:day,night',
            'to_date' => 'required|date_format:d-m-Y',
            'to_shift' => 'required|string|in:day,night',
            'reason' => 'required|string',
        ];
    }
}
