<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'cancel_reason' => 'required_if:status,cancelled|nullable|string|max:255',
            'proof_image' => 'required_if:status,completed|nullable|string',
        ];
    }
}
