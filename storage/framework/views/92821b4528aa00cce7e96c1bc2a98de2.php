<input type="hidden" name="item_type" value="<?php echo e(old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX'))); ?>">
<?php ($fixedType = old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX'))); ?>
<?php ($isCapex = $fixedType === 'CAPEX'); ?>
<?php ($existingItem = ($item ?? null)); ?>
<?php ($categoryTypeMap = collect($categories ?? [])->mapWithKeys(fn($cat) => [$cat->id => (($assetTypeOptions ?? [])[$cat->name] ?? [])])); ?>
<?php ($roomsByFloor = $roomOptions ?? []); ?>
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label"><?php echo e($isCapex ? 'Asset Tag ID' : 'Item Code'); ?></label>
    <?php if($isCapex): ?>
      <input class="form-control" value="<?php echo e($existingItem->item_code ?? 'Generated automatically on save'); ?>" readonly disabled>
      <div class="tiny mt-1">Auto-generated from the selected floor — random and duplicate-checked. No typing.</div>
    <?php else: ?>
      <input name="item_code" class="form-control" value="<?php echo e(old('item_code', $item->item_code ?? ($suggestedCode ?? ''))); ?>" placeholder="Leave blank to auto-generate">
    <?php endif; ?>
  </div>
  <div class="col-md-4">
    <label class="form-label"><?php echo e($isCapex ? 'Asset Name / Model' : 'Name'); ?></label>
    <input name="name" class="form-control" value="<?php echo e(old('name', $item->name ?? '')); ?>" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Type</label>
    <input class="form-control" value="<?php echo e($fixedType); ?>" readonly>
  </div>

  <div class="col-md-6">
    <label class="form-label">Category</label>
    <select name="category_id" id="category_select" class="form-select" required>
      <option value="">Select category</option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($category->id); ?>" <?php if(old('category_id', $item->category_id ?? '') == $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="tiny mt-1">Don't see the right one? Ask your Super Admin to add it under Reference Data.</div>
  </div>
  <div class="col-md-6">
    <label class="form-label">Assigned Department</label>
    <select name="assigned_department_id" class="form-select">
      <option value="">Select department</option>
      <?php $__currentLoopData = ($departments ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($department->id); ?>" <?php if(old('assigned_department_id', $item->assigned_department_id ?? '') == $department->id): echo 'selected'; endif; ?>><?php echo e($department->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <?php if($isCapex): ?>
    <div class="col-md-4">
      <label class="form-label">Date Acquired</label>
      <input type="date" name="acquisition_date" class="form-control" value="<?php echo e(old('acquisition_date', optional($item->acquisition_date ?? null)->format('Y-m-d') ?? '')); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Floor</label>
      <?php if($existingItem?->exists): ?>
        <input class="form-control" value="<?php echo e($existingItem->floorRef->name ?? $existingItem->floor); ?>" readonly disabled>
        <input type="hidden" name="floor_id" id="floor_select" value="<?php echo e($existingItem->floor_id); ?>">
        <div class="tiny mt-1">Floor is locked after creation to keep the asset tag consistent.</div>
      <?php else: ?>
        <select name="floor_id" id="floor_select" class="form-select" required>
          <option value="">Select floor</option>
          <?php $__currentLoopData = ($floors ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floorOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($floorOption->id); ?>" <?php if(old('floor_id') == $floorOption->id): echo 'selected'; endif; ?>><?php echo e($floorOption->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      <?php endif; ?>
    </div>
    <div class="col-md-4">
      <label class="form-label">Assigned Room</label>
      <select name="room_id" id="room_select" class="form-select" required>
        <option value="">Select floor first</option>
      </select>
      <div class="tiny mt-1">Missing room? Ask your Super Admin to add it under Reference Data.</div>
    </div>
    <div class="col-md-4">
      <label class="form-label">Asset Type</label>
      <select name="asset_type_name" id="asset_type_choice" class="form-select">
        <option value="">Select category first</option>
      </select>
    </div>
    <div class="col-md-8">
      <label class="form-label">Brand</label>
      <input name="brand" class="form-control" value="<?php echo e(old('brand', $item->brand ?? '')); ?>">
    </div>
    <input type="hidden" name="quantity" value="<?php echo e(old('quantity', $item->quantity ?? 1)); ?>">
    <input type="hidden" name="unit" value="<?php echo e(old('unit', $item->unit ?? 'unit')); ?>">
    <input type="hidden" name="unit_price" value="<?php echo e(old('unit_price', $item->unit_price ?? 0)); ?>">
    <input type="hidden" name="availability_status" value="<?php echo e(old('availability_status', $item->availability_status ?? 'Available')); ?>">
    <input type="hidden" name="low_stock_threshold" value="<?php echo e(old('low_stock_threshold', $item->low_stock_threshold ?? 0)); ?>">
    <?php if (! ($existingItem?->exists)): ?>
    <div class="col-md-4">
      <label class="form-label">How Many Units?</label>
      <input type="number" name="unit_count" class="form-control" min="1" max="300" value="<?php echo e(old('unit_count', 1)); ?>" required>
      <div class="tiny mt-1">Creates that many separate assets in this same room, each with its own auto-generated asset tag ID. No need to add them one by one.</div>
    </div>
    <?php endif; ?>
    <script>
    (function () {
      const categoryTypeMap = <?php echo json_encode($categoryTypeMap, 15, 512) ?>;
      const roomsByFloor = <?php echo json_encode($roomsByFloor, 15, 512) ?>;
      const categorySelect = document.getElementById('category_select');
      const typeChoice = document.getElementById('asset_type_choice');
      const floorSelect = document.getElementById('floor_select');
      const roomSelect = document.getElementById('room_select');
      const presetAssetType = <?php echo json_encode(old('asset_type_name', $item->asset_type_name ?? ''), 512) ?>;
      const presetRoomId = <?php echo json_encode(old('room_id', $item->room_id ?? ''), 512) ?>;

      function populateTypes(catId, preselect) {
        const options = categoryTypeMap[catId] || [];
        typeChoice.innerHTML = '<option value="">Select asset type</option>';
        options.forEach(function (opt) {
          const o = document.createElement('option');
          o.value = opt; o.textContent = opt;
          if (preselect === opt) o.selected = true;
          typeChoice.appendChild(o);
        });
      }

      function populateRooms(floorId, preselect) {
        const options = roomsByFloor[floorId] || [];
        roomSelect.innerHTML = options.length ? '<option value="">Select room</option>' : '<option value="">No rooms set up for this floor yet</option>';
        options.forEach(function (room) {
          const o = document.createElement('option');
          o.value = room.id; o.textContent = room.name;
          if (String(preselect) === String(room.id)) o.selected = true;
          roomSelect.appendChild(o);
        });
      }

      if (categorySelect) {
        populateTypes(categorySelect.value, presetAssetType);
        categorySelect.addEventListener('change', function () { populateTypes(categorySelect.value, ''); });
      }
      if (floorSelect) {
        if (floorSelect.value) populateRooms(floorSelect.value, presetRoomId);
        if (floorSelect.tagName === 'SELECT') {
          floorSelect.addEventListener('change', function () { populateRooms(floorSelect.value, ''); });
        }
      }
    })();
    </script>
  <?php else: ?>
    <div class="col-md-3"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" value="<?php echo e(old('quantity', $item->quantity ?? 0)); ?>" required></div>
    <div class="col-md-3"><label class="form-label">Unit</label><input name="unit" class="form-control" value="<?php echo e(old('unit', $item->unit ?? '')); ?>" required></div>
    <div class="col-md-3"><label class="form-label">Unit Price</label><input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="<?php echo e(old('unit_price', $item->unit_price ?? 0)); ?>"></div>
    <div class="col-md-3"><label class="form-label">Brand</label><input name="brand" class="form-control" value="<?php echo e(old('brand', $item->brand ?? '')); ?>"></div>
    <div class="col-md-4"><label class="form-label">Availability</label><select name="availability_status" class="form-select"><option value="Available" <?php if(old('availability_status', $item->availability_status ?? 'Available')==='Available'): echo 'selected'; endif; ?>>Available</option><option value="Limited Stock" <?php if(old('availability_status', $item->availability_status ?? '')==='Limited Stock'): echo 'selected'; endif; ?>>Limited Stock</option><option value="Out of Stock" <?php if(old('availability_status', $item->availability_status ?? '')==='Out of Stock'): echo 'selected'; endif; ?>>Out of Stock</option></select></div>
    <div class="col-md-4"><label class="form-label">Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" value="<?php echo e(old('low_stock_threshold', $item->low_stock_threshold ?? 0)); ?>" required></div>
  <?php endif; ?>

  <div class="col-md-6"><label class="form-label">Item Image</label><input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*"><div class="tiny mt-1">Upload a JPG, PNG, or WEBP file up to 4MB.</div></div>
  <div class="col-md-6"><label class="form-label">Specifications</label><textarea name="specifications" class="form-control" rows="3"><?php echo e(old('specifications', $item->specifications ?? '')); ?></textarea></div>
  <?php if(!empty($item?->display_image)): ?>
    <div class="col-md-6">
      <label class="form-label d-block">Current Image</label>
      <img src="<?php echo e($item->display_image); ?>" alt="<?php echo e($item->name ?? 'Item image'); ?>" style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid #cfd5de;background:#fff">
      <?php if(!empty($item?->image_path)): ?>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="remove_image">
        <label class="form-check-label" for="remove_image">Remove current image</label>
      </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"><?php echo e(old('description', $item->description ?? '')); ?></textarea></div>
  <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" <?php if(old('is_active', $item->is_active ?? true)): echo 'checked'; endif; ?>><label class="form-check-label" for="is_active">Active item</label></div>
</div>
<?php /**PATH /home/claude/webfix/capex-opex/resources/views/items/form.blade.php ENDPATH**/ ?>