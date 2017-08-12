<?php

namespace DeveoDK\CoreTimeTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\Permission\Traits\HasRoles;

abstract class CoreTimetrack extends Model implements HasMedia
{
    use Notifiable, HasMediaTrait, HasRoles;

    /** @var string */
    protected $guard_name = 'core';

    public function authenticateAttempts()
    {
        return $this->morphMany(JwtAuthenticateAttempt::class, 'authenticable');
    }

    /**
     * Return a collection of blacklisted tokens for model
     * @return MorphMany
     */
    public function blacklists()
    {
        return $this->morphMany(JwtBlacklist::class, 'authenticable');
    }
}
