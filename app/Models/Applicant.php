<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Applicant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
        'checklist' => 'array',
        'height' => 'integer',
        'weight' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(ApplicantWorkExperience::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(ApplicantChild::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(ApplicantEmergencyContact::class);
    }

    public function position(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
