<?php $__env->startSection('content'); ?>
<div class="module-head">
  <div><h2 class="module-title">Facilities Management Office</h2><div class="module-note">Digital venue reservations with schedule conflict detection and approval workflow.</div></div>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn-primaryx" href="<?php echo e(route('facilities.reserve')); ?>"><i class="bi bi-calendar-plus"></i> Reserve Facility</a>
    <?php if(auth()->user()->canManageFacilities()): ?><a class="btn-primaryx" href="<?php echo e(route('facilities.create')); ?>"><i class="bi bi-building-add"></i> Add Facility</a><?php endif; ?>
  </div>
</div>

<div class="surface p-3 mb-3">
  <form class="search-strip" method="GET">
    <i class="bi bi-search"></i><input class="search-input" name="search" value="<?php echo e($search); ?>" placeholder="Search facility, code, or location">
    <button class="btn-soft small-btn" type="submit">Search</button>
  </form>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Code</th><th>Facility</th><th>Location</th><th>Capacity</th><th>Resources</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($facility->code); ?></td><td><?php echo e($facility->name); ?></td><td><?php echo e($facility->location ?? 'N/A'); ?></td><td><?php echo e($facility->capacity); ?></td><td><?php echo e($facility->resources ?? 'N/A'); ?></td>
          <td><span class="status <?php echo e($facility->is_active ? 'approved' : 'low'); ?>"><?php echo e($facility->is_active ? 'Active' : 'Inactive'); ?></span></td>
          <td><?php if(auth()->user()->canManageFacilities()): ?><a href="<?php echo e(route('facilities.edit', $facility)); ?>" class="btn-soft small-btn">Edit</a><?php else: ?> — <?php endif; ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" class="empty-state">No facilities found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3"><?php echo e($facilities->links()); ?></div>
</div>

<div class="surface p-3">
  <div class="module-head mb-2"><div><h2 class="module-title">Reservation Requests</h2><div class="module-note">Pending and approved schedules are checked to prevent overlapping reservations.</div></div></div>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>No.</th><th>Requester</th><th>Facility</th><th>Title</th><th>Schedule</th><th>Status</th><th>FMO Action</th></tr></thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($reservation->reservation_no); ?></td>
          <td><?php echo e($reservation->user->name ?? 'N/A'); ?></td>
          <td><?php echo e($reservation->facility->name ?? 'N/A'); ?></td>
          <td><?php echo e($reservation->title); ?><div class="tiny"><?php echo e($reservation->purpose); ?></div></td>
          <td><?php echo e($reservation->start_at->format('M d, Y h:i A')); ?><br><span class="tiny">to <?php echo e($reservation->end_at->format('M d, Y h:i A')); ?></span></td>
          <td><span class="status <?php echo e($reservation->status === 'approved' ? 'approved' : ($reservation->status === 'rejected' ? 'low' : 'pending')); ?>"><?php echo e(ucfirst($reservation->status)); ?></span></td>
          <td>
            <?php if(auth()->user()->canManageFacilities() && $reservation->status === 'pending'): ?>
              <form class="d-inline" method="POST" action="<?php echo e(route('facilities.reservations.approve', $reservation)); ?>"><?php echo csrf_field(); ?><button class="btn-approve">Approve</button></form>
              <form class="d-inline" method="POST" action="<?php echo e(route('facilities.reservations.reject', $reservation)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="rejection_reason" value="Schedule or resource unavailable"><button class="btn-reject">Reject</button></form>
            <?php else: ?>
              <span class="tiny"><?php echo e($reservation->reviewer->name ?? 'No action yet'); ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" class="empty-state">No reservation requests yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3"><?php echo e($reservations->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Facilities Management'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/sys/capex-opex-main/resources/views/facilities/index.blade.php ENDPATH**/ ?>