<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'type_name',
        'type_description',
        'type_category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'type_attributes')
                    ->withPivot('display_order', 'is_required')
                    ->withTimestamps();
    }

    public function existing(): HasMany
    {
        return $this->hasMany(Existing::class);
    }
}
