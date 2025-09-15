<?php

namespace TokoBot\Controllers;

use TelegramBot\Request;
use TelegramBot\Telegram;

class BotApiController extends BaseController
{
    private function getBotToken(int $botId): ?string
    {
        $botsFile = CONFIG_PATH . '/bots.php';
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
                throw new \Exception('Bot token not found.');
            }

            new Telegram($token);
            $response = Request::getWebhookInfo();

            if (!$response->isOk()) {
                throw new \Exception($response->getDescription());
            }

            $this->sendJsonResponse($response->getResult());
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function setWebhook($id)
    {
        $webhookUrl = $_POST['url'] ?? '';
        if (empty($webhookUrl) || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            $this->sendJsonResponse(['error' => 'Invalid or empty URL provided.'], 400);
        }

        try {
            $token = $this->getBotToken($id);
            if (!$token) {
                throw new \Exception('Bot token not found.');
            }

            // Create the bot's webhook entry file
            $webhookFilePath = PUBLIC_PATH . '/bots/' . $id . '.php';
            $handlerClass = '\TokoBot\BotHandlers\GenericBotHandler'; // Example handler
            $webhookFileContent = "<?php\nrequire_once __DIR__ . '/../../vendor/autoload.php';\n\n// Entry point for bot ID: $id\n\$botConfig = [\'id\' => $id];\n\n(new {$handlerClass}(\$botConfig))->handle();\n";
            
            if (!file_put_contents($webhookFilePath, $webhookFileContent)){
                throw new \Exception('Could not write webhook file. Check permissions.');
            }

            new Telegram($token);
            $response = Request::setWebhook(['url' => $webhookUrl]);

            if (!$response->isOk()) {
                if (file_exists($webhookFilePath)) {
                    unlink($webhookFilePath);
                }
                throw new \Exception($response->getDescription());
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook set successfully!']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteWebhook($id)
    {
        try {
            $token = $this->getBotToken($id);
            if (!$token) {
                throw new \Exception('Bot token not found.');
            }

            new Telegram($token);
            $response = Request::deleteWebhook();

            if (!$response->isOk()) {
                throw new \Exception($response->getDescription());
            }

            // Delete the bot's webhook entry file
            $webhookFilePath = PUBLIC_PATH . '/bots/' . $id . '.php';
            if (file_exists($webhookFilePath)) {
                unlink($webhookFilePath);
            }

            $this->sendJsonResponse(['success' => true, 'message' => 'Webhook deleted successfully!']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
