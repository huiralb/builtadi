<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesArea extends Model
{
    protected $table = 'sales_areas';
    protected $fillable = [
        'name', 'created_at', 'updated_at'
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'area_id');
    }
}
