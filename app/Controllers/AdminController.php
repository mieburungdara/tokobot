<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use TokoBot\Exceptions\DatabaseException;
use Symfony\Component\Process\Process;

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
        $stmt = $pdo->query("SELECT count(*) FROM tbots");
        $totalBots = $stmt->fetchColumn();

        $botsFile = CONFIG_PATH . '/tbots.php';
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

    public function botAnalytics()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Bot Analytics']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();

        // Command Usage
        $commandUsageStmt = $pdo->query("SELECT text, count(*) as command_count FROM messages WHERE text LIKE '/%' GROUP BY text ORDER BY command_count DESC LIMIT 10");
        $commandUsage = $commandUsageStmt->fetchAll();

        // Active Users
        $activeUsersStmt = $pdo->query("SELECT username, first_name, last_activity_at FROM users WHERE last_activity_at IS NOT NULL ORDER BY last_activity_at DESC LIMIT 10");
        $activeUsers = $activeUsersStmt->fetchAll();

        // Bot Errors
        $appLogFile = ROOT_PATH . '/logs/app.log';
        $telegramLogFile = ROOT_PATH . '/logs/telegram.log';
        $appLogs = file_exists($appLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($appLogFile)))), 0, 5) : [];
        $telegramLogs = file_exists($telegramLogFile) ? array_slice(array_reverse(explode("\n", trim(file_get_contents($telegramLogFile)))), 0, 5) : [];

        $viewData = [
            'commandUsage' => $commandUsage,
            'activeUsers' => $activeUsers,
            'appLogs' => $appLogs,
            'telegramLogs' => $telegramLogs,
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/analytics.php',
            'Bot Analytics',
            'Analyze bot usage and activity.',
            [],
            $breadcrumbs,
            $viewData
        );
    }

    public function viewLogs()
    {
        $logChannel = $_GET['log'] ?? 'app';
        $allowedLogs = ['app', 'telegram', 'critical', 'tbot_error','auth','database'];

        if (!in_array($logChannel, $allowedLogs)) {
            $logChannel = 'app';
        }

        if ($logChannel === 'tbot_error') {
            $logFile = PUBLIC_PATH . '/tbot/error_log.txt';
        } else {
            $logFile = ROOT_PATH . "/logs/{$logChannel}.log";
        }

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
            [
                'logs' => $logs,
                'logChannel' => $logChannel,
                'allowedLogs' => $allowedLogs,
            ]
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
        $stmt = $pdo->query("SELECT id, username, first_name, token IS NOT NULL as has_token FROM tbots ORDER BY first_name ASC");
        $tbots = $stmt->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/tbots.php',
            'Bot Management',
            'Manage your Telegram bots.',
            [],
            $breadcrumbs,
            ['bots' => $tbots] // Pass bots data to the view
        );
    }

    public function storageChannels()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT id, bot_id, channel_id, last_used_at FROM bot_storage_channels ORDER BY bot_id ASC, id ASC");
        $storageChannels = $stmt->fetchAll();

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channels.php',
            'Storage Channel Management',
            'Manage Telegram storage channels for bots.',
            [],
            $breadcrumbs,
            ['storageChannels' => $storageChannels]
        );
    }

    public function addStorageChannel()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Add']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->query("SELECT id, username FROM tbots ORDER BY username ASC");
        $bots = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                \TokoBot\Helpers\Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/add');
                exit();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO bot_storage_channels (bot_id, channel_id) VALUES (?, ?)");
                $stmt->execute([$botId, $channelId]);
                \TokoBot\Helpers\Session::flash('success_message', 'Storage channel added successfully!');
            } catch (\PDOException $e) {
                \TokoBot\Helpers\Session::flash('error_message', 'Failed to add storage channel: ' . $e->getMessage());
            }

            header('Location: /storage-channels');
            exit();
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channel_form.php',
            'Add Storage Channel',
            'Add a new Telegram storage channel.',
            [],
            $breadcrumbs,
            ['bots' => $bots]
        );
    }

    public function editStorageChannel($id)
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Storage Channels', 'url' => '/storage-channels'],
            ['name' => 'Edit']
        ];

        $pdo = \TokoBot\Helpers\Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, bot_id, channel_id FROM bot_storage_channels WHERE id = ?");
        $stmt->execute([$id]);
        $channel = $stmt->fetch();

        if (!$channel) {
            \TokoBot\Helpers\Session::flash('error_message', 'Storage channel not found.');
            header('Location: /storage-channels');
            exit();
        }

        $stmt = $pdo->query("SELECT id, username FROM tbots ORDER BY username ASC");
        $bots = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $botId = $_POST['bot_id'] ?? null;
            $channelId = $_POST['channel_id'] ?? null;

            if (empty($botId) || empty($channelId)) {
                \TokoBot\Helpers\Session::flash('error_message', 'Bot ID and Channel ID cannot be empty.');
                header('Location: /storage-channels/edit/' . $id);
                exit();
            }

            try {
                $stmt = $pdo->prepare("UPDATE bot_storage_channels SET bot_id = ?, channel_id = ? WHERE id = ?");
                $stmt->execute([$botId, $channelId, $id]);
                \TokoBot\Helpers\Session::flash('success_message', 'Storage channel updated successfully!');
            } catch (\PDOException $e) {
                \TokoBot\Helpers\Session::flash('error_message', 'Failed to update storage channel: ' . $e->getMessage());
            }

            header('Location: /storage-channels');
            exit();
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/storage_channel_form.php',
            'Edit Storage Channel',
            'Edit an existing Telegram storage channel.',
            [],
            $breadcrumbs,
            ['channel' => $channel, 'bots' => $bots, 'formAction' => '/storage-channels/edit/' . $id]
        );
    }

    public function deleteStorageChannel($id)
    {
        $pdo = \TokoBot\Helpers\Database::getInstance();

        try {
            $stmt = $pdo->prepare("DELETE FROM bot_storage_channels WHERE id = ?");
            $stmt->execute([$id]);
            \TokoBot\Helpers\Session::flash('success_message', 'Storage channel deleted successfully!');
        } catch (\PDOException $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to delete storage channel: ' . $e->getMessage());
        }

        header('Location: /storage-channels');
        exit();
    }

    public function deleteStorageChannel($id)
    {
        $pdo = \TokoBot\Helpers\Database::getInstance();

        try {
            $stmt = $pdo->prepare("DELETE FROM bot_storage_channels WHERE id = ?");
            $stmt->execute([$id]);
            \TokoBot\Helpers\Session::flash('success_message', 'Storage channel deleted successfully!');
        } catch (\PDOException $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to delete storage channel: ' . $e->getMessage());
        }

        header('Location: /storage-channels');
        exit();
    }

    public function migrations()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Database Migrations']
        ];

        $migrations = [];
        $status = 'unknown';
        $output = '';

        try {
            $process = new \Symfony\Component\Process\Process([ROOT_PATH . '/vendor/bin/phinx', 'status', '-c', ROOT_PATH . '/phinx.php']);
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                $status = 'success';

                // Parse Phinx status output
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (preg_match('/^\s*([0-9]{14})\s*([^\s]+)\s*(up|down)\s*(.*)$/i', $line, $matches)) {
                        $migrations[] = [
                            'version' => $matches[1],
                            'name' => $matches[2],
                            'status' => $matches[3],
                            'started' => trim($matches[4])
                        ];
                    }
                }
            } else {
                $output = $process->getErrorOutput();
                $status = 'error';
                \TokoBot\Helpers\Session::flash('error_message', 'Phinx status command failed: ' . $output);
            }
        } catch (\Exception $e) {
            $output = $e->getMessage();
            $status = 'error';
            \TokoBot\Helpers\Session::flash('error_message', 'Error running Phinx status: ' . $output);
        }

        $this->renderDashmix(
            VIEWS_PATH . '/admin/migrations.php',
            'Database Migrations',
            'Manage and run database migrations.',
            [],
            $breadcrumbs,
            ['migrations' => $migrations, 'phinxOutput' => $output, 'phinxStatus' => $status]
        );
    }

    public function runMigrations()
    {
        try {
            $process = new \Symfony\Component\Process\Process([ROOT_PATH . '/vendor/bin/phinx', 'migrate', '-c', ROOT_PATH . '/phinx.php']);
            $process->run();

            if ($process->isSuccessful()) {
                \TokoBot\Helpers\Session::flash('success_message', 'Migrations ran successfully: ' . $process->getOutput());
            } else {
                \TokoBot\Helpers\Session::flash('error_message', 'Migrations failed: ' . $process->getErrorOutput());
            }
        } catch (\Exception $e) {
            \TokoBot\Helpers\Session::flash('error_message', 'Error running migrations: ' . $e->getMessage());
        }

        header('Location: /migrations');
        exit();
    }

    public function addBot()
    {
        $token = $_POST['token'] ?? '';

        if (empty($token)) {
            \TokoBot\Helpers\Session::flash('error_message', 'Token cannot be empty.');
            header('Location: /bot-management');
            exit();
        }

        try {
            // Use the provided token for this request
            $telegram = new Telegram($token, 'TokoBot');
            $response = Request::getMe();

            if ($response->isOk()) {
                $botInfo = $response->getResult();
                $botId = $botInfo->getId();

                // --- Save public info and token to DB ---
                $pdo = \TokoBot\Helpers\Database::getInstance();
                $sql = "INSERT INTO tbots (id, username, first_name, is_bot, token) VALUES (?, ?, ?, ?, ?) "
                     . "ON DUPLICATE KEY UPDATE username = VALUES(username), first_name = VALUES(first_name), token = VALUES(token)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $botId,
                    $botInfo->getUsername(),
                    $botInfo->getFirstName(),
                    $botInfo->getIsBot(),
                    $token // Simpan token di sini
                ]);

                $successMessage = 'Bot "' . $botInfo->getFirstName() . '" has been added/updated successfully!';
                \TokoBot\Helpers\Session::flash('success_message', $successMessage);
            } else {
                throw new TelegramException('Invalid token: ' . $response->getDescription());
            }
        } catch (TelegramException $e) {
            Logger::channel('telegram')->error('Failed to add bot', ['error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'Failed to add bot: ' . $e->getMessage());
        } catch (\Exception $e) {
            Logger::channel('app')->error('An unexpected error occurred in addBot', ['error' => $e->getMessage()]);
            \TokoBot\Helpers\Session::flash('error_message', 'An unexpected error occurred.');
        }

        header('Location: /bot-management');
        exit();
    }

    public function deleteBot($id)
    {
        try {
            // --- Delete from DB (set token to NULL) ---
            try {
                $pdo = \TokoBot\Helpers\Database::getInstance();
                // Kita tidak hapus botnya, hanya tokennya, agar relasi tetap ada
                $stmt = $pdo->prepare("UPDATE tbots SET token = NULL WHERE id = ?");
                $stmt->execute([$id]);
            } catch (\PDOException $e) {
                throw new DatabaseException('Failed to delete bot token from database.', (int)$e->getCode(), $e);
            }

            // --- Delete webhook file ---
            $webhookFile = PUBLIC_PATH . '/tbot/' . $id . '.php';
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

        header('Location: /bot-management');
        exit();
    }
}
