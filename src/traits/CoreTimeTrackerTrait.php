<?php

namespace DeveoDK\CoreTimeTracker\Traits;

use DeveoDK\CoreTimeTracker\Models\CoreTimeTrack;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CoreTimeTrackerTrait
{
    /**
     * Return the related CoreTimeTrack model
     * @return MorphMany
     */
    public function coreTimeTracked()
    {
        return $this->morphMany(CoreTimeTrack::class, 'tracked');
    }
}
