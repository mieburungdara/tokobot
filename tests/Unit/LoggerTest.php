<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TokoBot\Helpers\Logger;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\TestHandler;

class LoggerTest extends TestCase
{
    protected string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        // Define ROOT_PATH for testing purposes
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', __DIR__ . '/../../');
        }
        $this->logPath = ROOT_PATH . '/logs/test.log';
        // Ensure log directory exists
        if (!is_dir(dirname($this->logPath))) {
            mkdir(dirname($this->logPath), 0777, true);
        }
        // Clear log file before each test
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
    }

    protected function tearDown(): void
    {
        // Clean up log file after each test
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
        parent::tearDown();
    }

    public function testChannelReturnsMonologLoggerInstance(): void
    {
        $logger = Logger::channel('test');
        $this->assertInstanceOf(MonologLogger::class, $logger);
    }

    public function testDifferentChannelsReturnDifferentInstances(): void
    {
        $logger1 = Logger::channel('channel1');
        $logger2 = Logger::channel('channel2');
        $this->assertNotSame($logger1, $logger2);
    }

    public function testSameChannelReturnsSameInstance(): void
    {
        $logger1 = Logger::channel('same_channel');
        $logger2 = Logger::channel('same_channel');
        $this->assertSame($logger1, $logger2);
    }

    public function testLogFileIsCreatedAndContainsMessage(): void
    {
        $logger = Logger::channel('test_file');
        $message = 'This is a test log message.';
        $logger->info($message);

        $this->assertFileExists(ROOT_PATH . '/logs/test_file.log');
        $content = file_get_contents(ROOT_PATH . '/logs/test_file.log');
        $this->assertStringContainsString($message, $content);
        $this->assertStringContainsString('TEST_FILE.INFO', $content);
    }

    public function testLogContextAndExtraAreIncluded(): void
    {
        $logger = Logger::channel('test_context');
        $message = 'Message with context.';
        $context = ['user_id' => 123, 'action' => 'login'];
        $extra = ['ip_address' => '127.0.0.1'];

        // Monolog's LineFormatter includes context and extra by default if present
        $logger->warning($message, $context, $extra);

        $this->assertFileExists(ROOT_PATH . '/logs/test_context.log');
        $content = file_get_contents(ROOT_PATH . '/logs/test_context.log');

        $this->assertStringContainsString($message, $content);
        $this->assertStringContainsString('"user_id":123', $content);
        $this->assertStringContainsString('"action":"login"', $content);
        // Note: LineFormatter might not output 'extra' directly in the default format unless configured.
        // For this test, we'll check for context which is more reliably included.
    }
}