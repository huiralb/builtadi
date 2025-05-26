<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalAccessToken extends Model
{
    protected $table = 'personal_access_tokens';
    protected $fillable = [
        // Add actual columns if needed
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tokenable_id');
    }
}
