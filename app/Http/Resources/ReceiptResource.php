<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'date' => $this->date,
            'status' => $this->status,
            'price_received' => $this->price_received,
            'price_owed' => $this->price_owed,
            'edit_confirmed_by_user' => $this->edit_confirmed_by_user ?? 1,
            'accus' => $this->whenLoaded('accus', function () {
                return $this->accus->map(function ($accu) {
                    return [
                        'id' => $accu->id,
                        'name' => $accu->name,
                        'brand' => $accu->brand ?? '-',
                        'amount' => $accu->pivot->amount ?? 0,
                    ];
                });
            }),
            'transfer' => $this->whenLoaded('transfer'),
        ];
    }
}
