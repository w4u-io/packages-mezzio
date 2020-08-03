<?php

namespace Olivier\Mezzio\Exception;

use Exception;
use Throwable;

class HttpException extends Exception
{
    const ERROR_CODE = 'INTERNAL_SERVER_ERROR';


    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * HttpException constructor.
     * @param string $message
     * @param int $statusCode
     * @param string $errorCode
     * @param Throwable|null $previous
     */
    public function __construct($message, int $statusCode = 0, $errorCode = null, ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);

        if ($errorCode !== null) {
            $this->setErrorCode($errorCode);
        }
    }

    /**
     * @return string
     */
    public function getErrorCode (): string {
        return $this->errorCode ?? self::ERROR_CODE;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode (string $errorCode): void {
        $this->errorCode = $errorCode;
    }
}
