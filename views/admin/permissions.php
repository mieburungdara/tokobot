<?php

/**
 * @var \Template $this
 * @var array $viewData
 */

$this->inc('views/inc/header.php', $viewData);

$roles = $viewData['roles'];
$permissions = $viewData['permissions'];

?>

<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Role & Permission Matrix</h3>
        </div>
        <div class="block-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <p class="mb-0"><?= $_SESSION['success_message'] ?></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <p class="mb-0"><?= $_SESSION['error_message'] ?></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <form action="/admin/permissions" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $viewData['csrf_token'] ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <?php foreach ($roles as $role): ?>
                                    <th class="text-center"><?= ucfirst($role['name']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $permission): ?>
                                <tr>
                                    <td>
                                        <strong><?= ucfirst(str_replace('_', ' ', $permission['name'])) ?></strong>
                                    </td>
                                    <?php foreach ($roles as $role): ?>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" type="checkbox"
                                                       name="permissions[<?= $role['id'] ?>][<?= $permission['id'] ?>]"
                                                       id="check-<?= $role['id'] ?>-<?= $permission['id'] ?>"
                                                       <?php if (in_array($permission['id'], $role['permissions'])): ?>checked<?php endif; ?>
                                                       <?php if ($role['name'] === 'admin'): ?>disabled<?php endif; ?>>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->inc('views/inc/footer.php'); ?>
