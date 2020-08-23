<?php

namespace Olivier\Mezzio\Middleware;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Laminas\Diactoros\Response\JsonResponse;
use Olivier\Mezzio\Exception\HttpException;
use Olivier\Mezzio\Log\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseFormatter implements MiddlewareInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = null;
        try {
            $response = $handler->handle($request);

            if ($response->getStatusCode() === StatusCode::STATUS_NOT_FOUND) {
                $this->logger->info('Resource not found', Logger::context(
                    Logger::request($request)
                ));

                return new JsonResponse([
                    'error' => 'Resource not found'
                ], StatusCode::STATUS_NOT_FOUND);
            }

        } catch (HttpException $exception) {
            $this->logger->info('HTTP Exception catch', Logger::context(
                Logger::exception($exception),
                Logger::request($request)
            ));

            return new JsonResponse([
                'message' => $exception->getMessage(),
                'code' => $exception->getErrorCode(),
            ], $exception->getCode());
        } catch (\Exception $exception) {

            $this->logger->error('Exception catch', Logger::context(
                Logger::exception($exception),
                Logger::request($request)
            ));

            return new JsonResponse([
                'error' => $exception->getMessage()
            ], StatusCode::STATUS_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $throwable) {

            $this->logger->error('Throwable catch', Logger::context(
                Logger::exception($throwable),
                Logger::request($request)
            ));

            return new JsonResponse([
                'error' => $throwable->getMessage()
            ], StatusCode::STATUS_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
