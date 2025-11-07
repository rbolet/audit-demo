<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'attribute_name',
        'attribute_description',
        'attribute_type',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'type_attributes')
                    ->withPivot('display_order', 'is_required')
                    ->withTimestamps();
    }

    public function valuesets(): BelongsToMany
    {
        return $this->belongsToMany(Valueset::class, 'attribute_valuesets')
                    ->withTimestamps();
    }

    public function existingAttributeValues(): HasMany
    {
        return $this->hasMany(ExistingAttributeValue::class);
    }
}
