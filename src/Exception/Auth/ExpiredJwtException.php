<?php

namespace Olivier\Mezzio\Exception\Auth;

use Throwable;
use Olivier\Mezzio\Exception\HttpException;

class ExpiredJwtException extends HttpException
{
    const ERROR_CODE = 'JWT_EXPIRED';

    /**
     * MissingJwtException constructor.
     * @param Throwable|null $previous
     */
    public function __construct (?Throwable $previous = null) {
        parent::__construct(
            'Unauthorized',
            401,
            self::ERROR_CODE,
            $previous
        );
    }
}
