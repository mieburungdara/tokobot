<!-- START BOTS VIEW -->
<div class="block block-rounded">
  <div class="block-header block-header-default">
    <h3 class="block-title">Bot Management</h3>
    <div class="block-options">
      <button type="button" class="btn-block-option" data-bs-toggle="modal" data-bs-target="#modal-add-bot">
        <i class="si si-plus"></i> Add Bot
      </button>
    </div>
  </div>
  <div class="block-content">
    <?php if (\TokoBot\Helpers\Session::get('success_message')) : ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <p class="mb-0"><?php echo \TokoBot\Helpers\Session::flash('success_message'); ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (\TokoBot\Helpers\Session::get('error_message')) : ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <p class="mb-0"><?php echo \TokoBot\Helpers\Session::flash('error_message'); ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <table class="table table-striped table-vcenter">
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th>Name</th>
          <th>Username</th>
          <th class="text-center">Token Status</th>
          <th class="text-center" style="width: 100px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($bots)) : ?>
          <tr>
            <td colspan="5" class="text-center">No bots configured yet.</td>
          </tr>
        <?php else : ?>
          <?php foreach ($bots as $index => $bot) : ?>
            <tr>
              <td class="text-center"><?php echo $index + 1; ?></td>
              <td class="fw-semibold"><?php echo htmlspecialchars($bot['first_name']); ?></td>
              <td>@<?php echo htmlspecialchars($bot['username']); ?></td>
              <td class="text-center">
                <?php if ($bot['has_token']) : ?>
                  <span class="badge bg-success">Exists</span>
                <?php else : ?>
                  <span class="badge bg-danger">Missing</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Edit">
                    <i class="fa fa-pencil-alt"></i>
                  </button>
                  <form action="/tbot/<?php echo $bot['id']; ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bot?');">
                    <button type="submit" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Delete">
                      <i class="fa fa-times"></i>
                    </button>
                  </form>
                  <button type="button" class="btn btn-sm btn-alt-secondary webhook-btn" data-bs-toggle="modal" data-bs-target="#modal-webhook-status" data-bot-id="<?php echo $bot['id']; ?>" data-bot-name="<?php echo htmlspecialchars($bot['first_name']); ?>" data-bs-toggle="tooltip" title="Webhook">
                    <i class="fa fa-sitemap"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Bot Modal -->
<div class="modal" id="modal-add-bot" tabindex="-1" role="dialog" aria-labelledby="modal-add-bot" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="block block-rounded block-transparent mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">Add New Bot</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-fw fa-times"></i>
            </button>
          </div>
        </div>
        <div class="block-content">
          <form action="/tbot" method="POST">
            <div class="mb-4">
              <label class="form-label" for="bot-token">Bot Token</label>
              <input type="text" class="form-control" id="bot-token" name="token" placeholder="Enter bot's API token...">
            </div>
            <div class="mb-4">
              <button type="submit" class="btn btn-primary w-100">Check & Save Bot</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Webhook Status Modal -->
<div class="modal" id="modal-webhook-status" tabindex="-1" role="dialog" aria-labelledby="modal-webhook-status" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="block block-rounded block-transparent mb-0">
        <div class="block-header block-header-default">
          <h3 class="block-title">Webhook Status for <span id="webhook-bot-name"></span></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa fa-fw fa-times"></i>
            </button>
          </div>
        </div>
        <div class="block-content" id="webhook-info-content">
          <div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>
        </div>
        <div class="block-content block-content-full text-end bg-body">
            <form id="form-set-webhook" class="w-100 mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="webhook-url" name="url" placeholder="Set Webhook URL...">
                    <button type="submit" class="btn btn-primary">Set</button>
                </div>
            </form>
            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="btn-delete-webhook">Delete Webhook</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const webhookModal = document.getElementById('modal-webhook-status');
    const webhookBotName = document.getElementById('webhook-bot-name');
    const webhookInfoContent = document.getElementById('webhook-info-content');
    const webhookUrlInput = document.getElementById('webhook-url');
    const setWebhookForm = document.getElementById('form-set-webhook');
    const deleteWebhookBtn = document.getElementById('btn-delete-webhook');
    let currentBotId = null;

    webhookModal.addEventListener('show.bs.modal', async function (event) {
        const button = event.relatedTarget;
        currentBotId = button.getAttribute('data-bot-id');
        const botName = button.getAttribute('data-bot-name');
        
        webhookBotName.textContent = botName;
        webhookInfoContent.innerHTML = `<div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>`;
        webhookUrlInput.value = `${window.location.origin}/bots/${currentBotId}.php`;

        try {
            const response = await fetch(`/api/tbot/${currentBotId}/webhook`);
            const data = await response.json();

            let content = '<dl class="row">';
            if (data.error) {
                content = `<div class="alert alert-danger">${data.error}</div>`;
            } else if (!data.url) {
                content = `<div class="alert alert-warning">Webhook is not set.</div>`;
            } else {
                const formatTitle = (str) => {
                    return str.replace(/_/g, ' ').replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
                };

                for (const [key, value] of Object.entries(data)) {
                    if (value !== null && value !== '') {
                        let formattedValue = value;
                        let formattedKey = formatTitle(key);

                        if (key === 'last_error_date') {
                            formattedValue = new Date(value * 1000).toLocaleString(undefined, { dateStyle: 'long', timeStyle: 'medium' });
                        } else if (key === 'last_error_message') {
                            formattedValue = `<span class="text-danger fw-semibold">${value}</span>`;
                        } else if (typeof value === 'boolean') {
                            formattedValue = value ? '<span class="text-success">Yes</span>' : '<span class="text-muted">No</span>';
                        }

                        content += `<dt class="col-sm-5 text-muted">${formattedKey}</dt><dd class="col-sm-7">${formattedValue}</dd>`;
                    }
                }
            }
            content += '</dl>';
            webhookInfoContent.innerHTML = content;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${error.message}<br><small>Could not connect to the server or the response was not valid JSON.</small></div>`;
        }
    });

    setWebhookForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        webhookInfoContent.innerHTML = `<div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>`;
        
        try {
            const response = await fetch(`/api/tbot/${currentBotId}/webhook`, {
                method: 'POST',
                body: new FormData(setWebhookForm)
            });
            const data = await response.json();
            let alertType = data.success ? 'success' : 'danger';
            let message = data.success ? data.message : data.error;
            webhookInfoContent.innerHTML = `<div class="alert alert-${alertType}">${message}</div>`;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${error.message}<br><small>Could not connect to the server or the response was not valid JSON.</small></div>`;
        }
    });

    deleteWebhookBtn.addEventListener('click', async function () {
        if (!confirm('Are you sure you want to delete the webhook for this bot?')) {
            return;
        }
        webhookInfoContent.innerHTML = `<div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>`;

        try {
            const response = await fetch(`/api/tbot/${currentBotId}/webhook`, {
                method: 'DELETE'
            });
            const data = await response.json();
            let alertType = data.success ? 'success' : 'danger';
            let message = data.success ? data.message : data.error;
            webhookInfoContent.innerHTML = `<div class="alert alert-${alertType}">${message}</div>`;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${error.message}<br><small>Could not connect to the server or the response was not valid JSON.</small></div>`;
        }
    });
});
</script>
