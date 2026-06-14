<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'address'    => $this->address,
            'costPrice' => $this->whenPivotLoaded('product_supplier', fn () => $this->pivot->cost_price),
            'createdAt' => $this->created_at->toIso8601String(),
        ];
    }
}
