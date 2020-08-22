<?php

namespace Olivier\Mezzio\Log;

use DateTimeZone;
use Redis;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\RedisHandler;

class Logger extends MonologLogger
{
    public function __construct (string $name, $handlers = [], $processors = [], ?DateTimeZone $timezone = null) {

        $redis = new Redis();
        $redis->connect('redis');

        $redisHandler = new RedisHandler($redis, 'services');
        $redisHandler->setFormatter(new Formatter());

        $handlers[] = $redisHandler;

        if (!$timezone) {
            $timezone = new DateTimeZone('Europe/Brussels');
        }

        parent::__construct($name, $handlers, $processors, $timezone);
    }
}
