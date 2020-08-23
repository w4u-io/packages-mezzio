<?php

declare(strict_types=1);

namespace Olivier\Mezzio\Middleware;

use Olivier\Mezzio\Log\Logger;
use Psr\Container\ContainerInterface;

class ResponseFormatterFactory
{
    public function __invoke(ContainerInterface $container) : ResponseFormatter
    {
        $logger = null;

        if ($container->has(Logger::class)) {
            $logger = $container->get(Logger::class);
        }

        return new ResponseFormatter($logger);
    }
}
