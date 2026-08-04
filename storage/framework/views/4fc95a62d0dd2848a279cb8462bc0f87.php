<?php $__env->startSection('content'); ?>
<div class="form-shell">
  <div class="module-head"><div><h2 class="module-title">School Facilities Reservation — Digital Proposal</h2><div class="module-note">Same fields as the paper form, now routed digitally: Prepared By (you + Adviser) → Noted By (Dean/Principal &amp; SDAO) → Reviewed By (Facilities Mgmt. &amp; Academic Director) → Approved By (Executive Director). No physical routing needed.</div></div></div>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('activity-proposals.store')); ?>"><?php echo csrf_field(); ?>
    <h3 class="module-title" style="font-size:14px">Activity Details</h3>
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Name of Organization / Department / College</label>
        <input name="organization_name" class="form-control" value="<?php echo e(old('organization_name')); ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Position</label>
        <input name="requester_position" class="form-control" value="<?php echo e(old('requester_position')); ?>" placeholder="e.g. President">
      </div>
      <div class="col-md-3">
        <label class="form-label">Expected Attendees</label>
        <input type="number" min="1" name="participants_count" class="form-control" value="<?php echo e(old('participants_count')); ?>" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Title of Activity</label>
        <input name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Day(s) of Activity</label>
        <input name="activity_days" class="form-control" value="<?php echo e(old('activity_days')); ?>" placeholder="e.g. Monday">
      </div>
      <div class="col-md-3">
        <label class="form-label">Name of Speaker (if applicable)</label>
        <input name="speaker_name" class="form-control" value="<?php echo e(old('speaker_name')); ?>">
      </div>

      <div class="col-md-3">
        <label class="form-label">Start Date and Time</label>
        <input type="datetime-local" name="start_at" class="form-control" value="<?php echo e(old('start_at')); ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date and Time</label>
        <input type="datetime-local" name="end_at" class="form-control" value="<?php echo e(old('end_at')); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Venue</label>
        <select name="facility_id" id="facility_select" class="form-select" required>
          <option value="">Select venue</option>
          <?php $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($facility->id); ?>" <?php if(old('facility_id') == $facility->id): echo 'selected'; endif; ?>><?php echo e($facility->name); ?> — <?php echo e($facility->location); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="text" name="venue_other_note" id="venue_other_note" class="form-control mt-2 d-none" placeholder="Specify venue" value="<?php echo e(old('venue_other_note')); ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Other Items Needed and Services</label>
        <div class="d-flex flex-wrap gap-3">
          <?php $__currentLoopData = $equipmentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="tiny d-flex align-items-center gap-1">
              <input type="checkbox" name="equipment_needed[]" value="<?php echo e($option); ?>" <?php if(collect(old('equipment_needed', []))->contains($option)): echo 'checked'; endif; ?>>
              <?php echo e($option); ?>

            </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <div class="col-12">
        <label class="form-label">Program Flow</label>
        <textarea name="program_flow" class="form-control" rows="5" placeholder="e.g. 8:00 AM Registration, 8:30 AM Opening Program, 9:00 AM Main Activity ..."><?php echo e(old('program_flow')); ?></textarea>
      </div>
    </div>

    <h3 class="module-title" style="font-size:14px">Signature Routing</h3>
    <div class="module-note mb-2">Pick who should digitally sign at each stage — replaces walking the paper form around campus.</div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Prepared By: Adviser / Program Chair</label>
        <select name="adviser_id" class="form-select" required>
          <option value="">Select adviser</option>
          <?php $__currentLoopData = $advisers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('adviser_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Noted By: Dean / Principal</label>
        <select name="department_approver_id" class="form-select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $deansPrincipals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('department_approver_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Noted By: SDAO</label>
        <select name="sdao_id" class="form-select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $sdaoOfficers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('sdao_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Reviewed By: Facilities Management</label>
        <select name="facilities_mgmt_id" class="form-select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $facilitiesStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('facilities_mgmt_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Reviewed By: Academic Director</label>
        <select name="academic_director_id" class="form-select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $academicDirectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('academic_director_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Approved By: Executive Director</label>
        <select name="executive_director_id" class="form-select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $executiveDirectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($person->id); ?>" <?php if(old('executive_director_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn-primaryx">Submit Proposal</button>
      <a class="btn-soft" href="<?php echo e(route('activity-proposals.index')); ?>">Cancel</a>
    </div>
  </form>
</div>
<script>
(function () {
  var select = document.getElementById('facility_select');
  var other = document.getElementById('venue_other_note');
  function sync() {
    var text = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '';
    if (text.indexOf('Others') !== -1) { other.classList.remove('d-none'); } else { other.classList.add('d-none'); other.value = ''; }
  }
  select.addEventListener('change', sync);
  sync();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'New Activity Proposal'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/sys/capex-opex-main/resources/views/activity_proposals/create.blade.php ENDPATH**/ ?>