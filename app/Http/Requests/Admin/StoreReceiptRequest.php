<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|unique:receipts,id',
            'receipt_number' => 'required|string|max:45|unique:receipts,receipt_number',
            'date' => 'required|date',
            'status' => 'required|string|max:45',
            'price_received' => 'required|integer',
            'price_owed' => 'nullable|integer',
            'users_id' => 'required|integer|exists:users,id',
            'orders_id' => 'required|integer|exists:orders,id|unique:receipts,orders_id',
            'accus' => 'required|array|min:1',
            'accus.*.id' => 'required|integer|exists:accus,id',
            'accus.*.amount' => 'required|integer|min:1',
        ];
    }
}
