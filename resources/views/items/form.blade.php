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
      <label class="form-label">Category &amp; Asset Type</label>
      <select id="asset_selector" class="form-select" required>
        <option value="">Select category, then asset type</option>
        @foreach($categories as $category)
          @php($typesForCategory = $categoryTypeMap[$category->id] ?? [])
          <optgroup label="{{ $category->name }}">
            @foreach($typesForCategory as $typeName)
              <option
                value="{{ $typeName }}"
                data-category-id="{{ $category->id }}"
                @selected(old('category_id', $item->category_id ?? '') == $category->id && old('asset_type_name', $item->asset_type_name ?? '') === $typeName)
              >{{ $typeName }}</option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
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
        <select name="floor_id" id="floor_select" class="form-select" required>
          <option value="">Select floor</option>
          @foreach(($floors ?? []) as $floorOption)
            <option value="{{ $floorOption->id }}" @selected(old('floor_id') == $floorOption->id)>{{ $floorOption->name }}</option>
          @endforeach
        </select>
      @endif
    </div>
    <div class="col-md-4">
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
      const roomsByFloor = @json($roomsByFloor);
      const assetSelector = document.getElementById('asset_selector');
      const categoryHidden = document.getElementById('category_select');
      const typeHidden = document.getElementById('asset_type_choice');
      const floorSelect = document.getElementById('floor_select');
      const roomSelect = document.getElementById('room_select');
      const presetRoomId = @json(old('room_id', $item->room_id ?? ''));

      // Single "Category & Asset Type" dropdown -- picking one option (e.g. under the
      // "Electronics" group, "Laptop") sets both hidden fields (category_id +
      // asset_type_name) in one step, instead of two separate dropdowns.
      if (assetSelector) {
        function syncFromSelector() {
          const selected = assetSelector.selectedOptions[0];
          if (selected && selected.value) {
            categoryHidden.value = selected.dataset.categoryId || '';
            typeHidden.value = selected.value;
          } else {
            categoryHidden.value = '';
            typeHidden.value = '';
          }
        }
        assetSelector.addEventListener('change', syncFromSelector);
        // Only auto-sync on load if an option actually matched the existing item's
        // saved category/asset type. If nothing matched (e.g. the asset type was
        // renamed/removed from Reference Data after this item was created), leave the
        // hidden fields exactly as they were pre-filled -- don't wipe real saved data
        // just because the dropdown itself can't visually show a matching selection.
        if (assetSelector.value) {
          syncFromSelector();
        }
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

      if (floorSelect) {
        if (floorSelect.value) populateRooms(floorSelect.value, presetRoomId);
        if (floorSelect.tagName === 'SELECT') {
          floorSelect.addEventListener('change', function () { populateRooms(floorSelect.value, ''); });
        }
      }
    })();
    </script>
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
