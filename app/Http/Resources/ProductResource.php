<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'sku'             => $this->sku,
            'description'     => $this->description,
            'price'           => $this->price,
            'formattedPrice' => $this->formatted_price,
            'stockQuantity'  => $this->stock_quantity,
            'isActive'       => $this->is_active,
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'suppliers'       => SupplierResource::collection($this->whenLoaded('suppliers')),
            'createdAt'      => $this->created_at->toIso8601String(),
            'updatedAt'      => $this->updated_at->toIso8601String(),
            'deletedAt'      => $this->when($this->deleted_at, fn () => $this->deleted_at->toIso8601String()),
        ];
    }
}
