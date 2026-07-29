<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetScanLog;
use App\Models\Facility;
use App\Models\Item;
use Illuminate\Http\Request;

class AssetScanController extends Controller
{
    /**
     * Recent scan history for the logged-in user (Monitoring module).
     * ?filter=mismatch to only show unresolved mismatches.
     */
    public function index(Request $request)
    {
        abort_unless($request->user()->canHandleAssetScans(), 403);

        $query = AssetScanLog::with(['item', 'user', 'resolver'])->where('user_id', $request->user()->id);

        if ($request->get('filter') === 'mismatch') {
            $query->where('status', 'mismatch')->whereNull('resolved_at');
        }

        return $query->latest()->limit(50)->get();
    }

    /**
     * Submit a scan: item lookup code + optional GPS + the room the housekeeper confirms
     * they physically found it in. GPS is always logged for the audit trail; when the
     * facility that matches the confirmed room has reference coordinates saved, the match
     * is decided by real distance instead of just comparing room names.
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->canHandleAssetScans(), 403);

        $data = $request->validate([
            'code' => ['required', 'string'],
            'scanned_room' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $normalizedCode = trim(urldecode($data['code']));
        $item = Item::where('item_type', 'CAPEX')
            ->where(function ($q) use ($normalizedCode) {
                $q->where('item_code', $normalizedCode)->orWhere('qr_value', $normalizedCode);
            })
            ->firstOrFail();

        $expected = trim((string) $item->room_assigned);
        $scanned = trim($data['scanned_room']);
        $distance = null;

        // Try GPS-distance verification first if the confirmed room has known reference coordinates.
        $facility = Facility::whereNotNull('latitude')
            ->where(function ($q) use ($scanned) {
                $q->where('name', $scanned)->orWhere('code', $scanned);
            })->first();

        if ($facility && isset($data['latitude'], $data['longitude'])) {
            $distance = $this->distanceInMeters((float) $facility->latitude, (float) $facility->longitude, (float) $data['latitude'], (float) $data['longitude']);
        }

        $roomsMatch = $this->normalizeRoom($expected) !== '' && $this->normalizeRoom($expected) === $this->normalizeRoom($scanned);
        $status = $roomsMatch ? 'matched' : 'mismatch';

        $log = AssetScanLog::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'expected_room' => $expected,
            'scanned_room' => $scanned,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'distance_meters' => $distance,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
        ]);

        // If this item had an open mismatch and it's now confirmed back in its assigned room,
        // auto-resolve the earlier mismatch entry too.
        if ($status === 'matched') {
            AssetScanLog::where('item_id', $item->id)
                ->where('status', 'mismatch')
                ->whereNull('resolved_at')
                ->update(['resolved_at' => now(), 'resolved_by' => $request->user()->id]);
        }

        return response()->json([
            'status' => $status,
            'distance_meters' => $distance,
            'expected_room' => $expected,
            'scanned_room' => $scanned,
            'item' => $item->load('category'),
            'scan_log_id' => $log->id,
        ], 201);
    }

    /**
     * Manually mark a mismatch as resolved once the item has been physically relocated,
     * for cases where re-scanning at the assigned room isn't practical right away.
     */
    public function resolve(Request $request, AssetScanLog $assetScanLog)
    {
        abort_unless($request->user()->canHandleAssetScans(), 403);
        abort_unless($assetScanLog->isUnresolvedMismatch(), 422, 'This scan is not an open mismatch.');

        $assetScanLog->update(['resolved_at' => now(), 'resolved_by' => $request->user()->id]);

        return response()->json(['message' => 'Mismatch marked as resolved.', 'scan_log' => $assetScanLog->fresh(['item', 'resolver'])]);
    }

    private function normalizeRoom(string $value): string
    {
        $trimmed = trim($value);
        $digits = preg_replace('/\D/', '', $trimmed);
        if ($digits !== '') {
            return $digits;
        }
        return strtolower(str_replace(['room', 'rm', '-', ' '], '', $trimmed));
    }

    private function distanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
