<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Storage Channels</h3>
            <div class="block-options">
                <a href="/storage-channels/add" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus me-1"></i> Add Channel
                </a>
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

            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Bot ID</th>
                        <th>Channel ID</th>
                        <th>Last Used At</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($storageChannels)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No storage channels found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($storageChannels as $channel): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($channel['id']); ?></td>
                                <td><?php echo htmlspecialchars($channel['bot_id']); ?></td>
                                <td><?php echo htmlspecialchars($channel['channel_id']); ?></td>
                                <td><?php echo htmlspecialchars($channel['last_used_at'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/storage-channels/edit/<?php echo $channel['id']; ?>" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                        </a>
                                        <form action="/storage-channels/delete/<?php echo $channel['id']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this channel?');">
                                            <button type="submit" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fa fa-fw fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
