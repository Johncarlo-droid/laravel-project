<?php $__env->startSection('content'); ?>
<div class="form-shell">
  <div class="module-head"><div><h2 class="module-title">Facility Reservation Form</h2><div class="module-note">The system will block the request when it overlaps with an existing pending or approved schedule.</div></div></div>
  <form method="POST" action="<?php echo e(route('facilities.reservations.store')); ?>"><?php echo csrf_field(); ?>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Facility</label><select name="facility_id" class="form-select" required><option value="">Select facility</option><?php $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($facility->id); ?>" <?php if(old('facility_id') == $facility->id): echo 'selected'; endif; ?>><?php echo e($facility->name); ?> — <?php echo e($facility->location); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      <div class="col-md-6"><label class="form-label">Activity / Event Title</label><input name="title" class="form-control" value="<?php echo e(old('title')); ?>" required></div>
      <div class="col-md-6"><label class="form-label">Start Date and Time</label><input type="datetime-local" name="start_at" class="form-control" value="<?php echo e(old('start_at')); ?>" required></div>
      <div class="col-md-6"><label class="form-label">End Date and Time</label><input type="datetime-local" name="end_at" class="form-control" value="<?php echo e(old('end_at')); ?>" required></div>
      <div class="col-md-6"><label class="form-label">Purpose</label><textarea name="purpose" class="form-control" rows="4"><?php echo e(old('purpose')); ?></textarea></div>
      <div class="col-md-6"><label class="form-label">Resources Needed</label><textarea name="resources_needed" class="form-control" rows="4" placeholder="Chairs, tables, projector, microphone, etc."><?php echo e(old('resources_needed')); ?></textarea></div>
    </div>
    <div class="mt-3 d-flex gap-2"><button class="btn-primaryx">Submit Reservation</button><a class="btn-soft" href="<?php echo e(route('facilities.index')); ?>">Cancel</a></div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Reserve Facility'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/sys/capex-opex-main/resources/views/facilities/reserve.blade.php ENDPATH**/ ?>