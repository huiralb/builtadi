<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $table = 'sales_orders';
    protected $fillable = [
        'reference_no', 'sales_id', 'customer_id', 'created_at', 'updated_at'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'order_id');
    }
}
