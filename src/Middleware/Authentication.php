<?php

declare(strict_types=1);

namespace Olivier\Mezzio;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        error_log('JWT: ' . $this->getJwtFromHeader($request));

        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getJwtFromHeader(ServerRequestInterface $request)
    {
        $header = $request->getHeader('authorization');
        return trim((string) preg_replace('/^(?:\s+)?Bearer\s/', '', $header[0]));
    }
}
