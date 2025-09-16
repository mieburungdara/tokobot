<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use TokoBot\Exceptions\TelegramApiException;
use TokoBot\Exceptions\DatabaseException;

class AdminController extends DashmixController
{
    public function index()
    {
        $this->dashmixDashboard();
    }

    public function dashmixDashboard()
    {
        // Widget Data: Bot Status
        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT count(*) FROM bots");
        $totalBots = $stmt->fetchColumn();

        $botsFile = CONFIG_PATH . '/bots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];
        $botsWithToken = count($botTokens);

        // Widget Data: Critical Logs
        $logFile = ROOT_PATH . '/logs/critical.log';
        $criticalLogs = [];
        if (file_exists($logFile)) {
            $fileContent = file_get_contents($logFile);
            $lines = explode("\n", trim($fileContent));
            $criticalLogs = array_slice(array_reverse($lines), 0, 5); // Get latest 5 lines
        }

        $viewData = [
            'totalBots' => $totalBots,
            'botsWithToken' => $botsWithToken,
            'criticalLogs' => $criticalLogs
        ];

        $breadcrumbs = [
            ['name' => 'Dashboard']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/dashboard.php',
            'Admin Dashboard',
            'Welcome to the admin dashboard.',
            [],
            $breadcrumbs,
            $viewData
        );
    }

    public function users()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Users']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT telegram_id, username, first_name, role FROM users ORDER BY first_name ASC");
        $users = $stmt->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/users.php',
            'User Management',
            'Manage all application users.',
            [],
            $breadcrumbs,
            ['users' => $users]
        );
    }

    public function settings()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Settings']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/settings.php',
            'Application Settings',
            'Configure application settings.',
            [],
            $breadcrumbs
        );
    }

    public function reports()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Reports']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/reports.php',
            'Reports',
            'View application reports.',
            [],
            $breadcrumbs
        );
    }

    public function viewLogs()
    {
        $logChannel = $_GET['log'] ?? 'app';
        $allowedLogs = ['app', 'telegram', 'critical'];

        if (!in_array($logChannel, $allowedLogs)) {
            $logChannel = 'app';
        }

        $logFile = ROOT_PATH . "/logs/{$logChannel}.log";

        if (isset($_GET['action']) && $_GET['action'] === 'clear') {
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
            header("Location: /logs?log={$logChannel}");
            exit;
        }

        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Log Viewer']
        ];

        $logs = [];

        if (file_exists($logFile)) {
            $fileContent = file_get_contents($logFile);
            $logs = explode("\n", trim($fileContent));
            $logs = array_reverse($logs);
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/logs.php',
            'Log Viewer',
            'View application logs.',
            [],
            $breadcrumbs,
            ['logs' => $logs]
        );
    }

    public function manageBots()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Bot Management']
        ];

        // Load bots from the database
        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM bots ORDER BY first_name ASC");
        $botsFromDb = $stmt->fetchAll();

        // Load tokens from the config file
        $botsFile = CONFIG_PATH . '/bots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];

        // Check token status for each bot
        $bots = array_map(function ($bot) use ($botTokens) {
            $bot['has_token'] = isset($botTokens[$bot['id']]);
            return $bot;
        }, $botsFromDb);

        $this->renderDashmix(
            VIEWS_PATH . '/admin/bots.php',
            'Bot Management',
            'Manage your Telegram bots.',
            [],
            $breadcrumbs,
            ['bots' => $bots] // Pass bots data to the view
        );
    }

    public function addBot()
    {
        $token = $_POST['token'] ?? '';

        if (empty($token)) {
            \TokoBot\Helpers\Session::flash('error_message', 'Token cannot be empty.');
            header('Location: /tbot');
            exit();
        }

        try {
            // Use the provided token for this request
            new \TelegramBot\Telegram($token);
            $response = \TelegramBot\Request::getMe();

            if ($response->isOk()) {
                $botInfo = $response->getResult(); // This is an array
                $botId = $botInfo['id'];

                // --- Save public info to DB ---
                $pdo = \TokoBot\Helpers\Database::getInstance();
                $sql = "INSERT INTO bots (id, username, first_name, is_bot) VALUES (?, ?, ?, ?) "
                     . "ON DUPLICATE KEY UPDATE username = VALUES(username), first_name = VALUES(first_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $botId,
                    $botInfo['username'],
                    $botInfo['first_name'],
                    $botInfo['is_bot']
                ]);

                // --- Save token to config file ---
                $botsFile = CONFIG_PATH . '/bots.php';
                $botTokens = file_exists($botsFile) ? require $botsFile : [];
                $botTokens[$botId] = $token;

                $fileContent = "<?php\n\n// Bot token configuration file\n" \
                             . "// Maps Bot ID to its secret token.\nreturn " \
                             . var_export($botTokens, true) . ";\n";

                if (file_put_contents($botsFile, $fileContent) === false) {
                    throw new \Exception(
                        'Failed to write to token config file. Please check file permissions for config/bots.php.'
                    );
                }

                $successMessage = 'Bot "' . $botInfo['first_name'] . '" has been added/updated successfully!';
                \TokoBot\Helpers\Session::flash('success_message', $successMessage);
            } else {
                throw new TelegramApiException('Invalid token: ' . $response->getDescription());
            }
        } catch (TelegramApiException $e) {
            Logger::channel('telegram')->error('Failed to add bot', ['error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to add bot: ' . $e->getMessage());
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in addBot', ['error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'An unexpected error occurred.');
        }

        header('Location: /tbot');
        exit();
    }

    public function deleteBot($id)
    {
        try {
            // --- Delete from DB ---
            try {
                $pdo = \TokoBot\Helpers\Database::getInstance();
                $stmt = $pdo->prepare("DELETE FROM bots WHERE id = ?");
                $stmt->execute([$id]);
            } catch (\PDOException $e) {
                throw new DatabaseException('Failed to delete bot from database.', (int)$e->getCode(), $e);
            }

            // --- Delete token from config file ---
            $botsFile = CONFIG_PATH . '/bots.php';
            $botTokens = file_exists($botsFile) ? require $botsFile : [];
            if (isset($botTokens[$id])) {
                unset($botTokens[$id]);
                $fileContent = "<?php\n\n// Bot token configuration file\n" \
                             . "// Maps Bot ID to its secret token.\nreturn " \
                             . var_export($botTokens, true) . ";\n";
                if (file_put_contents($botsFile, $fileContent) === false) {
                    // Log this but don't throw, as the main DB operation succeeded
                    Logger::channel('app')->error('Failed to write to token config file during bot deletion.', ['bot_id' => $id]);
                }
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($botsFile);
                }
            }

            // --- Delete webhook file ---
            $webhookFile = PUBLIC_PATH . '/bots/' . $id . '.php';
            if (file_exists($webhookFile)) {
                unlink($webhookFile);
            }

            \TokoBot\Helpers\Session::flash('success_message', 'Bot has been deleted successfully!');
        } catch (DatabaseException $e) {
            Logger::channel('database')->error('Failed to delete bot', ['bot_id' => $id, 'error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to delete bot.');
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in deleteBot', ['bot_id' => $id, 'error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'An unexpected error occurred.');
        }

        header('Location: /tbot');
        exit();
    }
}
