<?php

namespace TokoBot\Helpers;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Logger
{
    private static array $instances = [];

    public static function channel(string $name = 'app'): MonologLogger
    {
        if (!isset(self::$instances[$name])) {
            // Format log: [timestamp] channel.LEVEL: message context extra
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            );

            // Handler akan menulis log ke file logs/{$name}.log
            $handler = new StreamHandler(ROOT_PATH . "/logs/{$name}.log", MonologLogger::DEBUG);
            $handler->setFormatter($formatter);

            // Buat logger instance
            self::$instances[$name] = new MonologLogger(strtoupper($name));
            self::$instances[$name]->pushHandler($handler);
        }

        return self::$instances[$name];
    }
}

