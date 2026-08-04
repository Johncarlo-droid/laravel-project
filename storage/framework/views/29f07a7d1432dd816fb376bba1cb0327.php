<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">User Access Management</h2>
        <div class="module-note">Manage user roles, department assignments, and account approval.</div>
    </div>
</div>

<div class="surface p-3 mb-3">
    <form method="GET" class="search-strip mb-0">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name, email, or role...">
        <button class="btn-primaryx" type="submit"><i class="bi bi-funnel"></i> Filter</button>
    </form>
</div>

<div class="surface p-3">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Approver Type</th>
                    <th>Approval</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="User">
                        <div style="font-weight:700"><?php echo e($user->name); ?></div>
                        <div class="tiny"><?php echo e($user->email); ?></div>
                    </td>
                    <td data-label="Department"><?php echo e($user->department->name ?? 'Not assigned'); ?></td>
                    <td data-label="Role"><span class="status <?php echo e(in_array($user->role, ['admin','super_admin']) ? 'approved' : ($user->role === 'approver' ? 'available' : 'pending')); ?>"><?php echo e(ucwords(str_replace('_',' ', $user->role))); ?></span></td>
                    <td data-label="Approver Type"><?php echo e($user->approver_type ? ucwords(str_replace('_',' ', $user->approver_type)) : '-'); ?></td>
                    <td data-label="Approval">
                        <span class="status <?php echo e($user->is_approved ? 'approved' : 'pending'); ?>"><?php echo e($user->is_approved ? 'Approved' : 'Pending Review'); ?></span>
                        <div class="tiny-2 mt-1"><?php echo e($user->email_verified_at ? 'Email verified' : 'Email not verified'); ?></div>
                    </td>
                    <td data-label="Actions">
                        <button class="btn-soft small-btn" type="button" data-bs-toggle="collapse" data-bs-target="#edit-user-<?php echo e($user->id); ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                        <?php if($user->id !== auth()->id()): ?>
                        <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" class="d-inline" onsubmit="return confirm('Delete this user account?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn-reject small-btn" type="submit"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="collapse-row">
                    <td colspan="6" class="p-0 border-0">
                        <div class="collapse" id="edit-user-<?php echo e($user->id); ?>">
                            <div class="p-3" style="background:#f7f8fb;border-top:1px solid #e2e5ee">
                                <form method="POST" action="<?php echo e(route('users.update', $user)); ?>" class="row g-3">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                        <input class="form-control" name="name" value="<?php echo e($user->name); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email" value="<?php echo e($user->email); ?>" readonly aria-readonly="true">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Role</label>
                                        <select class="form-select role-select" name="role" data-user-id="<?php echo e($user->id); ?>">
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role); ?>" <?php if($user->role === $role): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_',' ', $role))); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 approver-type-wrapper" id="approver-type-wrapper-<?php echo e($user->id); ?>">
                                        <label class="form-label">Approver Type</label>
                                        <select class="form-select approver-type-select" name="approver_type">
                                            <option value="">None</option>
                                            <?php $__currentLoopData = $approverTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type); ?>" <?php if($user->approver_type === $type): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_',' ', $type))); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department_id">
                                            <option value="">No department</option>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($department->id); ?>" <?php if((int) $user->department_id === (int) $department->id): echo 'selected'; endif; ?>><?php echo e($department->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Approval</label>
                                        <select class="form-select" name="is_approved">
                                            <option value="0" <?php if(!$user->is_approved): echo 'selected'; endif; ?>>Pending Review</option>
                                            <option value="1" <?php if($user->is_approved): echo 'selected'; endif; ?>>Approved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button class="btn-primaryx w-100 justify-content-center" type="submit"><i class="bi bi-save"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty-state">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <?php echo e($users->links()); ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.role-select').forEach(function (roleSelect) {
        const userId = roleSelect.dataset.userId;
        const wrapper = document.getElementById('approver-type-wrapper-' + userId);
        if (!wrapper) return;

        const approverTypeSelect = wrapper.querySelector('.approver-type-select');

        function toggleApproverType() {
            const isApprover = roleSelect.value === 'approver';
            wrapper.style.display = isApprover ? '' : 'none';
            if (!isApprover && approverTypeSelect) {
                approverTypeSelect.value = '';
            }
        }

        toggleApproverType();
        roleSelect.addEventListener('change', toggleApproverType);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', ['title' => 'User Management'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work/capex-opex/resources/views/users/index.blade.php ENDPATH**/ ?>