<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'site_id',
        'assessment_name',
        'assessment_description',
        'assessment_date',
        'report_date',
        'assigned_to_id',
        'status',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'report_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function existing(): HasMany
    {
        return $this->hasMany(Existing::class);
    }
}
