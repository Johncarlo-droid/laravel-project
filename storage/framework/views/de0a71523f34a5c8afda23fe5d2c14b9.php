<?php $__env->startSection('content'); ?>
<div class="module-head">
  <div><h2 class="module-title">Linear Regression-Based Consumption Forecasting</h2><div class="module-note">Forecasts future OPEX consumption from monthly historical usage data.</div></div>
</div>

<div class="surface p-3 mb-3">
  <form method="GET" class="row g-3 align-items-end">
    <div class="col-md-8"><label class="form-label">OPEX Item</label><select name="item_id" class="form-select"><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($item->id); ?>" <?php if($selectedItem?->id === $item->id): echo 'selected'; endif; ?>><?php echo e($item->item_code); ?> — <?php echo e($item->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    <div class="col-md-4"><button class="btn-primaryx"><i class="bi bi-graph-up-arrow"></i> Compute Forecast</button></div>
  </form>
</div>

<?php if($selectedItem): ?>
<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>

<?php if($forecast): ?>
<div class="surface p-3 mb-3">
  <h2 class="module-title mb-2">Log Historical Usage</h2>
  <div class="module-note mb-2">
    <?php if(!$forecast['ready']): ?>
      The regression needs at least two different calendar months of usage data before it can compute a trend. Rather than waiting for real time to pass, record past months' usage directly — this is standard practice for bootstrapping a forecasting model with historical data, the same way any live system would once it has been in use for a while.
    <?php else: ?>
      Add another month's figures anytime to extend the trend line.
    <?php endif; ?>
  </div>
  <form method="POST" action="<?php echo e(route('forecast.usage-logs.store')); ?>" class="row g-3 align-items-end">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="item_id" value="<?php echo e($selectedItem->id); ?>">
    <div class="col-md-4">
      <label class="form-label">Usage Month</label>
      <input type="date" name="usage_date" class="form-control" max="<?php echo e(now()->toDateString()); ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Quantity Used</label>
      <input type="number" name="quantity_used" class="form-control" min="1" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Remarks (optional)</label>
      <input type="text" name="remarks" class="form-control" placeholder="e.g. April consumption">
    </div>
    <div class="col-md-2">
      <button class="btn-primaryx w-100"><i class="bi bi-plus-lg"></i> Add</button>
    </div>
  </form>
  <?php if(!$forecast['ready']): ?>
  <div class="tiny mt-2">Tip: add one entry dated last month and one dated this month (or any two different months) to instantly have enough data for a demo-ready forecast.</div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="panel-grid-2">
  <div class="data-panel">
    <h2 class="module-title mb-2">Historical Monthly Usage</h2>
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>x</th><th>Month</th><th>Usage (y)</th><th>x²</th><th>xy</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $forecast['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr><td><?php echo e($point['x']); ?></td><td><?php echo e($point['period']); ?></td><td><?php echo e($point['y']); ?></td><td><?php echo e($point['x'] * $point['x']); ?></td><td><?php echo e($point['x'] * $point['y']); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5" class="empty-state">No usage data yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="data-panel">
    <h2 class="module-title mb-2">Forecast Result</h2>
    <?php if(!$forecast['ready']): ?>
      <div class="alert alert-warning"><?php echo e($forecast['message']); ?></div>
    <?php else: ?>
      <div class="row g-3">
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Σx</div><div class="fs-4 fw-bold"><?php echo e($forecast['sumX']); ?></div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Σy</div><div class="fs-4 fw-bold"><?php echo e($forecast['sumY']); ?></div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Slope (b)</div><div class="fs-4 fw-bold"><?php echo e($forecast['b']); ?></div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Intercept (a)</div><div class="fs-4 fw-bold"><?php echo e($forecast['a']); ?></div></div></div>
      </div>
      <hr>
      <p class="mb-1"><strong>Equation:</strong> y = <?php echo e($forecast['a']); ?> + <?php echo e($forecast['b']); ?>x</p>
      <p class="mb-1"><strong>Next period x:</strong> <?php echo e($forecast['nextX']); ?></p>
      <p class="mb-1"><strong>Forecasted demand:</strong> <?php echo e($forecast['predicted']); ?> <?php echo e($selectedItem->unit); ?></p>
      <p class="mb-1"><strong>Current stock:</strong> <?php echo e($forecast['currentStock']); ?> <?php echo e($selectedItem->unit); ?></p>
      <p class="mb-1"><strong>Suggested restock:</strong> <span class="status <?php echo e($forecast['suggestedRestock'] > 0 ? 'low' : 'approved'); ?>"><?php echo e($forecast['suggestedRestock']); ?> <?php echo e($selectedItem->unit); ?></span></p>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Linear Regression Forecasting'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\System Demo\resources\views/forecast/index.blade.php ENDPATH**/ ?>