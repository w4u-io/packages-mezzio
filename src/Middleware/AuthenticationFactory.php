<?php

declare(strict_types=1);

namespace Olivier\Mezzio\Middleware;

use Psr\Container\ContainerInterface;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container) : Authentication
    {
        return new Authentication();
    }
}
