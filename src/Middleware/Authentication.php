<?php

declare(strict_types=1);

namespace Olivier\Mezzio\Middleware;

use Olivier\Mezzio\Exception\Auth\InvalidJwtException;
use Olivier\Mezzio\Exception\Auth\MissingJwtException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface
{
//    protected $encryptionKey;
//
//    public function __construct($encryptionKey)
//    {
//        $this->encryptionKey = $encryptionKey;
//    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws InvalidJwtException
     * @throws MissingJwtException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwt = $this->getJwtFromHeader($request);

        if ($jwt === '') {
            throw new MissingJwtException();
        }

        $token = $this->getTokenFromJwt($jwt);

        if ($this->isTokenValid($token) === false) {
            throw new InvalidJwtException();
        }

        return $handler->handle($request->withAttribute('auth', $token->getClaims()));
    }

    /**
     * @param Token $token
     * @return boolean
     */
    protected function isTokenValid($token)
    {
        $encryptionKey = getenv('OAUTH_ENCRYPTION_KEY');
        $signer = new Sha512();
        return $token->verify($signer, $encryptionKey);
    }

    /**
     * @param string $jwt
     * @return Token
     */
    protected function getTokenFromJwt($jwt)
    {
        $parser = new Parser();
        return $parser->parse($jwt);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getJwtFromHeader(ServerRequestInterface $request)
    {
        $authorizationHeader = $request->getHeaderLine('authorization');
        return trim((string) preg_replace('/^(?:\s+)?Bearer\s/', '', $authorizationHeader));
    }
}
