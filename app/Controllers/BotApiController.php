<?php

namespace TokoBot\Controllers;

use TokoBot\Core\Routing\Route;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use TokoBot\Exceptions\BotNotFoundException;
use Longman\TelegramBot\Exception\TelegramException;
use TokoBot\Helpers\Logger;

class BotApiController extends BaseController
 {
    /**
     * Fetches the bot token from the model.
     * This method is a simple wrapper, allowing for easier extension (e.g., caching) in the future.
     *
     * @param int $botId
     * @return string|null
     */
    private function getBotToken(int $botId): ?string
    {
        return \TokoBot\Models\Bot::findTokenById($botId);
    }

    /**
     * Sends a JSON response and terminates the script.
     *
     * @param array $data
     * @param int $statusCode
     */
    private function sendJsonResponse(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Initializes the Telegram API object for a specific bot.
     *
     * @param int $botId The ID of the bot.
     * @param bool $withUsername Whether to fetch the bot's username.
     * @return Telegram
     * @throws BotNotFoundException
     * @throws TelegramException
     */
    private function initializeTelegram(int $botId, bool $withUsername = false): Telegram
    {
        $token = $this->getBotToken($botId);
        if (!$token) {
            throw new BotNotFoundException('Bot token not found for ID: ' . $botId);
        }

        // The longman/telegram-bot library uses a static Telegram instance.
        // We must first instantiate with the token to make API calls.
        $telegram = new Telegram($token);

        if ($withUsername) {
            $getMeResponse = Request::getMe();
            if (!$getMeResponse->isOk()) {
                throw new TelegramException('Failed to get bot info: ' . $getMeResponse->getDescription());
            }
            $botUsername = $getMeResponse->getResult()->getUsername();
            // Re-initialize to set the username for the static instance.
            return new Telegram($token, $botUsername);
        }

        return $telegram;
    }

    /**
     * A wrapper to execute bot API actions with standardized error handling.
     *
     * @param int $botId
     * @param callable $action The action to perform. It receives the Telegram object.
     * @param bool $withUsername Whether to initialize the Telegram object with the username.
     */
    private function handleBotApiAction(int $botId, callable $action, bool $withUsername = false)
    {
        try {
            $telegram = $this->initializeTelegram($botId, $withUsername);
            $action($telegram);
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramException $e) {
            $context = ['bot_id' => $botId, 'error' => $e->getMessage()];
            Logger::channel('telegram')->error('Telegram API action failed', $context);
            $this->sendJsonResponse(['error' => 'Telegram API Error: ' . $e->getMessage()], 502);
        } catch (\Exception $e) {
            $context = ['bot_id' => $botId, 'error' => $e->getMessage()];
            Logger::channel('app')->error('An unexpected error occurred during a bot API action', $context);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    #[Route('/api/tbot/{id:\d+}/webhook', method: 'GET', middleware: ['AuthMiddleware', ['PermissionMiddleware', 'manage_bots']])]
    public function getWebhookInfo(int $id)
    {
        $this->handleBotApiAction($id, function () {
            $response = Request::getWebhookInfo();

            if (!$response->isOk()) {
                throw new TelegramException($response->getDescription());
            }

            $result = $response->getResult();
            $this->sendJsonResponse($result ? $result->getRawData() : []);
        });
    }

    #[Route('/api/tbot/{id:\d+}/webhook', method: 'POST', middleware: ['AuthMiddleware', ['PermissionMiddleware', 'manage_bots']])]
    public function setWebhook(int $id)
    {
        Logger::channel('app')->info('setWebhook called for bot ID: ' . $id);

        // The webhook URL is now generated on the server for security and consistency.
        // The client no longer needs to send it, making the API more robust.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $webhookUrl = rtrim($protocol . $host, '/') . '/tbot/' . $id . '.php';

        if (!filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            $this->sendJsonResponse(['error' => 'Server could not generate a valid webhook URL.'], 500);
        }

        $this->handleBotApiAction($id, function (Telegram $telegram) use ($webhookUrl, $id) {
            $botUsername = $telegram->getBotUsername();

            $webhookFilePath = PUBLIC_PATH . '/tbot/' . $id . '.php';
            $webhookFileDir = dirname($webhookFilePath);

            if (!is_dir($webhookFileDir)) {
                if (!mkdir($webhookFileDir, 0775, true)) {
                    throw new \Exception('Could not create webhook directory. Check permissions.');
                }
            }

            // Use var_export for safer variable injection into the PHP file content.
            $exportedUsername = var_export($botUsername, true);

            $webhookFileContent = <<<PHP
<?php

require_once dirname(__DIR__, 2) . '/bootstrap/webhook.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use TokoBot\BotHandlers\GenericBotHandler;
use TokoBot\Helpers\Logger;

\$botId = (int) basename(__FILE__, '.php');
\$botToken = \TokoBot\Models\Bot::findTokenById(\$botId);

if (!\$botToken) {
    http_response_code(404);
    Logger::channel('telegram')->error('Webhook call for unknown bot ID or token not found in DB: ' . \$botId);
    exit("Bot configuration not found for ID: " . \$botId);
}

// --- Acknowledge the request to Telegram immediately ---
// This prevents Telegram from re-sending the same update if processing takes a long time.
// It tells Telegram "I got it, thank you!" and then we process the update in the background.
if (function_exists('fastcgi_finish_request')) {
    // This is the cleanest way for PHP-FPM setups (like Nginx).
    http_response_code(200);
    header('Content-Length: 0');
    header('Connection: close');
    fastcgi_finish_request();
} else {
    // Fallback for other server APIs (e.g., Apache with mod_php).
    ignore_user_abort(true);
    set_time_limit(0); // Disable script timeout
    ob_start();
    http_response_code(200);
    header('Content-Length: 0');
    header('Connection: close');
    ob_end_flush();
    flush();
}

try {
    // Create Telegram API object and handle webhook request
    \$telegram = new Telegram(\$botToken, {$exportedUsername});
    Logger::channel('debug')->debug('Bot ID before GenericBotHandler instantiation', ['botToken' => \$botToken, 'type' => \$telegram]);


    // Set custom input to be used by our handler
    \$telegram->setCustomInput(file_get_contents('php://input'));

    // Pass the bot ID and Telegram object to our custom handler
    Logger::channel('app')->debug('Bot ID before GenericBotHandler instantiation', ['botId' => \$botId, 'type' => gettype(\$botId)]);
    \$handler = new GenericBotHandler(\$botId, \$telegram);
    \$handler->handle(); // The library will handle sending the response to Telegram.

} catch (TelegramException \$e) {
    Logger::channel('telegram')->error('Telegram API Exception', ['error' => \$e->getMessage()]);
} catch (\Exception \$e) {
    Logger::channel('app')->error('Generic Webhook Exception', ['error' => \$e->getMessage()]);
}
PHP;

            if (!file_put_contents($webhookFilePath, $webhookFileContent)) {
                throw new \Exception('Could not write webhook file. Check directory permissions.');
            }

            $response = Request::setWebhook(['url' => $webhookUrl]);

            if (!$response->isOk()) {
                // Clean up the created file if setting the webhook fails.
                if (file_exists($webhookFilePath)) {
                    unlink($webhookFilePath);
                }
                throw new TelegramException($response->getDescription());
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook successfully set!']);
        }, true);
    }

    #[Route('/api/tbot/{id:\d+}/webhook', method: 'DELETE', middleware: ['AuthMiddleware', ['PermissionMiddleware', 'manage_bots']])]
    public function deleteWebhook(int $id)
    {
        // @phpstan-ignore closure.unusedUse
        $this->handleBotApiAction($id, function () use ($id) {
            // Delete the webhook and explicitly drop any pending updates.
            $response = Request::deleteWebhook(['drop_pending_updates' => true]);

            if (!$response->isOk()) {
                throw new TelegramException($response->getDescription());
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook successfully deleted!']);
        });
    }
}
