<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Accessor: formatted price with currency symbol
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format((float) $this->price, 2),
        );
    }

    // Mutator: ensure SKU is always stored uppercase
    protected function sku(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query, int $threshold = 10): void
    {
        $query->where('stock_quantity', '<=', $threshold);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)->withPivot('cost_price')->withTimestamps();
    }
}
