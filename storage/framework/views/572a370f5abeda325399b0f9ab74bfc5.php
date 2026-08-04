<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">Activity Proposals</h2>
        <div class="module-note">Digital routing: Adviser → Department → Facilities Management Office. No more walking the form around campus.</div>
    </div>
    <a href="<?php echo e(route('activity-proposals.create')); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> New Proposal</a>
</div>
<div class="page-tabs">
    <span class="active">
        <?php if(auth()->user()->isAdviserApprover()): ?> Adviser Queue
        <?php elseif(auth()->user()->isDeanApprover()): ?> Department Queue
        <?php elseif(auth()->user()->isFmo()): ?> FMO Queue
        <?php elseif(auth()->user()->isAdmin()): ?> All Proposals
        <?php else: ?> My Proposals
        <?php endif; ?>
    </span>
</div>
<div class="surface p-3">
    <?php $__empty_1 = true; $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="request-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-weight:800"><?php echo e($proposal->proposal_no); ?></span>
                    <span class="status <?php echo e($proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending')); ?>"><?php echo e($proposal->statusLabel()); ?></span>
                    <span class="tiny"><i class="bi bi-calendar-event"></i> <?php echo e(optional($proposal->start_at)->format('Y-m-d H:i')); ?></span>
                </div>
                <div style="font-weight:700;margin-top:4px"><?php echo e($proposal->title); ?></div>
                <div class="tiny">Requested by: <?php echo e($proposal->user->name ?? 'Unknown'); ?> · Venue: <?php echo e($proposal->facility->name ?? 'N/A'); ?></div>
                <div class="tiny-2 mt-1">Adviser: <?php echo e($proposal->adviser->name ?? 'N/A'); ?> · Department Approver: <?php echo e($proposal->departmentApprover->name ?? 'N/A'); ?></div>
                <?php if($proposal->status === 'rejected' && $proposal->rejection_reason): ?>
                <div class="tiny mt-2 text-danger"><strong>Reason:</strong> <?php echo e($proposal->rejection_reason); ?></div>
                <?php endif; ?>
            </div>
            <div class="request-actions">
                <a class="btn-approve" href="<?php echo e(route('activity-proposals.show', $proposal)); ?>"><i class="bi bi-eye"></i> View</a>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="empty-state">No activity proposals found.</div>
    <?php endif; ?>
    <?php echo e($proposals->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Activity Proposals'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/activity_proposals/index.blade.php ENDPATH**/ ?>