<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValuesetValue extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'valueset_id',
        'value_text',
        'value_description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function valueset(): BelongsTo
    {
        return $this->belongsTo(Valueset::class);
    }
}
