<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'order_id' => $this->id,
            'uuid' => $this->uuid,
            'order_type' => $this->order_type ?? 'sell',
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'delivery_method' => $this->delivery_method ?? 'warehouse',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                ];
            }),
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                    'address' => $this->warehouse->address,
                ];
            }),
            'pickup_address' => $this->pickup_address,
            'pickup_address_note' => $this->pickup_address_note,
            'cancel_reason' => $this->cancel_reason,
            'warehouse_proof' => $this->warehouse_proof,
            'receipt' => new ReceiptResource($this->whenLoaded('receipt')),
        ];
    }
}
