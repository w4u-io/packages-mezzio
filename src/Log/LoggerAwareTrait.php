<?php

namespace Olivier\Mezzio\Log;

use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    /**
     * Logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * Get a new instance of the default logger.
     *
     * @return Logger
     */
    protected function getDefaultLogger()
    {
        return new Logger(self::class);
    }

    /**
     * {@inheritDoc}
     * @return Logger
     */
    public function getLogger()
    {
        if ($this->logger) {
            return $this->logger;
        }

        $this->logger = $this->getDefaultLogger();

        return $this->logger;
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
