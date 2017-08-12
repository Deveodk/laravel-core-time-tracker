<?php

namespace DeveoDK\CoreTimeTracker\Controllers;

use DeveoDK\CoreTimeTracker\Requests\NewTimeTrack;
use DeveoDK\CoreTimeTracker\Services\OptionService;
use DeveoDK\CoreTimeTracker\Services\TimeTrackService;
use Infrastructure\Http\BaseController;

class TimeTrackController extends BaseController
{
    /** @var OptionService */
    private $optionService;

    /** @var TimeTrackService */
    private $timeTrackService;

    public function __construct(
        OptionService $optionService,
        TimeTrackService $timeTrackService
    ) {
        $this->optionService = $optionService;
        $this->timeTrackService = $timeTrackService;
    }

    /**
     * @param NewTimeTrack $request
     * @return void
     */
    public function newTracking(NewTimeTrack $request)
    {
        $tracks = $request->data();

        if (empty($tracks['data'])) {
            return;
        }

        foreach ($tracks['data'] as $track) {
            $this->timeTrackService->createNewTimeTrack($track);
        }
    }
}
