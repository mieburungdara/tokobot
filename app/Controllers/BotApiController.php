<?php

namespace TokoBot\Controllers;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use TokoBot\Exceptions\BotNotFoundException;
use Longman\TelegramBot\Exception\TelegramException;
use TokoBot\Helpers\Logger;

class BotApiController extends BaseController
 {
private function getBotToken(int $botId): ?string
{
       return \TokoBot\Models\Bot::findTokenById($botId);
}

private function sendJsonResponse(array $data, int $statusCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

public function getWebhookInfo($id)
{
    try {
        $token = $this->getBotToken($id);
        if (!$token) {
            throw new BotNotFoundException('Bot token not found for ID: ' . $id);
        }

        // Create a temporary Telegram object to make API requests
        $telegram = new Telegram($token);

        // Get bot info to retrieve the username, which is needed for the Telegram object.
        $getMeResponse = Request::getMe();
        if (!$getMeResponse->isOk()) {
            throw new TelegramException('Failed to get bot info: ' . $getMeResponse->getDescription());
        }
        $botUsername = $getMeResponse->getResult()->getUsername();

        // Re-initialize Telegram object with the correct username.
        $telegram = new Telegram($token, $botUsername);
        $response = Request::getWebhookInfo();

        if (!$response->isOk()) {
            throw new TelegramException($response->getDescription());
        }

        $result = $response->getResult(); // This is a WebhookInfo object

        if ($result === null) {
            $this->sendJsonResponse([]);
        } else {
            $this->sendJsonResponse($result->getRawData());
        }
    } catch (BotNotFoundException $e) {
        Logger::channel('telegram')->warning($e->getMessage());
        $this->sendJsonResponse(['error' => $e->getMessage()], 404);
    } catch (TelegramException $e) {
        Logger::channel('telegram')->error('Get webhook info failed', ['bot_id' => $id, 'error' => $e->getMessage()]);
        $this->sendJsonResponse(['error' => $e->getMessage()], 502);
    } catch (\Exception $e) {
        Logger::channel('app')->error('An unexpected error occurred in getWebhookInfo', ['bot_id' => $id, 'error' => $e->getMessage()]);
        $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
    }
}

    function setWebhook($id)
    {
        
        Logger::channel('app')->info('setWebhook called for bot ID: ' . $id);
        $webhookUrl = $_POST['url'] ?? '';
        if (empty($webhookUrl) || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            $this->sendJsonResponse(['error' => 'Invalid or empty URL provided.'], 400);
        }

        try {
            $token = $this->getBotToken($id);
            if (!$token) {
                throw new BotNotFoundException('Bot token not found for ID: ' . $id);
            }

            // Create a temporary Telegram object to make API requests
            $telegram = new Telegram($token);

            // Get bot info to retrieve the username
            $getMeResponse = Request::getMe();
            if (!$getMeResponse->isOk()) {
                throw new TelegramException('Failed to get bot info: ' . $getMeResponse->getDescription());
            }
            $botUsername = $getMeResponse->getResult()->getUsername();

            $webhookFilePath = PUBLIC_PATH . '/tbot/' . $id . '.php';
            $webhookFileDir = dirname($webhookFilePath);

            if (!is_dir($webhookFileDir)) {
                if (!mkdir($webhookFileDir, 0775, true)) {
                    throw new \Exception('Could not create webhook directory. Check permissions.');
                }
            }

            // The new template for `longman/telegram-bot`
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
    \$telegram = new Telegram(\$botToken, '{$botUsername}');
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
                throw new \Exception('Could not write webhook file. Check permissions.');
            }

            $telegram = new Telegram($token, $botUsername);
            $response = Request::setWebhook(['url' => $webhookUrl]);

            if (!$response->isOk()) {
                if (file_exists($webhookFilePath)) {
                    unlink($webhookFilePath);
                }
                throw new TelegramException($response->getDescription());
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook set successfully!']);
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramException $e) {
            Logger::channel('telegram')->error('Set webhook failed', ['bot_id' => $id, 'url' => $webhookUrl, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => $e->getMessage()], 502);
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in setWebhook', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    function deleteWebhook($id)
    {
        try {
            $token = $this->getBotToken($id);
            if (!$token) {
                throw new BotNotFoundException('Bot token not found for ID: ' . $id);
            }

            // Create Telegram API object. The username is not required for this action.
            $telegram = new Telegram($token);
            
            // Delete the webhook and explicitly drop any pending updates.
            // This will solve the issue of the pending count increasing.
            $response = Request::deleteWebhook(['drop_pending_updates' => true]);

            if (!$response->isOk()) {
                throw new TelegramException($response->getDescription());
            }

            $webhookFilePath = PUBLIC_PATH . '/tbot/' . $id . '.php';
            if (file_exists($webhookFilePath)) {
                unlink($webhookFilePath);
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook deleted successfully!']);
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramException $e) {
            Logger::channel('telegram')->error('Delete webhook failed', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => $e->getMessage()], 502);
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in deleteWebhook', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
