<?php

namespace DeveoDK\CoreTimeTracker\Services;

use DeveoDK\CoreTimeTracker\Models\CoreTimeTrack;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;

class TimeTrackService extends BaseService
{
    /** @var RequestService */
    private $requestService;

    /**
     * TimeTrackService constructor.
     * @param Dispatcher $dispatcher
     * @param DatabaseManager $database
     * @param OptionService $optionService
     */
    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        RequestService $requestService
    ) {
        $this->requestService = $requestService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    public function createNewTimeTrack($tracked)
    {
        if ($tracked['timeOnPage'] === 0.0) {
            return;
        }

        $user = user();

        $timeTrack = new CoreTimeTrack();
        $timeTrack->user_agent = $this->requestService->getUserAgent();
        $timeTrack->ip = $this->requestService->getRequestIp();
        $timeTrack->origin = $this->requestService->getOrigin();
        $timeTrack->page = $tracked['pageName'];
        $timeTrack->time = $tracked['timeOnPage'];
        $timeTrack->tracked_id = $user->id;
        $timeTrack->tracked_type = get_class($user);
        $timeTrack->save();
    }

}
