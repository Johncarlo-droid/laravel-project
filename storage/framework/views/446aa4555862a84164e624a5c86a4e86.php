<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title"><?php echo e($item->name); ?></h2>
        <div class="module-note">Detailed CAPEX/OPEX inventory record</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if(auth()->user()->canManageInventory()): ?>
        <a class="btn-primaryx" href="<?php echo e(route('items.edit', $item)); ?>"><i class="bi bi-pencil"></i> Edit Item</a>
        <?php endif; ?>
        <?php if($item->item_type === 'OPEX' && !auth()->user()->canManageInventory()): ?>
        <a class="btn-approve" href="<?php echo e(route('requisitions.create', ['item_id' => $item->id])); ?>"><i class="bi bi-file-earmark-plus"></i> Create Request</a>
        <?php endif; ?>
    </div>
</div>
<div class="panel-grid-2">
    <div class="surface p-3">
        <img src="<?php echo e($item->display_image); ?>" alt="<?php echo e($item->name); ?>" style="width:100%;max-height:320px;object-fit:cover;border-radius:16px;border:1px solid #cfd5de;margin-bottom:16px;">
        <h3 class="module-title mb-3" style="font-size:16px">Item Profile</h3>
        <div class="row g-3">
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2"><?php echo e($item->item_type === 'CAPEX' ? 'Asset Tag ID' : 'Item Code'); ?></div><div class="fw-bold"><?php echo e($item->asset_tag_id); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Type</div><div class="fw-bold"><?php echo e($item->item_type); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Category</div><div class="fw-bold"><?php echo e($item->category->name ?? 'Uncategorized'); ?></div></div></div>
            <?php if($item->item_type === 'CAPEX'): ?>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Date Acquired</div><div class="fw-bold"><?php echo e($item->date_acquired_label); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Department</div><div class="fw-bold"><?php echo e($item->department_label); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Asset Type</div><div class="fw-bold"><?php echo e($item->asset_type_label); ?></div></div></div>
            <?php endif; ?>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Brand</div><div class="fw-bold"><?php echo e($item->brand ?: 'No brand'); ?></div></div></div>
            <?php if($item->item_type !== 'CAPEX'): ?>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Unit</div><div class="fw-bold"><?php echo e($item->unit); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Unit Price</div><div class="fw-bold">₱<?php echo e(number_format((float)$item->unit_price, 2)); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Available Quantity</div><div class="fw-bold"><?php echo e($item->quantity); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Availability</div><div class="fw-bold"><?php echo e($item->availability_status); ?></div></div></div>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Low Stock Threshold</div><div class="fw-bold"><?php echo e($item->low_stock_threshold); ?></div></div></div>
            <?php endif; ?>
            <div class="col-md-6"><div class="report-stat"><div class="tiny-2">Assigned Room</div><div class="fw-bold"><?php echo e($item->room_assigned ?: 'Not assigned'); ?></div></div></div>
        </div>
    </div>
    <div class="surface p-3">
        <h3 class="module-title mb-3" style="font-size:16px">Details</h3>
        <div class="mb-3">
            <label class="form-label">Specifications</label>
            <div class="form-control" style="min-height:96px;background:#f8fafc"><?php echo e($item->specifications ?: 'No specifications available.'); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <div class="form-control" style="min-height:96px;background:#f8fafc"><?php echo e($item->description ?: 'No description available.'); ?></div>
        </div>
        <div class="settings-list">
            <div class="settings-item"><h5>Status</h5><p class="tiny mb-0"><?php echo e($item->is_active ? 'This item is active in inventory.' : 'This item is inactive in inventory.'); ?></p></div>
            <div class="settings-item"><h5>Request visibility</h5><p class="tiny mb-0">Out of stock OPEX items are hidden from requestor accounts.</p></div>
            <div class="settings-item"><h5>Inventory summary</h5><p class="tiny mb-0">Brand, pricing, specs, and image are now shown for easier identification.</p></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $item->item_type === 'OPEX' ? 'OPEX Item Details' : 'CAPEX Item Details'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/items/show.blade.php ENDPATH**/ ?>