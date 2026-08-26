<?php

namespace App\Http\Resources;

use App\Helpers\DataMasker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        $shouldMask = $request->header('X-View-Mode') !== 'unmasked';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'address_note' => $this->address_note,
            'ktp' => $this->ktp,
            'account_name' => $shouldMask ? DataMasker::accountName($this->account_name) : $this->account_name,
            'account_number' => $shouldMask ? DataMasker::accountNumber($this->account_number) : $this->account_number,
            'phone_number' => $shouldMask ? DataMasker::phone($this->phone_number) : $this->phone_number,
            'bank' => $this->whenLoaded('bank', function () {
                return [
                    'id' => $this->bank->id,
                    'name' => $this->bank->name,
                ];
            }),
        ];
    }
}
