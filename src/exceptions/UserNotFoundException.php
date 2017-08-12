<?php

namespace DeveoDK\CoreTimeTracker\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, trans('apiAuth.exceptions.userNotFound'));
    }
}
