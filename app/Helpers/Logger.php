<?php

namespace TokoBot\Helpers;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;

class Logger
{
    private static array $instances = [];

    public static function channel(string $name = 'app'): MonologLogger
    {
        if (!isset(self::$instances[$name])) {
            // Define ROOT_PATH if it's not already defined.
            // This makes the Logger usable in different entry points (e.g., webhooks)
            // without needing to load the main bootstrap file.
            if (!defined('ROOT_PATH')) {
                // This assumes Logger.php is in app/Helpers/
                define('ROOT_PATH', dirname(__DIR__, 2));
            }

            // Format log: [timestamp] channel.LEVEL [file:line]: message context extra
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name% [%extra.file%:%extra.line%]: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            );

            // Handler akan menulis log ke file logs/{$name}.log
            $handler = new StreamHandler(\ROOT_PATH . "/logs/{$name}.log", MonologLogger::DEBUG);
            $handler->setFormatter($formatter);

            // Buat logger instance
            self::$instances[$name] = new MonologLogger(strtoupper($name));
            self::$instances[$name]->pushHandler($handler);

            // Tambahkan processor untuk menyertakan file dan baris
            // Ini akan menambahkan 'file', 'line', 'class', dan 'function' ke array 'extra'
            $introspectionProcessor = new IntrospectionProcessor(
                Level::Debug, // Log file/line untuk semua level
                ['TokoBot\\Helpers\\Logger'] // Lewati kelas ini dalam stack trace untuk mendapatkan lokasi pemanggilan yang sebenarnya
            );
            self::$instances[$name]->pushProcessor($introspectionProcessor);
        }

        return self::$instances[$name];
    }
}
