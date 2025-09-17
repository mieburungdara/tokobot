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
            <h3 class="block-title">Log Viewer <small class="text-muted">- <?= htmlentities(ucfirst($logChannel)) ?>.log</small></h3>
            <div class="block-options">
                <!-- Grup tombol untuk memilih channel log -->
                <div class="btn-group" role="group" aria-label="Log Channels">
                    <?php foreach ($allowedLogs as $channel) : ?>
                        <a href="/logs?log=<?= urlencode($channel) ?>" class="btn btn-sm <?= $logChannel === $channel ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <?= htmlentities(ucfirst($channel)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a href="/logs?log=<?= urlencode($logChannel) ?>&action=clear" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Anda yakin ingin membersihkan file log ini?');">
                    <i class="fa fa-trash me-1"></i> Clear Log
                </a>
            </div>
        </div>
        <div class="block-content">
            <?php if (empty($logs)) : ?>
                <div class="alert alert-info text-center">
                    File log kosong.
                </div>
            <?php else : ?>
                <pre class="p-3 bg-light rounded" style="max-height: 600px; overflow-y: auto;"><code><?php
                    foreach ($logs as $log) {
                        // Escape output untuk mencegah XSS
                        echo htmlentities($log, ENT_QUOTES, 'UTF-8') . "\n";
                    }
                ?></code></pre>
            <?php endif; ?>
        </div>
    </div>