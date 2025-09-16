<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TokoBot\Helpers\Logger;

class LoggerTest extends TestCase
{
    /** @test */
    public function it_writes_to_the_correct_log_file()
    {
        $logFile = ROOT_PATH . '/logs/test.log';
        $testMessage = 'Hello, this is a test message. ' . uniqid();

        // 1. Cleanup before test
        if (file_exists($logFile)) {
            unlink($logFile);
        }

        // 2. Action: Log a message to a specific channel
        Logger::channel('test')->info($testMessage);

        // 3. Assertion: Check if the file was created and contains the message
        $this->assertFileExists($logFile, 'Log file was not created.');

        $logContent = file_get_contents($logFile);
        $this->assertStringContainsString($testMessage, $logContent, 'Log file does not contain the test message.');

        // 4. Cleanup after test
        unlink($logFile);
    }
}
