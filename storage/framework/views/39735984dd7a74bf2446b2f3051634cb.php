<?php $__env->startSection('content'); ?>
<div class="module-head"><div><h2 class="module-title">Mismatch Detection and Reporting</h2><div class="module-note">Manual web version for QR scan validation. Mobile app/API can submit the same fields later.</div></div></div>

<div class="surface p-3 mb-3">
  <form method="POST" action="<?php echo e(route('asset-scans.store')); ?>"><?php echo csrf_field(); ?>
    <div class="row g-3 align-items-end">
      <div class="col-md-5"><label class="form-label">CAPEX Asset</label><select name="item_id" class="form-select" required><option value="">Select scanned asset</option><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($item->id); ?>"><?php echo e($item->item_code); ?> — <?php echo e($item->name); ?> (Assigned: <?php echo e($item->room_assigned ?: 'N/A'); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      <div class="col-md-3"><label class="form-label">Current / Scanned Room</label><input name="scanned_room" class="form-control" placeholder="Example: 719" required></div>
      <div class="col-md-2"><label class="form-label">Latitude</label><input name="latitude" class="form-control" placeholder="optional"></div>
      <div class="col-md-2"><label class="form-label">Longitude</label><input name="longitude" class="form-control" placeholder="optional"></div>
      <div class="col-12"><label class="form-label">Notes</label><input name="notes" class="form-control" placeholder="Optional remarks from housekeeping/admin"></div>
      <div class="col-12"><button class="btn-primaryx"><i class="bi bi-qr-code-scan"></i> Save Scan Result</button></div>
    </div>
  </form>
</div>

<div class="data-panel">
  <div class="table-responsive"><table class="data-table"><thead><tr><th>Date</th><th>Asset</th><th>Expected Room</th><th>Scanned Room</th><th>Status</th><th>Scanned By</th><th>Notes</th></tr></thead><tbody>
  <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr><td><?php echo e($log->created_at->format('M d, Y h:i A')); ?></td><td><?php echo e($log->item->item_code ?? 'N/A'); ?><div class="tiny"><?php echo e($log->item->name ?? ''); ?></div></td><td><?php echo e($log->expected_room ?: 'N/A'); ?></td><td><?php echo e($log->scanned_room ?: 'N/A'); ?></td><td><span class="status <?php echo e($log->status === 'matched' ? 'approved' : 'low'); ?>"><?php echo e(ucfirst($log->status)); ?></span></td><td><?php echo e($log->user->name ?? 'System'); ?></td><td><?php echo e($log->notes); ?></td></tr>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" class="empty-state">No scan logs yet.</td></tr><?php endif; ?>
  </tbody></table></div>
  <div class="mt-3"><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Asset Scan Monitoring'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\System Demo\resources\views/asset_scans/index.blade.php ENDPATH**/ ?>