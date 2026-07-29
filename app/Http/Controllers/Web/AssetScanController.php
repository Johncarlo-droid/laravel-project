<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AssetScanLog;
use Illuminate\Http\Request;

class AssetScanController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->canHandleAssetScans(), 403);

        $filter = $request->get('filter', 'all');
        $query = AssetScanLog::with(['item', 'user', 'resolver']);

        if ($filter === 'unresolved') {
            $query->where('status', 'mismatch')->whereNull('resolved_at');
        } elseif ($filter === 'resolved') {
            $query->where('status', 'mismatch')->whereNotNull('resolved_at');
        } elseif ($filter === 'matched') {
            $query->where('status', 'matched');
        }

        $logs = $query->latest()->paginate(15)->withQueryString();
        $unresolvedCount = AssetScanLog::where('status', 'mismatch')->whereNull('resolved_at')->count();

        return view('asset_scans.index', compact('logs', 'filter', 'unresolvedCount'));
    }

    public function resolve(AssetScanLog $assetScanLog)
    {
        abort_unless(auth()->user()->canHandleAssetScans(), 403);
        abort_unless($assetScanLog->isUnresolvedMismatch(), 422);

        $assetScanLog->update(['resolved_at' => now(), 'resolved_by' => auth()->id()]);

        return back()->with('success', 'Mismatch marked as resolved.');
    }
}
