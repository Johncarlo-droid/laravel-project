<input type="hidden" name="item_type" value="{{ old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX')) }}">
@php($fixedType = old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX')))
@php($isCapex = $fixedType === 'CAPEX')
@php($existingItem = ($item ?? null))
@php($categoryTypeMap = collect($categories ?? [])->mapWithKeys(fn($cat) => [$cat->id => (($assetTypeOptions ?? [])[$cat->name] ?? [])]))
@php($roomsByFloor = $roomOptions ?? [])
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">{{ $isCapex ? 'Asset Tag ID' : 'Item Code' }}</label>
    @if($isCapex)
      <input class="form-control" value="{{ $existingItem->item_code ?? 'Generated automatically on save' }}" readonly disabled>
      <div class="tiny mt-1">Auto-generated from the selected floor — random and duplicate-checked. No typing.</div>
    @else
      <input name="item_code" class="form-control" value="{{ old('item_code', $item->item_code ?? ($suggestedCode ?? '')) }}" placeholder="Leave blank to auto-generate">
    @endif
  </div>
  <div class="col-md-4">
    <label class="form-label">{{ $isCapex ? 'Asset Name / Model' : 'Name' }}</label>
    <input name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Type</label>
    <input class="form-control" value="{{ $fixedType }}" readonly>
  </div>

  <div class="col-md-6">
    @if($isCapex)
      <label class="form-label">Category</label>
      <div id="category_chips" class="picker-chip-row"></div>
      <div id="asset_type_wrap" style="display:none" class="mt-2">
        <label class="form-label">Asset Type</label>
        <select id="asset_type_select" class="form-select" required>
          <option value="">Select asset type</option>
        </select>
      </div>
      <input type="hidden" name="category_id" id="category_select" value="{{ old('category_id', $item->category_id ?? '') }}">
      <input type="hidden" name="asset_type_name" id="asset_type_choice" value="{{ old('asset_type_name', $item->asset_type_name ?? '') }}">
      <div class="tiny mt-1">Don't see the right one? Ask your Super Admin to add it under Reference Data.</div>
    @else
      <label class="form-label">Category</label>
      <select name="category_id" id="category_select" class="form-select" required>
        <option value="">Select category</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
      <div class="tiny mt-1">Don't see the right one? Ask your Super Admin to add it under Reference Data.</div>
    @endif
  </div>
  <div class="col-md-6">
    <label class="form-label">Assigned Department</label>
    <select name="assigned_department_id" class="form-select">
      <option value="">Select department</option>
      @foreach(($departments ?? []) as $department)
        <option value="{{ $department->id }}" @selected(old('assigned_department_id', $item->assigned_department_id ?? '') == $department->id)>{{ $department->name }}</option>
      @endforeach
    </select>
  </div>

  @if($isCapex)
    <div class="col-md-4">
      <label class="form-label">Date Acquired</label>
      <input type="date" name="acquisition_date" class="form-control" value="{{ old('acquisition_date', optional($item->acquisition_date ?? null)->format('Y-m-d') ?? '') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Floor</label>
      @if($existingItem?->exists)
        <input class="form-control" value="{{ $existingItem->floorRef->name ?? $existingItem->floor }}" readonly disabled>
        <input type="hidden" name="floor_id" id="floor_select" value="{{ $existingItem->floor_id }}">
        <div class="tiny mt-1">Floor is locked after creation to keep the asset tag consistent.</div>
      @else
        <div id="floor_chips" class="picker-chip-row"></div>
        <input type="hidden" name="floor_id" id="floor_select" value="{{ old('floor_id') }}">
      @endif
    </div>
    <div class="col-md-4" id="room_wrap" style="{{ $existingItem?->exists ? '' : 'display:none' }}">
      <label class="form-label">Assigned Room</label>
      <select name="room_id" id="room_select" class="form-select" required>
        <option value="">Select floor first</option>
      </select>
      <div class="tiny mt-1">Missing room? Ask your Super Admin to add it under Reference Data.</div>
    </div>
    <div class="col-md-8">
      <label class="form-label">Brand</label>
      <input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}">
    </div>
    <input type="hidden" name="quantity" value="{{ old('quantity', $item->quantity ?? 1) }}">
    <input type="hidden" name="unit" value="{{ old('unit', $item->unit ?? 'unit') }}">
    <input type="hidden" name="unit_price" value="{{ old('unit_price', $item->unit_price ?? 0) }}">
    <input type="hidden" name="availability_status" value="{{ old('availability_status', $item->availability_status ?? 'Available') }}">
    <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}">
    @unless($existingItem?->exists)
    <div class="col-md-4">
      <label class="form-label">How Many Units?</label>
      <input type="number" name="unit_count" class="form-control" min="1" max="300" value="{{ old('unit_count', 1) }}" required>
      <div class="tiny mt-1">Creates that many separate assets in this same room, each with its own auto-generated asset tag ID. No need to add them one by one.</div>
    </div>
    @endunless
    <script>
    (function () {
      const categoryTypeMap = @json($categoryTypeMap);
      const roomsByFloor = @json($roomsByFloor);
      const categoryList = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());
      const floorList = @json(collect($floors ?? [])->map(fn($f) => ['id' => $f->id, 'name' => $f->name])->values());

      const categoryHidden = document.getElementById('category_select');
      const typeHidden = document.getElementById('asset_type_choice');
      const categoryChips = document.getElementById('category_chips');
      const assetTypeWrap = document.getElementById('asset_type_wrap');
      const assetTypeSelect = document.getElementById('asset_type_select');

      const floorHidden = document.getElementById('floor_select');
      const floorChips = document.getElementById('floor_chips');
      const roomWrap = document.getElementById('room_wrap');
      const roomSelect = document.getElementById('room_select');

      const presetCategoryId = @json(old('category_id', $item->category_id ?? ''));
      const presetAssetType = @json(old('asset_type_name', $item->asset_type_name ?? ''));
      const presetFloorId = @json(old('floor_id', $existingItem?->floor_id ?? ''));
      const presetRoomId = @json(old('room_id', $item->room_id ?? ''));

      // --- Category chips -> reveals Asset Type dropdown ---
      function buildChipRow(container, list, activeId, onPick) {
        if (!container) return;
        container.innerHTML = '';
        list.forEach(function (entry) {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'picker-chip' + (String(activeId) === String(entry.id) ? ' active' : '');
          btn.textContent = entry.name;
          btn.dataset.id = entry.id;
          btn.addEventListener('click', function () {
            container.querySelectorAll('.picker-chip').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            onPick(entry.id);
          });
          container.appendChild(btn);
        });
      }

      function populateAssetTypes(catId, preselect) {
        const options = categoryTypeMap[catId] || [];
        assetTypeSelect.innerHTML = '<option value="">Select asset type</option>';
        options.forEach(function (opt) {
          const o = document.createElement('option');
          o.value = opt; o.textContent = opt;
          if (preselect === opt) o.selected = true;
          assetTypeSelect.appendChild(o);
        });
        typeHidden.value = preselect && options.includes(preselect) ? preselect : '';
      }

      if (categoryChips) {
        buildChipRow(categoryChips, categoryList, presetCategoryId, function (catId) {
          categoryHidden.value = catId;
          assetTypeWrap.style.display = '';
          populateAssetTypes(catId, '');
        });
        if (presetCategoryId) {
          assetTypeWrap.style.display = '';
          populateAssetTypes(presetCategoryId, presetAssetType);
        }
        assetTypeSelect.addEventListener('change', function () { typeHidden.value = assetTypeSelect.value; });
      }

      // --- Floor chips -> reveals Room dropdown ---
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

      if (floorChips) {
        buildChipRow(floorChips, floorList, presetFloorId, function (floorId) {
          floorHidden.value = floorId;
          roomWrap.style.display = '';
          populateRooms(floorId, '');
        });
        if (presetFloorId) {
          roomWrap.style.display = '';
          populateRooms(presetFloorId, presetRoomId);
        }
      } else if (floorHidden && floorHidden.value) {
        // Locked/edit mode: floor is fixed, just populate rooms for it.
        populateRooms(floorHidden.value, presetRoomId);
      }
    })();
    </script>

    <style>
      .picker-chip-row{display:flex;flex-wrap:wrap;gap:8px}
      .picker-chip{border:1px solid var(--line,#c7cbd4);background:var(--surface,transparent);color:inherit;padding:8px 14px;border-radius:999px;font-size:13px;cursor:pointer;transition:all .15s ease}
      .picker-chip:hover{border-color:var(--primary,#1e1b4b)}
      .picker-chip.active{background:var(--primary,#1e1b4b);border-color:var(--primary,#1e1b4b);color:#fff;font-weight:600}
    </style>
  @else
    <div class="col-md-3"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? 0) }}" required></div>
    <div class="col-md-3"><label class="form-label">Unit</label><input name="unit" class="form-control" value="{{ old('unit', $item->unit ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">Unit Price</label><input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price ?? 0) }}"></div>
    <div class="col-md-3"><label class="form-label">Brand</label><input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Availability</label><select name="availability_status" class="form-select"><option value="Available" @selected(old('availability_status', $item->availability_status ?? 'Available')==='Available')>Available</option><option value="Limited Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Limited Stock')>Limited Stock</option><option value="Out of Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Out of Stock')>Out of Stock</option></select></div>
    <div class="col-md-4"><label class="form-label">Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}" required></div>
  @endif

  <div class="col-md-6"><label class="form-label">Item Image</label><input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*"><div class="tiny mt-1">Upload a JPG, PNG, or WEBP file up to 4MB.</div></div>
  <div class="col-md-6"><label class="form-label">Specifications</label><textarea name="specifications" class="form-control" rows="3">{{ old('specifications', $item->specifications ?? '') }}</textarea></div>
  @if(!empty($item?->display_image))
    <div class="col-md-6">
      <label class="form-label d-block">Current Image</label>
      <img src="{{ $item->display_image }}" alt="{{ $item->name ?? 'Item image' }}" style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid var(--line);background:var(--surface-2)">
      @if(!empty($item?->image_path))
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="remove_image">
        <label class="form-check-label" for="remove_image">Remove current image</label>
      </div>
      @endif
    </div>
  @endif
  <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $item->description ?? '') }}</textarea></div>
  <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label" for="is_active">Active item</label></div>
</div>
