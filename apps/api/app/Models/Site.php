<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'site_name',
        'site_address',
        'site_address_2',
        'site_city',
        'site_state',
        'site_postal_code',
        'site_contact_name',
        'site_contact_phone',
        'site_contact_email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the assessments for this site.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
