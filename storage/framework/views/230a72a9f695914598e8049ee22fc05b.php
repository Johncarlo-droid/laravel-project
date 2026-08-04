<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Issuance & Returns</h2><div class="module-note">Process approved requests and mark issued items as returned</div></div><a href="<?php echo e(route('issuances.create')); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> New Issuance</a></div>
<div class="page-tabs"><span class="active"><i class="bi bi-arrow-up-right-circle"></i> Issued (<?php echo e($issuances->where('status','issued')->count()); ?>)</span><span><i class="bi bi-arrow-counterclockwise"></i> Returned (<?php echo e($issuances->where('status','returned')->count()); ?>)</span></div>
<div class="surface p-3">
    <div class="search-strip"><i class="bi bi-search text-muted"></i><input class="search-input" placeholder="Search approved requests..."></div>
    <?php $__empty_1 = true; $__currentLoopData = $issuances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issuance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="issue-card">
        <div class="d-flex justify-content-between gap-4 align-items-start flex-wrap">
            <div>
                <div style="font-weight:800"><?php echo e($issuance->requisition->requisition_no ?? 'N/A'); ?> <span class="status approved"><?php echo e(ucfirst($issuance->status)); ?></span></div>
                <div class="tiny">Requester: <?php echo e($issuance->receiver->name ?? 'N/A'); ?> (<?php echo e($issuance->requisition->department->name ?? 'Department'); ?>)</div>
                <div class="tiny mt-2"><strong>ITEM TO ISSUE:</strong></div>
                <?php $__empty_2 = true; $__currentLoopData = $issuance->requisition->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reqItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <div class="tiny">- <?php echo e($reqItem->quantity_requested); ?>x <?php echo e($reqItem->item->name ?? 'Item'); ?> <span class="pill-opex"><?php echo e($reqItem->item->item_type ?? 'OPEX'); ?></span></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <div class="tiny">No linked item found.</div>
                <?php endif; ?>
            </div>
            <div class="d-grid gap-2" style="min-width:170px">
                <?php if($issuance->status === 'issued'): ?>
                    <form method="POST" action="<?php echo e(route('issuances.return',$issuance)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-primaryx small-btn w-100"><i class="bi bi-arrow-counterclockwise"></i> To Return</button>
                    </form>
                <?php else: ?>
                    <button type="button" class="btn btn-light btn-sm w-100" style="border-radius:8px;border:1px solid #c9ced6" disabled>Already Returned</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="empty-state">No issuance records found.</div>
    <?php endif; ?>
    <?php echo e($issuances->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Issuance & Returns'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\System Demo\resources\views/issuances/index.blade.php ENDPATH**/ ?>