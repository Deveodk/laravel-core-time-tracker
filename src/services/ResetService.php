<?php

namespace DeveoDK\CoreTimeTracker\Services;

use Carbon\Carbon;
use DeveoDK\CoreTimeTracker\events\PasswordReset;
use DeveoDK\CoreTimeTracker\Exceptions\PasswordResetNotCreated;
use DeveoDK\CoreTimeTracker\Models\JwtPasswordReset;
use DeveoDK\CoreTimeTracker\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;

class ResetService extends BaseService
{
    /** @var ChannelManager */
    private $notification;

    /** @var RequestService */
    private $requestService;

    /** @var JwtService */
    private $jwtService;

    /** @var Hasher */
    private $hasher;

    /** @var ApiAuthenticatorService */
    private $apiAuthenticateService;

    /** @var ReflectionService */
    private $reflectionService;

    /**
     * ResetService constructor.
     * @param Dispatcher $dispatcher
     * @param DatabaseManager $database
     * @param OptionService $optionService
     * @param ChannelManager $notification
     * @param RequestService $requestService
     * @param JwtService $jwtService
     * @param ApiAuthenticatorService $apiAuthenticatorService
     * @param Hasher $hasher
     * @param ReflectionService $reflectionService
     */
    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        ChannelManager $notification,
        RequestService $requestService,
        JwtService $jwtService,
        ApiAuthenticatorService $apiAuthenticatorService,
        Hasher $hasher,
        ReflectionService $reflectionService
    ) {
        $this->hasher = $hasher;
        $this->apiAuthenticateService = $apiAuthenticatorService;
        $this->jwtService = $jwtService;
        $this->notification = $notification;
        $this->requestService = $requestService;
        $this->reflectionService = $reflectionService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    /**
     * Validate the token
     *
     * @param $token
     * @return bool
     */
    public function validateToken($token)
    {
        $resetModel = JwtPasswordReset::where('token', '=', $token)->first();

        if (!$resetModel) {
            return false;
        }

        return true;
    }

    /**
     * Reset a password
     * @param $params
     * @return mixed
     */
    public function resetPassword($params)
    {
        $token = $params['token'];
        $password = $params['password'];

        $resetModel = JwtPasswordReset::where('token', '=', $token)->first();

        if (!$resetModel) {
            throw new PasswordResetNotCreated();
        }

        $authenticable = $resetModel->authenticable;

        $authenticable->password = $this->hasher->make($password);
        $authenticable->save();

        $resetModel->reset = Carbon::now()->toDateTimeString();
        $resetModel->save();
        $resetModel->delete();

        return $this->apiAuthenticateService
            ->generateAuthenticateToken($authenticable->id, $resetModel->authenticable_type);
    }

    /**
     * Generate a new password reset link
     *
     * @param $params
     * @return bool
     */
    public function generatePasswordResetLink($params)
    {
        $email = $params['email'];
        $url = $params['url'];

        $model = $this->reflectionService->getModelInstanceFromResponse($params);
        if (!$model) {
            return false;
        }

        $authenticable = (new $model())->where('email', '=', $email)->first();

        if (!$authenticable) {
            $this->dispatcher->fire(new PasswordReset($params['email'], $model));
            throw new PasswordResetNotCreated();
        }

        // Delete old password resets
        $this->deleteOldToken($authenticable->id);

        // Check for throttle
        $this->checkThrottle($params['email'], $model, $authenticable->id);

        $ttlInMinutes = Carbon::now()->addWeeks(2)->diffInMinutes();

        $payload = $this->jwtService->make($authenticable->id, $model, $ttlInMinutes);
        $token = $this->jwtService->encode($payload);

        // Save the magic link to the db
        $this->savePasswordReset($token, $authenticable);

        $magicLink = $url . '?token='.$token;

        $this->notification->send($authenticable, new PasswordResetNotification($magicLink));

        return true;
    }

    /**
     * @param $token
     * @param $authenticable
     */
    public function savePasswordReset($token, $authenticable)
    {
        $magicEntry = new JwtPasswordReset();
        $magicEntry->token = $token;
        $magicEntry->user_agent = $this->requestService->getUserAgent();
        $magicEntry->ip = $this->requestService->getRequestIp();
        $magicEntry->authenticable()->associate($authenticable);
        $magicEntry->save();
    }

    /**
     * Check for throttle
     *
     * @param $email
     * @param $model
     * @param $id
     */
    public function checkThrottle($email, $model, $id)
    {
        $magicCount = JwtPasswordReset::where('authenticable_id', '=', $id)->withTrashed()
            ->where('created_at', '>=', Carbon::now()->subHour(2))->count();

        // if there have been 2 password reset attempt
        if ($magicCount >= 2) {
            throw new PasswordResetNotCreated();
        }
    }

    /**
     * Delete old tokens
     *
     * @param $id
     */
    public function deleteOldToken($id)
    {
        JwtPasswordReset::where('authenticable_id', '=', $id)->delete();
    }
}
