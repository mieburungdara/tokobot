<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title"><?php echo $pageTitle; ?></h3>
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

            <form action="<?php echo $formAction; ?>" method="POST">
                <div class="row push">
                    <div class="col-lg-8 col-xl-5">
                        <div class="mb-4">
                            <label class="form-label" for="bot_id">Bot</label>
                            <select class="form-select" id="bot_id" name="bot_id">
                                <option value="">Select a Bot</option>
                                <?php foreach ($bots as $bot): ?>
                                    <option value="<?php echo htmlspecialchars($bot['id']); ?>"
                                        <?php echo (isset($channel['bot_id']) && $channel['bot_id'] == $bot['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($bot['username'] ?? $bot['id']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="channel_id">Channel ID</label>
                            <input type="text" class="form-control" id="channel_id" name="channel_id" placeholder="Enter Telegram Channel ID (e.g., -1001234567890)" value="<?php echo htmlspecialchars($channel['channel_id'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-primary">Submit</button>
                            <a href="/storage-channels" class="btn btn-alt-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
