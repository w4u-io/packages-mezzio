<?php

namespace Olivier\Mezzio\Log;

use Exception;
use Monolog\Logger as MonologLogger;
use Psr\Http\Message\ServerRequestInterface;

class Logger extends MonologLogger
{
    public static function context(...$args)
    {
        $context = [];

        if ($args && is_array($args)) {
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    foreach ($arg as $key => $value) {
                        if (!isset($context[$key])) {
                            $context[$key] = $value;
                        } else {
                            $old = $context[$key];
                            $context[$key] = array_merge($old, $value);
                        }
                    }
                }
            }
        }

        return $context;
    }

    /**
     * @param $name
     * @param $value
     * @return array
     */
    public static function data($name, $value)
    {
        return [
            'data' => [
                [
                    'name' => $name,
                    'value' => $value,
                ]
            ]
        ];
    }

    /**
     * @param string $tag
     * @return array
     */
    public static function tag($tag)
    {
        return [
            'tags' => [
                [
                    $tag
                ]
            ]
        ];
    }

    public static function request(ServerRequestInterface $request)
    {
        return array(
            'request' => array(
                'uri' => $request->getUri(),
                'headers' => $request->getHeaders(),
                'method' => $request->getMethod(),
                'parsedBody' => $request->getParsedBody(),
            ),
        );
    }

    public static function exception(Exception $exception)
    {
        $previous = null;
        if ($exception->getPrevious()) {
            $previous = [];
            self::getPreviousExceptions($exception, $previous);
        }

        return array(
            'exception' => array(
                'message' => $exception->getMessage(),
                'class' => get_class($exception),
                'code' => $exception->getCode(),
                'file' => $exception->getFile() . ':' . $exception->getLine(),
                'previous' => $previous,
            ),
        );
    }

    /**
     * @param Exception $exception
     * @param array $trace
     */
    private static function getPreviousExceptions(Exception $exception, & $trace)
    {
        if ($exception->getPrevious()) {
            $trace[] = array(
                'message' => $exception->getPrevious()->getMessage(),
                'class' => get_class($exception->getPrevious()),
                'code' => $exception->getPrevious()->getCode(),
                'file' => $exception->getPrevious()->getFile() . ':' . $exception->getPrevious()->getLine(),
            );

            if ($exception->getPrevious()->getPrevious()) {
                self::getPreviousExceptions($exception->getPrevious(), $trace);
            }
        }
    }
}
