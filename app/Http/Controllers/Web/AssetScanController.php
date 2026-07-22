<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AssetScanLog;
use App\Models\Item;
use Illuminate\Http\Request;

class AssetScanController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->canHandleAssetScans(), 403);
        $items = Item::where('item_type', 'CAPEX')->orderBy('item_code')->get();
        $logs = AssetScanLog::with(['item','user'])->latest()->paginate(15);
        return view('asset_scans.index', compact('items','logs'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canHandleAssetScans(), 403);
        $data = $request->validate([
            'item_id' => ['required','exists:items,id'],
            'scanned_room' => ['required','string','max:100'],
            'latitude' => ['nullable','numeric'],
            'longitude' => ['nullable','numeric'],
            'notes' => ['nullable','string','max:500'],
        ]);
        $item = Item::findOrFail($data['item_id']);
        $expected = trim((string) $item->room_assigned);
        $scanned = trim((string) $data['scanned_room']);
        $status = strcasecmp($expected, $scanned) === 0 ? 'matched' : 'mismatch';
        AssetScanLog::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'expected_room' => $expected,
            'scanned_room' => $scanned,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
        ]);
        return back()->with('success', $status === 'matched' ? 'Asset location matched.' : 'Mismatch detected and logged for reporting.');
    }
}
