<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|unique:shipments,id',
            'storages_id' => 'required|integer|exists:storages,id',
            'status' => 'required|string|max:45',
            'pickup_date' => 'required|date',
            'received_date' => 'required|date',
            'receipts_id' => 'required|integer|exists:receipts,id|unique:shipments,receipts_id',
        ];
    }
}
