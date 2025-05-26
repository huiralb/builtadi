<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'user_id', 'area_id', 'created_at', 'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(SalesArea::class, 'area_id');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'sales_id');
    }

    public function salesTargets(): HasMany
    {
        return $this->hasMany(SalesTarget::class, 'sales_id');
    }
}
