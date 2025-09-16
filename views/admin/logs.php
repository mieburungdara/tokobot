<?php
// Helper function to get badge color based on log level
function get_log_level_badge($level) {
    $level = strtoupper($level);
    switch ($level) {
        case 'CRITICAL':
        case 'ERROR':
            return 'bg-danger';
        case 'WARNING':
            return 'bg-warning';
        case 'INFO':
            return 'bg-info';
        case 'DEBUG':
            return 'bg-secondary';
        default:
            return 'bg-dark';
    }
}
?>

<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Application Logs</h3>
        <div class="block-options">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-log-channel" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Viewing: <?php echo ucfirst($_GET['log'] ?? 'app'); ?>.log
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-log-channel">
                    <a class="dropdown-item" href="/logs?log=app">app.log</a>
                    <a class="dropdown-item" href="/logs?log=telegram">telegram.log</a>
                    <a class="dropdown-item" href="/logs?log=critical">critical.log</a>
                </div>
            </div>
            <a href="/logs?action=clear&log=<?php echo ($_GET['log'] ?? 'app'); ?>" class="btn btn-sm btn-alt-danger" onclick="return confirm('Are you sure you want to clear this log file?');">
                <i class="fa fa-trash-alt"></i> Clear Log
            </a>
        </div>
    </div>
    <div class="block-content">
        <?php if (empty($logs) || (count($logs) === 1 && empty($logs[0]))): ?>
            <div class="alert alert-success text-center">
                <i class="fa fa-check-circle"></i> Log file is empty. No errors to show.
            </div>
        <?php else: ?>
            <pre class="bg-light p-3 rounded"><code><?php
                foreach ($logs as $log) {
                    echo htmlspecialchars($log) . "\n";
                }
            ?></code></pre>
        <?php endif; ?>
    </div>
</div>