<?php

namespace Olivier\Mezzio\Log;

use DateTimeZone;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RedisHandler;
use Redis;
use Psr\Container\ContainerInterface;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container) : Logger
    {
        $channel = 'service-logging';
        $timezone = 'UTC';

        $handlersConfig = null;

        if ($container->has('config')) {
            $config = $container->get('config');
            $logConfig = $config['logs'] ?? null;

            if ($logConfig) {
                $handlersConfig = $logConfig['handlers'] ?? null;
                $channel = $logConfig['channel'];
            }
            $timezone = $config['timezone'];
        }

        if ($handlersConfig === null) {
            $handlersConfig = [
                'stream' => [
                    'resource' => 'php://stdout',
                    'level' => Logger::WARNING
                ]
            ];
        }

        $handlers = $this->getHandlers($handlersConfig);

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
            switch ($handlerName) {
                case 'redis':
                    $handlers[] = $this->getRedisHandler($handlerConfig);
                    break;
                case 'stream':
                    $handlers[] = $this->getStreamHandler($handlerConfig);
                    break;
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

        $redisServer = new Redis();
        $redisServer->connect($redisHost, $redisPort);

        $redisHandler = new RedisHandler($redisServer, $redisKey);
        $redisHandler->setFormatter(new Formatter());

        return $redisHandler;
    }
}
