<?php

namespace DeveoDK\CoreTimeTracker\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Http\BaseController;

class TimeTrackController extends BaseController
{
    /** @var ApiAuthenticatorService */
    private $apiAuthenticatorService;

    /** @var OptionService */
    private $optionService;

    public function __construct(
        ApiAuthenticatorService $apiAuthenticatorService,
        OptionService $optionService
    ) {
        $this->apiAuthenticatorService = $apiAuthenticatorService;
        $this->optionService = $optionService;
        $this->apiAuthenticatorService->setModels($optionService->get('authenticationModels'));
    }

    /**
     * @param AuthAccountsRequest $request
     * @return JsonResponse
     */
    public function newTracking(AuthAccountsRequest $request)
    {
        $data = $this->apiAuthenticatorService->accounts($request->data());

        return response()
            ->json($this->apiAuthenticatorService
                ->setTransformer(new AuthAccountTransformer())->transformCollection($data));
    }

    /**
     * Authenticate the from email and password
     * @param AuthPasswordRequest $request
     * @return JsonResponse
     */
    public function authenticate(AuthPasswordRequest $request)
    {
        $data = $this->apiAuthenticatorService->authenticate($request->data());

        return response()
            ->json($this->apiAuthenticatorService->setTransformer(new AuthorizedTransformer())->transformItem($data))
            ->header('Authorization', $data);
    }

    /**
     * Get the authenticated user
     * @return JsonResponse
     */
    public function authenticatedUser()
    {
        $data = $this->apiAuthenticatorService->getUser();
        return response()
            ->json($this->apiAuthenticatorService->setTransformer(new AuthTransformer())->transformItem($data));
    }

    /**
     * The actual refresh process gets handled in the middleware
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticatedRefresh(Request $request)
    {
        $this->apiAuthenticatorService->refreshToken();
        $token = str_replace("Bearer ", "", $request->header('authorization'));
        $data = $this->apiAuthenticatorService->setTransformer(new AuthRefreshedTransformer())->transformItem($token);

        return response()->json($data)->header('Authorization', $token);
    }

    /**
     * Invalidate a JWT token
     * @param Request $request
     * @return array
     */
    public function authenticatedInvalidate(Request $request)
    {
        $this->apiAuthenticatorService->invalidateToken();
        $token = str_replace("Bearer ", "", $request->header('authorization'));
        $data = $this->apiAuthenticatorService->setTransformer(new AuthInvalidatedTransformer())->transformItem($token);

        return response()->json($data);
    }
}
