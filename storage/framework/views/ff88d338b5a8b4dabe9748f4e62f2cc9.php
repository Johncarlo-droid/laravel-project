<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">Reference Data</h2>
        <div class="module-note">Floors, Rooms, Categories, and Asset Types are managed here by Super Admin only. Everyone else picks from these lists when adding items — they can't type in new ones on the fly anymore.</div>
    </div>
</div>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>

<div class="panel-grid-2">
    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-layers-half"></i> Floors</h3>
        <form method="POST" action="<?php echo e(route('reference-data.floors.store')); ?>" class="row g-2 mb-3">
            <?php echo csrf_field(); ?>
            <div class="col-7"><input name="name" class="form-control" placeholder="e.g. 9th Floor" required></div>
            <div class="col-3"><input name="sort_order" type="number" class="form-control" placeholder="Order" min="0"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Floor</th><th>Rooms</th><th>Assets</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td data-label="Floor"><?php echo e($floor->name); ?></td>
                        <td data-label="Rooms"><?php echo e($floor->rooms_count); ?></td>
                        <td data-label="Assets"><?php echo e($floor->items_count); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('reference-data.floors.destroy', $floor)); ?>" onsubmit="return confirm('Remove <?php echo e($floor->name); ?>?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="empty-state">No floors yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-door-open"></i> Rooms</h3>
        <form method="POST" action="<?php echo e(route('reference-data.rooms.store')); ?>" class="row g-2 mb-3">
            <?php echo csrf_field(); ?>
            <div class="col-4">
                <select name="floor_id" class="form-select" required>
                    <option value="">Floor</option>
                    <?php $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($floor->id); ?>"><?php echo e($floor->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-4"><input name="name" class="form-control" placeholder="e.g. 719 or Server Room" required></div>
            <div class="col-2"><input name="code" class="form-control" placeholder="Code"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Room</th><th>Floor</th><th>Assets</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td data-label="Room"><?php echo e($room->name); ?> <?php if($room->code): ?><span class="tiny-2">(<?php echo e($room->code); ?>)</span><?php endif; ?></td>
                        <td data-label="Floor"><?php echo e($room->floor->name ?? 'N/A'); ?></td>
                        <td data-label="Assets"><?php echo e($room->items_count); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('reference-data.rooms.destroy', $room)); ?>" onsubmit="return confirm('Remove <?php echo e($room->name); ?>?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="empty-state">No rooms yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="panel-grid-2">
    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-tags"></i> Categories</h3>
        <form method="POST" action="<?php echo e(route('reference-data.categories.store')); ?>" class="row g-2 mb-3">
            <?php echo csrf_field(); ?>
            <div class="col-5"><input name="name" class="form-control" placeholder="e.g. Electronics" required></div>
            <div class="col-5"><input name="description" class="form-control" placeholder="Description (optional)"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Category</th><th>Items</th><th>Asset Types</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td data-label="Category"><?php echo e($category->name); ?></td>
                        <td data-label="Items"><?php echo e($category->items_count); ?></td>
                        <td data-label="Asset Types"><?php echo e($category->asset_types_count); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('reference-data.categories.destroy', $category)); ?>" onsubmit="return confirm('Remove <?php echo e($category->name); ?>?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="empty-state">No categories yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-diagram-2"></i> Asset Types</h3>
        <form method="POST" action="<?php echo e(route('reference-data.asset-types.store')); ?>" class="row g-2 mb-3">
            <?php echo csrf_field(); ?>
            <div class="col-5">
                <select name="item_category_id" class="form-select" required>
                    <option value="">Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-5"><input name="name" class="form-control" placeholder="e.g. Laptop" required></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Asset Type</th><th>Category</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td data-label="Asset Type"><?php echo e($type->name); ?></td>
                        <td data-label="Category"><?php echo e($type->category->name ?? 'N/A'); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('reference-data.asset-types.destroy', $type)); ?>" onsubmit="return confirm('Remove <?php echo e($type->name); ?>?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" class="empty-state">No asset types yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="surface p-3">
    <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-building"></i> Departments</h3>
    <div class="tiny mb-2">Full department management (with capex/opex budget limits) lives on its own page.</div>
    <a href="<?php echo e(route('departments.index')); ?>" class="btn-soft small-btn"><i class="bi bi-arrow-right"></i> Open Departments</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Reference Data'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\capex-opex-web-system\capex-opex\resources\views/reference-data/index.blade.php ENDPATH**/ ?>