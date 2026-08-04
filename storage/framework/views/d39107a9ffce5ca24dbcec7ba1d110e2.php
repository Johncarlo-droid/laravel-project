<?php $__env->startSection('content'); ?>
<?php
    $isOpex = $type === 'OPEX';
    $canManage = auth()->user()->canManageInventory();
?>
<div class="module-head">
    <div>
        <h2 class="module-title"><?php echo e($isOpex ? 'OPEX Inventory' : 'CAPEX Inventory'); ?></h2>
        <div class="module-note"><?php echo e($isOpex ? 'Track supplies and stock levels for request processing' : 'Inventory monitoring only for NU Clark CAPEX assets'); ?></div>
    </div>
    <?php if($canManage): ?>
        <a href="<?php echo e(route('items.create', ['type' => $type])); ?>" class="btn-primaryx"><i class="bi bi-plus-lg"></i> <?php echo e($isOpex ? 'Add OPEX Item' : 'Add CAPEX Item'); ?></a>
    <?php endif; ?>
</div>
<div class="surface p-3">
    <form method="GET" class="search-strip mb-3">
        <input type="hidden" name="type" value="<?php echo e($type); ?>">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e($isOpex ? 'Search by item name, code, brand, or specs...' : 'Search by asset name, code, or room...'); ?>">
        <div class="filter-box"><i class="bi bi-funnel text-muted"></i><select name="stock_filter" onchange="this.form.submit()"><option value="">All</option><?php if($isOpex): ?><option value="available" <?php if(($stockFilter ?? '') === 'available'): echo 'selected'; endif; ?>>Available</option><option value="low" <?php if(($stockFilter ?? '') === 'low'): echo 'selected'; endif; ?>>Limited Stock</option><?php if($canManage): ?><option value="out" <?php if(($stockFilter ?? '') === 'out'): echo 'selected'; endif; ?>>Out of Stock</option><?php endif; ?> <?php else: ?><option value="active" <?php if(($stockFilter ?? '') === 'active'): echo 'selected'; endif; ?>>Active Only</option><?php endif; ?></select></div>
        <?php if(!$isOpex): ?>
        <div class="filter-box"><i class="bi bi-layers-half text-muted"></i><select name="floor_id" onchange="this.form.submit()">
            <option value="">All Floors</option>
            <?php $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floorOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($floorOption->id); ?>" <?php if(($floorFilter ?? '') == $floorOption->id): echo 'selected'; endif; ?>><?php echo e($floorOption->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select></div>
        <div class="filter-box"><i class="bi bi-door-open text-muted"></i><select name="room_id" onchange="this.form.submit()">
            <option value="">All Rooms</option>
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roomOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($roomOption->id); ?>" <?php if(($roomFilter ?? '') == $roomOption->id): echo 'selected'; endif; ?>><?php echo e($roomOption->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select></div>
        <?php endif; ?>
        <button class="btn-primaryx small-btn" type="submit">Apply</button>
    </form>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th><?php echo e($isOpex ? 'Item' : 'Asset'); ?></th>
                    <th>Details</th>
                    <th><?php echo e($isOpex ? 'Category / Brand' : 'Category'); ?></th>
                    <th><?php echo e($isOpex ? 'Price' : 'QR / Code'); ?></th>
                    <th><?php echo e($isOpex ? 'Stock' : 'Assigned Room'); ?></th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td data-label="<?php echo e($isOpex ? 'Item' : 'Asset'); ?>">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?php echo e($item->display_image); ?>" alt="<?php echo e($item->name); ?>" style="width:64px;height:64px;object-fit:cover;border-radius:12px;border:1px solid #cfd5de">
                                <div>
                                    <div style="font-weight:700;font-size:12px"><?php echo e($item->name); ?></div>
                                    <div class="tiny-2"><?php echo e($item->item_code); ?></div>
                                    <div class="tiny-2">Unit: <?php echo e($item->unit); ?></div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Details"><?php echo e($item->specifications ?: ($item->description ?: '-')); ?></td>
                        <td data-label="<?php echo e($isOpex ? 'Category / Brand' : 'Category'); ?>"><?php echo e($item->category->name ?? 'Uncategorized'); ?><div class="tiny-2"><?php echo e($item->brand ?: 'No brand'); ?></div></td>
                        <td data-label="<?php echo e($isOpex ? 'Price' : 'QR / Code'); ?>"><?php echo e($isOpex ? '₱'.number_format((float) $item->unit_price, 2) : ($item->qr_value ?: $item->item_code)); ?></td>
                        <td data-label="<?php echo e($isOpex ? 'Stock' : 'Assigned Room'); ?>"><?php if($isOpex): ?><div style="font-weight:700"><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></div><div class="tiny-2">Threshold: <?php echo e($item->low_stock_threshold); ?></div><?php else: ?> <?php echo e($item->room_assigned ?: 'Not assigned'); ?> <?php endif; ?></td>
                        <td data-label="Status">
                            <?php if($isOpex): ?>
                                <span class="status <?php echo e($item->isOutOfStock() ? 'maintenance' : ($item->isLimitedStock() ? 'low' : 'available')); ?>"><?php echo e($item->availability_status); ?></span>
                            <?php else: ?>
                                <span class="status <?php echo e($item->is_active ? 'available' : 'maintenance'); ?>"><?php echo e($item->is_active ? 'Active' : 'Inactive'); ?></span>
                            <?php endif; ?>
                        </td>
<td class="text-end" data-label="Actions">
    <a class="btn-soft small-btn" href="<?php echo e(route('items.show', $item)); ?>">
        <i class="bi bi-eye"></i>
    </a>

    <?php if(!$isOpex): ?>
        <a class="btn-soft small-btn" target="_blank" href="<?php echo e(route('qr.show', $item)); ?>">
            <i class="bi bi-qr-code"></i>
        </a>
    <?php endif; ?>

    <?php if($canManage): ?>
        <a class="btn-soft small-btn" href="<?php echo e(route('items.edit', $item)); ?>">
            <i class="bi bi-pencil"></i>
        </a>

        <form class="d-inline" method="POST" action="<?php echo e(route('items.destroy', $item)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="btn-soft small-btn" onclick="return confirm('Delete item?')">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    <?php elseif($isOpex && auth()->user()->isRequestor()): ?>
        <a class="btn-approve small-btn" href="<?php echo e(route('requisitions.create', ['item_id' => $item->id])); ?>">
            <i class="bi bi-cart-plus"></i>
        </a>
    <?php endif; ?>
</td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="empty-state">No <?php echo e(strtolower($isOpex ? 'opex items' : 'capex assets')); ?> available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($items->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $type === 'OPEX' ? 'OPEX Inventory' : 'CAPEX Inventory'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\capex-opex-web-system\capex-opex\resources\views/items/index.blade.php ENDPATH**/ ?>