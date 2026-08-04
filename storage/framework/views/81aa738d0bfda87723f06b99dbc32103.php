<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Supplier Management</h2><div class="module-note">Manage vendor information and contacts</div></div><a href="<?php echo e(route('suppliers.create')); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> Add Supplier</a></div>
<div class="surface p-3">
    <div class="search-strip" style="max-width:230px"><i class="bi bi-search text-muted"></i><input class="search-input" placeholder="Search suppliers..."></div>
    <div class="grid-cards">
        <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="supplier-card">
            <div style="font-weight:800;font-size:14px"><?php echo e($supplier->name); ?></div>
            <div class="supplier-meta">S<?php echo e(str_pad($supplier->id, 3, '0', STR_PAD_LEFT)); ?></div>
            <div class="supplier-avatar"><?php echo e(strtoupper(substr($supplier->contact_person ?: $supplier->name, 0, 1))); ?></div>
            <div style="font-weight:700;font-size:13px"><?php echo e($supplier->contact_person ?: 'Contact Person'); ?></div>
            <div class="tiny">Contact Person</div>
            <div class="muted-line"><i class="bi bi-envelope"></i><span><?php echo e($supplier->email ?: 'No email provided'); ?></span></div>
            <div class="muted-line"><i class="bi bi-telephone"></i><span><?php echo e($supplier->phone ?: 'No phone provided'); ?></span></div>
            <div class="muted-line"><i class="bi bi-geo-alt"></i><span><?php echo e($supplier->address ?: 'Clark Freeport Zone'); ?></span></div>
            <hr>
            <div class="tiny-2 mb-1">PRODUCTS</div>
            <span class="tag">Office Supplies</span><span class="tag">Furniture</span>
            <div class="mt-3 d-flex gap-2"><a class="btn-soft small-btn" href="<?php echo e(route('suppliers.edit', $supplier)); ?>"><i class="bi bi-pencil"></i> Edit</a><form method="POST" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn-soft small-btn"><i class="bi bi-trash"></i></button></form></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty-state" style="grid-column:1/-1">No suppliers found.</div>
        <?php endif; ?>
    </div>
    <?php echo e($suppliers->links()); ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', ['title' => 'Supplier Management'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/suppliers/index.blade.php ENDPATH**/ ?>