<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Requisitions</h2><div class="module-note">Charge slip requests routed through Asset Management, College Dean, and Executive Director.</div></div><?php if(auth()->user()->isRequestor() || auth()->user()->isAdmin()): ?><a href="<?php echo e(route('requisitions.create')); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> New Request</a><?php endif; ?></div>
<div class="page-tabs"><span class="active">Request Monitoring</span><span><?php echo e(auth()->user()->isAdmin() ? 'Asset Management View' : (auth()->user()->isApprover() ? 'Approver Queue' : 'My Requests')); ?></span></div>
<div class="surface p-3">
    <form method="GET" class="search-strip mb-3">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="Search by reference, requester, or item...">
        <div class="filter-box"><i class="bi bi-funnel text-muted"></i>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php $__currentLoopData = ['pending_asset_management' => 'Asset Management','pending_college_dean' => 'College Dean','pending_executive_director' => 'Executive Director','approved' => 'Approved','partially_approved' => 'Partially Approved','rejected' => 'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(($status ?? '') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button class="btn-primaryx small-btn" type="submit">Apply</button>
    </form>
    <?php $__empty_1 = true; $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requisition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="request-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap"><span style="font-weight:800"><?php echo e($requisition->requisition_no); ?></span><span class="status <?php echo e(str_contains($requisition->status,'approved') ? 'approved' : ($requisition->status === 'rejected' ? 'low' : 'pending')); ?>"><?php echo e($requisition->statusLabel()); ?></span><span class="tiny"><i class="bi bi-calendar-event"></i> <?php echo e(optional($requisition->requested_at)->format('Y-m-d')); ?></span></div>
                <div style="font-weight:700;margin-top:4px"><?php echo e($requisition->department->name ?? 'No Department'); ?> · <?php echo e($requisition->branch ?: 'NU Clark'); ?></div>
                <div class="tiny">Requested by: <?php echo e($requisition->user->name ?? 'Unknown User'); ?></div>
                <div class="tiny-2 mt-1">Purpose: <?php echo e($requisition->purpose ?: 'No purpose stated.'); ?></div>
                <div class="tiny-2 mt-1">Items: <?php echo e($requisition->items->map(fn($line) => ($line->item->name ?? 'Item').' x'.$line->quantity_requested)->join(', ')); ?></div>
                <?php if($requisition->status === 'rejected' && $requisition->rejection_reason): ?>
                <div class="tiny mt-2 text-danger"><strong>Reason:</strong> <?php echo e($requisition->rejection_reason); ?></div>
                <?php endif; ?>
            </div>
            <div class="request-actions">
                <a class="btn-approve" href="<?php echo e(route('requisitions.show', $requisition)); ?>"><i class="bi bi-eye"></i> View</a>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="empty-state">No requisitions found.</div>
    <?php endif; ?>
    <?php echo e($requisitions->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Requisitions'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/sys/capex-opex-main/resources/views/requisitions/index.blade.php ENDPATH**/ ?>