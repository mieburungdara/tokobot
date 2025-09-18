<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">All Users</h3>
    </div>
    <div class="block-content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">ID</th>
                        <th>Name</th>
                        <th style="width: 30%;">Role</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-center"><?php echo htmlspecialchars($user['telegram_id']); ?></td>
                        <td class="fw-semibold">
                            <a href="#"><?php echo htmlspecialchars($user['first_name']); ?></a>
                            (<?php echo htmlspecialchars($user['username']); ?>)
                        </td>
                        <td>
                            <form action="/users/update-role" method="POST" class="d-inline-flex align-items-center">
                                <input type="hidden" name="telegram_id" value="<?php echo htmlspecialchars($user['telegram_id']); ?>">
                                <select name="role_id" class="form-select form-select-sm me-2">
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo htmlspecialchars($role['id']); ?>"
                                            <?php echo (isset($user['role_name']) && $user['role_name'] === $role['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(ucfirst($role['name'])); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-alt-primary" data-bs-toggle="tooltip" title="Update Role">
                                    <i class="fa fa-save"></i>
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Delete">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
