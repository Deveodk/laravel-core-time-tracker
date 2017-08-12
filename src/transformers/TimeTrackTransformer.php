<?php

namespace DeveoDK\CoreTimeTracker\Transformers;

use League\Fractal;

class TimeTrackTransformer extends Fractal\TransformerAbstract
{
    /**
     * @param $data
     * @return array
     */
    public function transform($data)
    {
        return [
            'id' => (int) $data->id,
            'user_agent' => (string) $data->user_agent,
            'time' => (double) $data->time,
            'page' => (string) $data->page,
            'origin' => (string) $data->origin,
            'ip' => (string) $data->ip
        ];
    }
}
