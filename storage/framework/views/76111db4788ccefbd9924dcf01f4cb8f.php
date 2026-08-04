<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Mismatch Detection and Monitoring</h2><div class="module-note">Scanning is performed exclusively through the mobile app by housekeeping/asset custodians (GPS + QR code). This page is a read-only monitoring view of everything they scan.</div></div></div>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

<?php if($unresolvedCount > 0): ?>
<div class="alert alert-danger"><?php echo e($unresolvedCount); ?> unresolved mismatch(es) — an asset was scanned somewhere other than its assigned room and hasn't been relocated/confirmed yet.</div>
<?php endif; ?>

<div class="page-tabs mb-3">
  <a href="<?php echo e(route('asset-scans.index', ['filter' => 'all'])); ?>" class="<?php echo e($filter === 'all' ? 'active' : ''); ?>">All Scans</a>
  <a href="<?php echo e(route('asset-scans.index', ['filter' => 'unresolved'])); ?>" class="<?php echo e($filter === 'unresolved' ? 'active' : ''); ?>">Unresolved Mismatches</a>
  <a href="<?php echo e(route('asset-scans.index', ['filter' => 'resolved'])); ?>" class="<?php echo e($filter === 'resolved' ? 'active' : ''); ?>">Resolved</a>
  <a href="<?php echo e(route('asset-scans.index', ['filter' => 'matched'])); ?>" class="<?php echo e($filter === 'matched' ? 'active' : ''); ?>">Matched</a>
</div>

<form method="GET" class="search-strip mb-3">
  <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
  <div class="filter-box"><i class="bi bi-door-open text-muted"></i><select name="room" onchange="this.form.submit()">
    <option value="">All Rooms</option>
    <?php $__currentLoopData = $roomOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roomName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($roomName); ?>" <?php if($roomFilter === $roomName): echo 'selected'; endif; ?>><?php echo e($roomName); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select></div>
  <button class="btn-primaryx small-btn" type="submit">Apply</button>
</form>

<div class="data-panel">
  <div class="table-responsive"><table class="data-table"><thead><tr><th>Date</th><th>Asset</th><th>Expected Room</th><th>Scanned Room</th><th>GPS Distance</th><th>Status</th><th>Scanned By</th><th>Notes</th><th></th></tr></thead><tbody>
  <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td><?php echo e($log->created_at->format('M d, Y h:i A')); ?></td>
      <td><?php echo e($log->item->item_code ?? 'N/A'); ?><div class="tiny"><?php echo e($log->item->name ?? ''); ?></div></td>
      <td><?php echo e($log->expected_room ?: 'N/A'); ?></td>
      <td><?php echo e($log->scanned_room ?: 'N/A'); ?></td>
      <td><?php echo e($log->distance_meters !== null ? $log->distance_meters.' m' : '—'); ?></td>
      <td>
        <span class="status <?php echo e($log->status === 'matched' ? 'approved' : ($log->resolved_at ? 'pending' : 'low')); ?>">
          <?php echo e($log->status === 'matched' ? 'Matched' : ($log->resolved_at ? 'Mismatch (Resolved)' : 'Mismatch (Open)')); ?>

        </span>
        <?php if($log->resolved_at): ?><div class="tiny">by <?php echo e($log->resolver->name ?? 'N/A'); ?> on <?php echo e($log->resolved_at->format('M d, h:i A')); ?></div><?php endif; ?>
      </td>
      <td><?php echo e($log->user->name ?? 'System'); ?></td>
      <td><?php echo e($log->notes); ?></td>
      <td>
        <?php if($log->isUnresolvedMismatch()): ?>
        <form method="POST" action="<?php echo e(route('asset-scans.resolve', $log)); ?>"><?php echo csrf_field(); ?>
          <button class="btn-soft small-btn"><i class="bi bi-check-lg"></i> Mark Resolved</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="9" class="empty-state">No scan logs yet. Scans will appear here once housekeeping starts scanning assets on mobile.</td></tr><?php endif; ?>
  </tbody></table></div>
  <div class="mt-3"><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Asset Scan Monitoring'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/asset_scans/index.blade.php ENDPATH**/ ?>