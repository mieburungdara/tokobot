<?php

namespace TokoBot\Controllers;

use TelegramBot\Request;
use TelegramBot\Telegram;
use TokoBot\Exceptions\BotNotFoundException;
use TokoBot\Exceptions\TelegramApiException;
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

            new Telegram($token);
            $response = Request::getWebhookInfo();

            if (!$response->isOk()) {
                throw new TelegramApiException($response->getDescription());
            }

            $this->sendJsonResponse($response->getResult());
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramApiException $e) {
            Logger::channel('telegram')->error('Get webhook info failed', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => $e->getMessage()], 502); // 502 Bad Gateway is more appropriate
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in getWebhookInfo', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function setWebhook($id)
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

            // Create the bot's webhook entry file
            $webhookFilePath = PUBLIC_PATH . '/tbot/' . $id . '.php';
            $webhookFileDir = dirname($webhookFilePath);

            // Create the directory if it doesn't exist
            if (!is_dir($webhookFileDir)) {
                if (!mkdir($webhookFileDir, 0775, true)) {
                    throw new \Exception('Could not create webhook directory. Check permissions.');
                }
            }

            $handlerClass = '\TokoBot\BotHandlers\GenericBotHandler';
            $webhookFileContent = <<<PHP
<?php

// This file is a bot-specific entry point for Telegram webhooks.
// It relies on a shared bootstrap file to set up the environment.

// Bootstrap the minimal application environment.
require_once dirname(__DIR__, 2) . '/bootstrap/webhook.php';

// The bot ID for this entry point is derived from the filename.
\$botId = (int) basename(__FILE__, '.php');

// --- Single Source of Truth: Get Token from Database ---
\$botToken = \TokoBot\Models\Bot::findTokenById(\$botId);

// Ensure a token exists for this bot ID.
if (!\$botToken) {
    http_response_code(404);
    // Use the logger if available, otherwise fallback to error_log.
    if (class_exists('\TokoBot\Helpers\Logger')) {
        \TokoBot\Helpers\Logger::channel('telegram')->error('Webhook call for unknown bot ID or token not found in DB: ' . \$botId);
    } else {
        error_log('Webhook call for unknown bot ID or token not found in DB: ' . \$botId);
    }
    echo "Bot configuration not found for ID: " . \$botId;
    exit();
}

// The telegram-bot-php library uses a static context for some operations like getUpdate().
// We must set the token for the current request context.
\TelegramBot\Telegram::setToken(\$botToken);

// Prepare bot configuration for the handler.
\$botConfig = [
    'id' => \$botId,
    'token' => \$botToken,
];

// Instantiate the handler and process the incoming update.
(new {$handlerClass}(\$botConfig))->handle();
PHP;

            Logger::channel('app')->info('Writing webhook file to: ' . $webhookFilePath);

            if (!file_put_contents($webhookFilePath, $webhookFileContent)) {
                throw new \Exception('Could not write webhook file. Check permissions.');
            }

            Logger::channel('app')->info('Successfully wrote webhook file.');

            new Telegram($token);
            $response = Request::setWebhook(['url' => $webhookUrl]);

            if (!$response->isOk()) {
                if (file_exists($webhookFilePath)) {
                    unlink($webhookFilePath);
                }
                throw new TelegramApiException($response->getDescription());
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook set successfully!']);
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramApiException $e) {
            Logger::channel('telegram')->error('Set webhook failed', ['bot_id' => $id, 'url' => $webhookUrl, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => $e->getMessage()], 502);
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in setWebhook', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function deleteWebhook($id)
    {
        try {
            $token = $this->getBotToken($id);
            if (!$token) {
                throw new BotNotFoundException('Bot token not found for ID: ' . $id);
            }

            new Telegram($token);
            $response = Request::deleteWebhook(['drop_pending_updates' => true]);

            if (!$response->isOk()) {
                throw new TelegramApiException($response->getDescription());
            }

            // Delete the bot's webhook entry file
            $webhookFilePath = PUBLIC_PATH . '/tbot/' . $id . '.php';
            if (file_exists($webhookFilePath)) {
                unlink($webhookFilePath);
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook deleted successfully!']);
        } catch (BotNotFoundException $e) {
            Logger::channel('telegram')->warning($e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 404);
        } catch (TelegramApiException $e) {
            Logger::channel('telegram')->error('Delete webhook failed', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => $e->getMessage()], 502);
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in deleteWebhook', ['bot_id' => $id, 'error' => $e->getMessage()]);
            $this->sendJsonResponse(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}