@extends('layouts.admin', ['title' => 'Reference Data'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">Reference Data</h2>
        <div class="module-note">Floors, Rooms, Categories, and Asset Types are managed here by Super Admin only. Everyone else picks from these lists when adding items — they can't type in new ones on the fly anymore.</div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="panel-grid-2">
    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-layers-half"></i> Floors</h3>
        <form method="POST" action="{{ route('reference-data.floors.store') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-7"><input name="name" class="form-control" placeholder="e.g. 9th Floor" required></div>
            <div class="col-3"><input name="sort_order" type="number" class="form-control" placeholder="Order" min="0"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Floor</th><th>Rooms</th><th>Assets</th><th></th></tr></thead>
                <tbody>
                @forelse($floors as $floor)
                    <tr>
                        <td data-label="Floor">{{ $floor->name }}</td>
                        <td data-label="Rooms">{{ $floor->rooms_count }}</td>
                        <td data-label="Assets">{{ $floor->items_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('reference-data.floors.destroy', $floor) }}" onsubmit="return confirm('Remove {{ $floor->name }}?');">
                                @csrf @method('DELETE')
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No floors yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-door-open"></i> Rooms</h3>
        <form method="POST" action="{{ route('reference-data.rooms.store') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-4">
                <select name="floor_id" class="form-select" required>
                    <option value="">Floor</option>
                    @foreach($floors as $floor)<option value="{{ $floor->id }}">{{ $floor->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-4"><input name="name" class="form-control" placeholder="e.g. 719 or Server Room" required></div>
            <div class="col-2"><input name="code" class="form-control" placeholder="Code"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <input type="text" id="roomSearch" class="form-control" placeholder="Search rooms by name, code, or floor…" style="max-width:280px">
            <div>
                <button type="button" id="expandAllRooms" class="btn-soft small-btn">Expand All</button>
                <button type="button" id="collapseAllRooms" class="btn-soft small-btn">Collapse All</button>
            </div>
        </div>

        <div id="roomsAccordion">
        @forelse($floors as $floor)
            @php($floorRooms = $rooms->where('floor_id', $floor->id))
            <details class="room-floor-group" data-floor-name="{{ strtolower($floor->name) }}">
                <summary>
                    <span><i class="bi bi-layers-half"></i> {{ $floor->name }}</span>
                    <span class="tiny-2">{{ $floorRooms->count() }} room{{ $floorRooms->count() === 1 ? '' : 's' }}</span>
                </summary>
                <div class="table-responsive mt-2">
                    <table class="data-table">
                        <thead><tr><th>Room</th><th>Assets</th><th></th></tr></thead>
                        <tbody>
                        @forelse($floorRooms as $room)
                            <tr class="room-row" data-search="{{ strtolower($room->name.' '.$room->code.' '.$floor->name) }}">
                                <td data-label="Room">{{ $room->name }} @if($room->code)<span class="tiny-2">({{ $room->code }})</span>@endif</td>
                                <td data-label="Assets">{{ $room->items_count }}</td>
                                <td>
                                    <form method="POST" action="{{ route('reference-data.rooms.destroy', $room) }}" onsubmit="return confirm('Remove {{ $room->name }}?');">
                                        @csrf @method('DELETE')
                                        <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">No rooms on this floor yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </details>
        @empty
            <div class="empty-state">Add a floor first, then rooms.</div>
        @endforelse
        </div>
        <div id="noRoomResults" class="empty-state" style="display:none">No rooms match your search.</div>

        <style>
            .room-floor-group{border:1px solid var(--border-color,rgba(255,255,255,.08));border-radius:10px;padding:10px 14px;margin-bottom:8px;background:var(--surface,transparent)}
            .room-floor-group summary{cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:600;list-style:none}
            .room-floor-group summary::-webkit-details-marker{display:none}
            .room-floor-group summary::before{content:'\25B8';display:inline-block;margin-right:8px;transition:transform .15s ease}
            .room-floor-group[open] summary::before{transform:rotate(90deg)}
        </style>
        <script>
        (function(){
            const search = document.getElementById('roomSearch');
            const groups = document.querySelectorAll('.room-floor-group');
            const noResults = document.getElementById('noRoomResults');
            search?.addEventListener('input', function(){
                const q = this.value.trim().toLowerCase();
                let anyVisible = false;
                groups.forEach(function(group){
                    let groupHasMatch = false;
                    group.querySelectorAll('.room-row').forEach(function(row){
                        const match = q === '' || row.dataset.search.includes(q);
                        row.style.display = match ? '' : 'none';
                        if (match) groupHasMatch = true;
                    });
                    const floorMatches = group.dataset.floorName.includes(q);
                    const show = q === '' || groupHasMatch || floorMatches;
                    group.style.display = show ? '' : 'none';
                    if (show && q !== '' && (groupHasMatch || floorMatches)) { group.open = true; anyVisible = true; }
                    if (show) anyVisible = true;
                });
                noResults.style.display = (!anyVisible && q !== '') ? '' : 'none';
            });
            document.getElementById('expandAllRooms')?.addEventListener('click', () => groups.forEach(g => g.open = true));
            document.getElementById('collapseAllRooms')?.addEventListener('click', () => groups.forEach(g => g.open = false));
        })();
        </script>
    </div>
</div>

<div class="panel-grid-2">
    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-tags"></i> Categories</h3>
        <form method="POST" action="{{ route('reference-data.categories.store') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-5"><input name="name" class="form-control" placeholder="e.g. Electronics" required></div>
            <div class="col-5"><input name="description" class="form-control" placeholder="Description (optional)"></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Category</th><th>Items</th><th>Asset Types</th><th></th></tr></thead>
                <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td data-label="Category">{{ $category->name }}</td>
                        <td data-label="Items">{{ $category->items_count }}</td>
                        <td data-label="Asset Types">{{ $category->asset_types_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('reference-data.categories.destroy', $category) }}" onsubmit="return confirm('Remove {{ $category->name }}?');">
                                @csrf @method('DELETE')
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No categories yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-diagram-2"></i> Asset Types</h3>
        <form method="POST" action="{{ route('reference-data.asset-types.store') }}" class="row g-2 mb-3">
            @csrf
            <div class="col-5">
                <select name="item_category_id" class="form-select" required>
                    <option value="">Category</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-5"><input name="name" class="form-control" placeholder="e.g. Laptop" required></div>
            <div class="col-2"><button class="btn-primaryx w-100 justify-content-center"><i class="bi bi-plus-lg"></i></button></div>
        </form>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Asset Type</th><th>Category</th><th></th></tr></thead>
                <tbody>
                @forelse($assetTypes as $type)
                    <tr>
                        <td data-label="Asset Type">{{ $type->name }}</td>
                        <td data-label="Category">{{ $type->category->name ?? 'N/A' }}</td>
                        <td>
                            <form method="POST" action="{{ route('reference-data.asset-types.destroy', $type) }}" onsubmit="return confirm('Remove {{ $type->name }}?');">
                                @csrf @method('DELETE')
                                <button class="btn-soft small-btn"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No asset types yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="surface p-3">
    <h3 class="module-title mb-2" style="font-size:16px"><i class="bi bi-building"></i> Departments</h3>
    <div class="tiny mb-2">Full department management (with capex/opex budget limits) lives on its own page.</div>
    <a href="{{ route('departments.index') }}" class="btn-soft small-btn"><i class="bi bi-arrow-right"></i> Open Departments</a>
</div>
@endsection
