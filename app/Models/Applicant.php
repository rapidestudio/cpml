<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Applicant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
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
}
