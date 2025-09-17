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
        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];
        return $botTokens[$botId] ?? null;
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

            $handlerClass = '\TokoBot\BotHandlers\GenericBotHandler'; // Handler default
            $webhookFileContent = <<<PHP
<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Definisikan konstanta path jika belum ada
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . '/../../config');
}

// Ambil Bot ID dari nama file
\$botId = (int) basename(__FILE__, '.php');

// Muat semua token bot
\$botTokens = require CONFIG_PATH . '/tbots.php';

// Periksa apakah token untuk bot ini ada
if (!isset(\$botTokens[\$botId])) {
    http_response_code(404);
    echo "Bot configuration not found for ID: " . \$botId;
    exit();
}

// Siapkan konfigurasi untuk handler
\$botConfig = [
    'id' => \$botId,
    'token' => \$botTokens[\$botId],
];

(new {$handlerClass}(\$botConfig))->handle();
PHP;


            Logger::channel('app')->info('Writing webhook file to: ' . $webhookFilePath);
            Logger::channel('app')->info('Webhook file content: ' . $webhookFileContent);

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