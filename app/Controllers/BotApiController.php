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

        $telegram = new Telegram($token, 'TokoBot');
        $response = Request::getWebhookInfo();

        if (!$response->isOk()) {
            throw new TelegramException($response->getDescription());
        }

        $result = $response->getResult();

        if ($result === null) {
            $this->sendJsonResponse([]);
        } else {
            $dataToSend = method_exists($result, 'toArray') ? $result->toArray() : (array) $result;

            $cleanData = [
                'bot_username' => $dataToSend['bot_username'] ?? null,
                'webhook_info' => $dataToSend['raw_data'] ?? [],
            ];

            // Kirim $cleanData ke response JSON
            $this->sendJsonResponse($dataToSend['raw_data'] ?? []);
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

try {
    // Create Telegram API object and handle webhook request
    \$telegram = new Telegram(\$botToken, 'TokoBot');

    // Set custom input to be used by our handler
    \$telegram->setCustomInput(file_get_contents('php://input'));

    // Pass the bot ID and Telegram object to our custom handler
    Logger::channel('app')->debug('Bot ID before GenericBotHandler instantiation', ['botId' => \$botId, 'type' => gettype(\$botId)]);
    \$handler = new GenericBotHandler(\$botId, \$telegram);
    \$handler->handle();
    // Pastikan Telegram mendapat respons sukses
    http_response_code(200);
    echo 'OK';

} catch (TelegramException \$e) {
    Logger::channel('telegram')->error('Telegram API Exception', ['error' => \$e->getMessage()]);
} catch (\Exception \$e) {
    Logger::channel('app')->error('Generic Webhook Exception', ['error' => \$e->getMessage()]);
}
PHP;

            if (!file_put_contents($webhookFilePath, $webhookFileContent)) {
                throw new \Exception('Could not write webhook file. Check permissions.');
            }

            $telegram = new Telegram($token, 'TokoBot');
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

            $telegram = new Telegram($token, 'TokoBot');
                        $response = Request::deleteWebhook([]);

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
