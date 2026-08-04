<?php $__env->startSection('content'); ?>
<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:18px"><?php echo e($proposal->proposal_no); ?></h2>
                <div class="module-note"><?php echo e($proposal->title); ?></div>
            </div>
            <span class="status <?php echo e($proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending')); ?>"><?php echo e($proposal->statusLabel()); ?></span>
        </div>

        <div class="row g-2 mb-3 tiny">
            <div class="col-md-6"><strong>Organization:</strong> <?php echo e($proposal->organization_name); ?></div>
            <div class="col-md-6"><strong>Requested By:</strong> <?php echo e($proposal->user->name ?? 'N/A'); ?> <?php if($proposal->requester_position): ?>(<?php echo e($proposal->requester_position); ?>)<?php endif; ?></div>
            <div class="col-md-6"><strong>Venue:</strong> <?php echo e($proposal->facility->name ?? 'N/A'); ?> — <?php echo e($proposal->facility->location ?? ''); ?> <?php if($proposal->venue_other_note): ?><span class="tiny-2">(<?php echo e($proposal->venue_other_note); ?>)</span><?php endif; ?></div>
            <div class="col-md-6"><strong>Schedule:</strong> <?php echo e(optional($proposal->start_at)->format('Y-m-d H:i')); ?> – <?php echo e(optional($proposal->end_at)->format('Y-m-d H:i')); ?></div>
            <div class="col-md-6"><strong>Day(s) of Activity:</strong> <?php echo e($proposal->activity_days ?: 'N/A'); ?></div>
            <div class="col-md-6"><strong>Expected Attendees:</strong> <?php echo e($proposal->participants_count); ?></div>
            <?php if($proposal->speaker_name): ?><div class="col-12"><strong>Speaker:</strong> <?php echo e($proposal->speaker_name); ?></div><?php endif; ?>
            <div class="col-md-6"><strong>Venue Slot:</strong> <?php echo e(ucfirst($proposal->reservation->status ?? 'N/A')); ?> <?php if($proposal->reservation && $proposal->reservation->isPrePlotted()): ?><span class="tiny text-muted">(pre-plotted — not yet confirmed)</span><?php endif; ?></div>
            <div class="col-12"><strong>Other Items Needed and Services:</strong> <?php echo e($proposal->equipment_needed ?: 'None specified'); ?></div>
            <div class="col-12"><strong>Program Flow:</strong><br><?php echo nl2br(e($proposal->program_flow)); ?></div>
        </div>

        <div class="mt-3 tiny">
            <div><strong>Prepared By — Adviser / Program Chair:</strong> <?php echo e($proposal->adviserSigner->name ?? ($proposal->adviser->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->adviser_signed_at): ?> — signed <?php echo e($proposal->adviser_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
            <div><strong>Noted By — Dean / Principal:</strong> <?php echo e($proposal->departmentSigner->name ?? ($proposal->departmentApprover->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->department_signed_at): ?> — signed <?php echo e($proposal->department_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
            <div><strong>Noted By — SDAO:</strong> <?php echo e($proposal->sdaoSigner->name ?? ($proposal->sdao->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->sdao_signed_at): ?> — signed <?php echo e($proposal->sdao_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
            <div><strong>Reviewed By — Facilities Management:</strong> <?php echo e($proposal->fmoSigner->name ?? ($proposal->facilitiesMgmt->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->fmo_signed_at): ?> — signed <?php echo e($proposal->fmo_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
            <div><strong>Reviewed By — Academic Director:</strong> <?php echo e($proposal->academicDirectorSigner->name ?? ($proposal->academicDirector->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->academic_director_signed_at): ?> — signed <?php echo e($proposal->academic_director_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
            <div><strong>Approved By — Executive Director:</strong> <?php echo e($proposal->executiveSigner->name ?? ($proposal->executiveDirector->name ?? 'Assigned') . ' — pending'); ?> <?php if($proposal->executive_signed_at): ?> — signed <?php echo e($proposal->executive_signed_at->format('Y-m-d H:i')); ?> <?php endif; ?></div>
        </div>
        <div class="tiny text-muted mt-2">Digital signature = the approver's authenticated account confirming approval; no wet ink or physical routing required.</div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title" style="font-size:16px">Actions</h3>

        <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
        <?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>

        <?php if($proposal->status === 'rejected'): ?>
            <div class="alert alert-danger">Rejected at "<?php echo e($proposal->statusLabel()); ?>" stage by <?php echo e($proposal->rejecter->name ?? 'N/A'); ?>: <?php echo e($proposal->rejection_reason); ?></div>
        <?php endif; ?>

        <?php $user = auth()->user(); ?>

        <?php if($proposal->isAwaitingAdviser() && ($user->isAdmin() || $user->id === $proposal->adviser_id)): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.approve-adviser', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Adviser/Program Chair</button>
            </form>
        <?php endif; ?>

        <?php if($proposal->isAwaitingNoted()): ?>
            <?php if(!$proposal->department_signed_at && ($user->isAdmin() || $user->id === $proposal->department_approver_id)): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.sign-dean', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Dean/Principal</button>
            </form>
            <?php endif; ?>
            <?php if(!$proposal->sdao_signed_at && ($user->isAdmin() || $user->id === $proposal->sdao_id)): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.sign-sdao', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as SDAO</button>
            </form>
            <?php endif; ?>
            <div class="tiny text-muted mb-2">Both Dean/Principal and SDAO must sign before this moves to the Reviewed By stage.</div>
        <?php endif; ?>

        <?php if($proposal->isAwaitingReview()): ?>
            <?php if(!$proposal->fmo_signed_at && ($user->isAdmin() || $user->isFmo())): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.sign-facilities', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Facilities Management</button>
            </form>
            <?php endif; ?>
            <?php if(!$proposal->academic_director_signed_at && ($user->isAdmin() || $user->id === $proposal->academic_director_id)): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.sign-academic-director', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Academic Director</button>
            </form>
            <?php endif; ?>
            <div class="tiny text-muted mb-2">Both Facilities Management and Academic Director must sign before this moves to Executive Director for final approval.</div>
        <?php endif; ?>

        <?php if($proposal->isAwaitingExecutive() && ($user->isAdmin() || $user->id === $proposal->executive_director_id)): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.approve-executive', $proposal)); ?>" class="mb-3"><?php echo csrf_field(); ?>
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Final Approve & Confirm Venue</button>
            </form>
        <?php endif; ?>

        <?php if(!in_array($proposal->status, ['approved','rejected'])): ?>
            <?php
                $canReject = $user->isAdmin()
                    || ($proposal->isAwaitingAdviser() && $user->id === $proposal->adviser_id)
                    || ($proposal->isAwaitingNoted() && in_array($user->id, [$proposal->department_approver_id, $proposal->sdao_id]))
                    || ($proposal->isAwaitingReview() && ($user->isFmo() || $user->id === $proposal->academic_director_id))
                    || ($proposal->isAwaitingExecutive() && $user->id === $proposal->executive_director_id);
            ?>
            <?php if($canReject): ?>
            <form method="POST" action="<?php echo e(route('activity-proposals.reject', $proposal)); ?>"><?php echo csrf_field(); ?>
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="rejection_reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Proposal</button>
            </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($proposal->status === 'approved'): ?>
            <div class="alert alert-success">Fully approved. Venue is confirmed — no other reservation can be approved for this facility during this time.</div>
        <?php endif; ?>

        <a class="btn-soft w-100 justify-content-center mt-2" href="<?php echo e(route('activity-proposals.index')); ?>"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Activity Proposal Details'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/sys/capex-opex-main/resources/views/activity_proposals/show.blade.php ENDPATH**/ ?>