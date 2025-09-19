<?php

/**
 * @var \Template $this
 * @var array $viewData
 */

function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

$this->inc('views/inc/header.php', $viewData);

?>

<div class="content">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <p class="mb-0">_SESSION['success_message']</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <p class="mb-0">_SESSION['error_message']</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (!$viewData['apcu_enabled']): ?>
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">APCu Not Enabled</h3>
            </div>
            <div class="block-content">
                <div class="alert alert-danger">
                    <p>APCu extension is not loaded or enabled on this server. Caching functionality is disabled.</p>
                    <p>Please check your <code>php.ini</code> file and ensure <code>extension=apcu.so</code> is active and <code>apc.enabled=1</code>.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Cache Actions</h3>
                    </div>
                    <div class="block-content">
                        <form action="/admin/cache" method="POST" onsubmit="return confirm('Are you sure you want to clear the entire APCu cache? This may temporarily slow down the application.');">
                            <input type="hidden" name="action" value="clear">
                            <input type="hidden" name="csrf_token" value="<?= $viewData['csrf_token'] ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash me-1"></i> Clear APCu Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Memory Chart -->
            <div class="col-md-6">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Memory Usage</h3>
                    </div>
                    <div class="block-content">
                        <canvas id="memory-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Hit/Miss Chart -->
            <div class="col-md-6">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Hit/Miss Ratio</h3>
                    </div>
                    <div class="block-content">
                        <canvas id="hit-miss-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- General Info -->
            <div class="col-md-6">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">General Information</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-striped table-vcenter">
                            <tbody>
                                <tr>
                                    <td>APCu Version</td>
                                    <td>_phpversion('apcu')</td>
                                </tr>
                                <tr>
                                    <td>Start Time</td>
                                    <td>date('Y-m-d H:i:s', $viewData['cache_info']['start_time'])</td>
                                </tr>
                                <tr>
                                    <td>Uptime</td>
                                    <td>number_format($viewData['cache_info']['ttl']) seconds</td>
                                </tr>
                                <tr>
                                    <td>Cached Entries</td>
                                    <td>number_format($viewData['cache_info']['num_entries'])</td>
                                </tr>
                                <tr>
                                    <td>Hits</td>
                                    <td>number_format($viewData['cache_info']['num_hits'])</td>
                                </tr>
                                <tr>
                                    <td>Misses</td>
                                    <td>number_format($viewData['cache_info']['num_misses'])</td>
                                </tr>
                                <tr>
                                    <td>Hit Rate</td>
                                    <td>
                                        <?php 
                                        $total = $viewData['cache_info']['num_hits'] + $viewData['cache_info']['num_misses'];
                                        $rate = ($total > 0) ? ($viewData['cache_info']['num_hits'] / $total) * 100 : 0;
                                        echo number_format($rate, 2) . ' %';
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="col-md-6">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Memory Usage</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-striped table-vcenter">
                            <tbody>
                                <tr>
                                    <td>Total Memory</td>
                                    <td>format_bytes($viewData['sma_info']['seg_size'])</td>
                                </tr>
                                <tr>
                                    <td>Used Memory</td>
                                    <td>format_bytes($viewData['sma_info']['seg_size'] - $viewData['sma_info']['avail_mem'])</td>
                                </tr>
                                <tr>
                                    <td>Free Memory</td>
                                    <td>format_bytes($viewData['sma_info']['avail_mem'])</td>
                                </tr>
                                <tr>
                                    <td>Fragmentation</td>
                                    <td>
                                        <?php 
                                        $frag_perc = ($viewData['sma_info']['avail_mem'] > 0) ? ($viewData['cache_info']['mem_size'] / $viewData['sma_info']['avail_mem']) * 100 : 0;
                                        echo number_format($frag_perc, 2) . ' %';
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cached Entries List -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Cached Entries</h3>
            </div>
            <div class="block-content">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Size</th>
                            <th>Created At</th>
                            <th>Expires At (TTL)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($viewData['cache_info']['cache_list'])): ?>
                            <tr>
                                <td colspan="4" class="text-center">No entries in cache.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($viewData['cache_info']['cache_list'] as $entry): ?>
                                <tr>
                                    <td><code>entry['info']</code></td>
                                    <td>format_bytes($entry['mem_size'])</td>
                                    <td>date('Y-m-d H:i:s', $entry['creation_time'])</td>
                                    <td>
                                        <?php 
                                        if ($entry['ttl'] == 0) {
                                            echo 'Never';
                                        } else {
                                            echo date('Y-m-d H:i:s', $entry['creation_time'] + $entry['ttl']);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($viewData['apcu_enabled']): ?>
        // Memory Chart
        const memoryCtx = document.getElementById('memory-chart');
        const memoryData = {
            labels: [
                'Used Memory',
                'Free Memory'
            ],
            datasets: [{
                data: [
                    <?= $viewData['sma_info']['seg_size'] - $viewData['sma_info']['avail_mem'] ?>,
                    <?= $viewData['sma_info']['avail_mem'] ?>
                ],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)'
                ],
                hoverOffset: 4
            }]
        };
        new Chart(memoryCtx, {
            type: 'doughnut',
            data: memoryData,
        });

        // Hit/Miss Chart
        const hitMissCtx = document.getElementById('hit-miss-chart');
        const hitMissData = {
            labels: [
                'Hits',
                'Misses'
            ],
            datasets: [{
                data: [
                    <?= $viewData['cache_info']['num_hits'] ?>,
                    <?= $viewData['cache_info']['num_misses'] ?>
                ],
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        };
        new Chart(hitMissCtx, {
            type: 'doughnut',
            data: hitMissData,
        });
        <?php endif; ?>
    });
</script>
<?php $this->addScript(ob_get_clean()); ?>

<?php $this->inc('views/inc/footer.php'); ?>