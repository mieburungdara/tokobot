<?php
// views/admin/analytics.php
?>

<!-- Page Content -->
<div class="content">
    <!-- Overview -->
    <div class="row items-push">
        <div class="col-sm-6 col-xl-4">
            <div class="block block-rounded d-flex flex-column h-100 mb-0">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold"><?php echo count($activeUsers); ?></dt>
                        <dd class="fs-sm fw-medium text-muted mb-0">Recently Active Users</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="fa fa-users fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="block block-rounded d-flex flex-column h-100 mb-0">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold"><?php echo array_sum(array_column($commandUsage, 'command_count')); ?></dt>
                        <dd class="fs-sm fw-medium text-muted mb-0">Total Commands Used</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="fa fa-terminal fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="block block-rounded d-flex flex-column h-100 mb-0">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold"><?php echo count($appLogs) + count($telegramLogs); ?></dt>
                        <dd class="fs-sm fw-medium text-muted mb-0">Recent Errors</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="fa fa-exclamation-circle fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Overview -->

    <!-- Top Commands and Active Users -->
    <div class="row">
        <div class="col-xl-6">
            <!-- Top Commands -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Top Commands</h3>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th class="text-center">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandUsage as $command):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($command['text']); ?></td>
                                <td class="text-center"><?php echo $command['command_count']; ?></td>
                            </tr>
                            <?php endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END Top Commands -->
        </div>
        <div class="col-xl-6">
            <!-- Recently Active Users -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Recently Active Users</h3>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Last Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeUsers as $user):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' (@' . $user['username'] . ')'); ?></td>
                                <td><?php echo $user['last_activity_at']; ?></td>
                            </tr>
                            <?php endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END Recently Active Users -->
        </div>
    </div>
    <!-- END Top Commands and Active Users -->

    <!-- Recent Errors -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Recent Errors</h3>
        </div>
        <div class="block-content">
            <h5>Application Errors (app.log)</h5>
            <pre class="bg-light p-2 rounded"><code><?php echo empty($appLogs) ? 'No errors.' : htmlspecialchars(implode("\n", $appLogs)); ?></code></pre>
            <h5 class="mt-4">Telegram API Errors (telegram.log)</h5>
            <pre class="bg-light p-2 rounded"><code><?php echo empty($telegramLogs) ? 'No errors.' : htmlspecialchars(implode("\n", $telegramLogs)); ?></code></pre>
        </div>
    </div>
    <!-- END Recent Errors -->
</div>
<!-- END Page Content -->