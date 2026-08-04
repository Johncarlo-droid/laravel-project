<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Department Allocation</h2><div class="module-note">Manage department profiles and budget limits</div></div><a href="<?php echo e(route('departments.create')); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> Add Department</a></div>
<div class="surface p-3">
    <div class="search-strip"><i class="bi bi-search text-muted"></i><input class="search-input" placeholder="Search departments..."><div class="filter-box"><i class="bi bi-funnel text-muted"></i><select><option>All</option><option>High CAPEX</option></select></div></div>
    <div class="table-responsive">
    <table class="data-table">
        <thead><tr><th>Department</th><th>Code</th><th>CAPEX Limit</th><th>OPEX Limit</th><th>Users</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><div style="font-weight:700"><?php echo e($department->name); ?></div><div class="tiny">Budget controls and request routing</div></td>
                <td><?php echo e($department->code); ?></td>
                <td><?php echo e($department->capex_limit); ?></td>
                <td><?php echo e($department->opex_limit); ?></td>
                <td><?php echo e($department->users()->count()); ?></td>
                <td class="text-end"><a class="btn-soft small-btn" href="<?php echo e(route('departments.edit',$department)); ?>"><i class="bi bi-pencil"></i></a><form class="d-inline" method="POST" action="<?php echo e(route('departments.destroy',$department)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn-soft small-btn"><i class="bi bi-three-dots-vertical"></i></button></form></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="empty-state">No departments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <?php echo e($departments->links()); ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', ['title' => 'Departments'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/departments/index.blade.php ENDPATH**/ ?>