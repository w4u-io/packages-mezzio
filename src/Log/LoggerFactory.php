<?php

namespace Olivier\Mezzio\Log;

use DateTimeZone;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RedisHandler;
use Redis;
use RedisException;
use Psr\Container\ContainerInterface;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container) : Logger
    {
        $channel = 'service-logging';
        $timezone = 'UTC';

        $handlers = null;
        $handlersConfig = null;

        if ($container->has('config')) {
            $config = $container->get('config');
            $logConfig = $config['logs'] ?? null;

            if ($logConfig) {
                $handlersConfig = $logConfig['handlers'] ?? null;
                $channel = $logConfig['channel'];
            }

            if (isset($config['timezone'])) {
                $timezone = $config['timezone'];
            }

            if ($handlersConfig !== null) {
                $handlers = $this->getHandlers($handlersConfig);
            }
        }

        if ($handlers === null) {
            $handlersConfig = [
                'stream' => [
                    'resource' => 'php://stdout',
                    'level' => Logger::WARNING
                ]
            ];
            $handlers = $this->getHandlers($handlersConfig);
        }

        $timezone = new DateTimeZone($timezone);

        return new Logger($channel, $handlers, [], $timezone);
    }

    /**
     * @param array $config
     * @return AbstractProcessingHandler[]
     */
    private function getHandlers(array $config)
    {
        $handlers = [];
        foreach ($config as $handlerName => $handlerConfig) {
            $handler = null;
            switch ($handlerName) {
                case 'redis':
                    $handler = $this->getRedisHandler($handlerConfig);
                    break;
                case 'stream':
                    $handler = $this->getStreamHandler($handlerConfig);
                    break;
            }
            if ($handler !== null) {
                $handlers[] = $handler;
            }
        }
        return $handlers;
    }

    /**
     * @param array $config
     * @return StreamHandler
     */
    private function getStreamHandler(array $config)
    {
        $resource = $config['resource'] ?? 'php://stdout';
        $level = $config['level'] ?? Logger::WARNING;
        return new StreamHandler($resource, $level);
    }

    /**
     * @param array $config
     * @return RedisHandler
     */
    private function getRedisHandler(array $config)
    {
        $redisHost = $config['host'] ?? 'redis';
        $redisKey = $config['key'] ?? 'services';
        $redisPort = $config['port'] ?? 6379;

        try {
            $redisServer = new Redis();
            $redisServer->connect($redisHost, $redisPort);

            $redisHandler = new RedisHandler($redisServer, $redisKey);
            $redisHandler->setFormatter(new Formatter());

            return $redisHandler;
        } catch (RedisException $exception) {
            error_log('Could not connect to Redis server: ' . $redisHost . ' - Exception: ' . $exception->getMessage());
            return null;
        }
    }
}
