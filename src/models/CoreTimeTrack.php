<?php

namespace DeveoDK\CoreTimeTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CoreTimeTrack extends Model
{
    /**
     * Return the related CoreTimeTrack model
     * @return MorphTo
     */
    public function tracked()
    {
        return $this->morphTo('tracked');
    }
}
