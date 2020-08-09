<?php

namespace Olivier\Mezzio\Middleware;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Laminas\Diactoros\Response\JsonResponse;
use Olivier\Mezzio\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseFormatter implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = null;
        try {
            $response = $handler->handle($request);

            if ($response->getStatusCode() === StatusCode::STATUS_NOT_FOUND) {
                return new JsonResponse([
                    'error' => 'Resource not found'
                ], StatusCode::STATUS_NOT_FOUND);
            }

        } catch (HttpException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'code' => $exception->getErrorCode(),
            ], $exception->getCode());
        } catch (\Throwable $throwable) {
            return new JsonResponse([
                'error' => $throwable->getMessage()
            ], StatusCode::STATUS_INTERNAL_SERVER_ERROR);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage()
            ], StatusCode::STATUS_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
