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
            const response = await fetch(`/api/bots/${currentBotId}/webhook`);
            const data = await response.json();

            let content = '<dl class="row'>';
            if (data.error) {
                content = `<div class="alert alert-danger">${data.error}</div>`;
            } else if (!data.url) {
                content = `<div class="alert alert-warning">Webhook is not set.</div>`;
            } else {
                for (const [key, value] of Object.entries(data)) {
                    if (value) {
                        content += `<dt class="col-sm-4">${key.replace(/_/g, ' ')}</dt><dd class="col-sm-8">${value}</dd>`;
                    }
                }
            }
            content += '</dl>';
            webhookInfoContent.innerHTML = content;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger">Failed to fetch webhook status.</div>`;
        }
    });

    setWebhookForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        webhookInfoContent.innerHTML = `<div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>`;
        
        try {
            const response = await fetch(`/api/bots/${currentBotId}/webhook`, {
                method: 'POST',
                body: new FormData(setWebhookForm)
            });
            const data = await response.json();
            let alertType = data.success ? 'success' : 'danger';
            let message = data.success ? data.message : data.error;
            webhookInfoContent.innerHTML = `<div class="alert alert-${alertType}">${message}</div>`;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        }
    });

    deleteWebhookBtn.addEventListener('click', async function () {
        if (!confirm('Are you sure you want to delete the webhook for this bot?')) {
            return;
        }
        webhookInfoContent.innerHTML = `<div class="text-center py-4"><i class="fa fa-2x fa-spinner fa-spin"></i></div>`;

        try {
            const response = await fetch(`/api/bots/${currentBotId}/webhook`, {
                method: 'DELETE'
            });
            const data = await response.json();
            let alertType = data.success ? 'success' : 'danger';
            let message = data.success ? data.message : data.error;
            webhookInfoContent.innerHTML = `<div class="alert alert-${alertType}">${message}</div>`;
        } catch (error) {
            webhookInfoContent.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        }
    });
});
</script>
