<?php

namespace Olivier\Mezzio\Log;

use Psr\Log\LoggerAwareInterface as PsrLoggerAwareInterface;
use Psr\Log\LoggerInterface;

interface LoggerAwareInterface extends PsrLoggerAwareInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();
}
