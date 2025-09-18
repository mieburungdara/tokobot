<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Database Migrations</h3>
            <div class="block-options">
                <form action="/migrations/run" method="POST" onsubmit="return confirm('Are you sure you want to run pending migrations? This cannot be undone.');">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa fa-play me-1"></i> Run Migrations
                    </button>
                </form>
            </div>
        </div>
        <div class="block-content block-content-full">
            <?php if (\TokoBot\Helpers\Session::has('success_message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo \TokoBot\Helpers\Session::flash('success_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (\TokoBot\Helpers\Session::has('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo \TokoBot\Helpers\Session::flash('error_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <h4 class="fw-light">Migration Status</h4>
            <?php if ($phinxStatus === 'error'): ?>
                <div class="alert alert-danger">Error fetching migration status: <pre><?php echo htmlspecialchars($phinxOutput); ?></pre></div>
            <?php elseif (empty($migrations)): ?>
                <div class="alert alert-info">No migrations found or Phinx output could not be parsed.</div>
                <pre><?php echo htmlspecialchars($phinxOutput); ?></pre>
            <?php else: ?>
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Version</th>
                            <th>Migration Name</th>
                            <th style="width: 100px;">Status</th>
                            <th>Started At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($migrations as $migration): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($migration['version']); ?></td>
                                <td><?php echo htmlspecialchars($migration['name']); ?></td>
                                <td>
                                    <?php if ($migration['status'] === 'up'): ?>
                                        <span class="badge bg-success">Up</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Down</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($migration['started']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h4 class="fw-light mt-4">Raw Phinx Output</h4>
                <pre class="bg-light p-3 rounded"><?php echo htmlspecialchars($phinxOutput); ?></pre>
            <?php endif; ?>
        </div>
    </div>
</div>
