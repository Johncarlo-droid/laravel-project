<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InventoryUsageLog;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $items = Item::where('item_type', 'OPEX')->orderBy('name')->get();
        $selectedItem = $request->filled('item_id') ? Item::find($request->integer('item_id')) : $items->first();
        $forecast = null;
        $usageLogs = collect();

        if ($selectedItem) {
            $rows = InventoryUsageLog::where('item_id', $selectedItem->id)
                ->selectRaw("TO_CHAR(usage_date, 'YYYY-MM') as period, SUM(quantity_used) as usage_qty")
                ->groupByRaw("TO_CHAR(usage_date, 'YYYY-MM')")
                ->orderBy('period')
                ->get();

            $points = [];
            foreach ($rows as $i => $row) {
                $points[] = ['x' => $i + 1, 'period' => $row->period, 'y' => (int) $row->usage_qty];
            }
            $forecast = $this->linearRegression($points, (int) $selectedItem->quantity, (int) $selectedItem->low_stock_threshold);
            $usageLogs = InventoryUsageLog::where('item_id', $selectedItem->id)->orderByDesc('usage_date')->limit(15)->get();
        }

        return view('forecast.index', compact('items','selectedItem','forecast','usageLogs'));
    }

    public function storeUsageLog(Request $request)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'usage_date' => ['required', 'date', 'before_or_equal:today'],
            'quantity_used' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        InventoryUsageLog::create([
            'item_id' => $data['item_id'],
            'usage_date' => $data['usage_date'],
            'quantity_used' => $data['quantity_used'],
            'source' => 'manual_backfill',
            'remarks' => $data['remarks'] ?? 'Manually recorded historical usage',
        ]);

        return redirect()->route('forecast.index', ['item_id' => $data['item_id']])
            ->with('success', 'Historical usage record added. Forecast recalculated below.');
    }

    private function linearRegression(array $points, int $currentStock, int $lowStockThreshold): array
    {
        $n = count($points);
        if ($n < 2) {
            return ['ready' => false, 'points' => $points, 'message' => 'At least two monthly usage records are needed to compute Linear Regression.'];
        }

        $sumX = array_sum(array_column($points, 'x'));
        $sumY = array_sum(array_column($points, 'y'));
        $sumX2 = array_sum(array_map(fn($p) => $p['x'] * $p['x'], $points));
        $sumXY = array_sum(array_map(fn($p) => $p['x'] * $p['y'], $points));
        $denominator = ($n * $sumX2) - ($sumX * $sumX);
        $b = $denominator == 0 ? 0 : (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $a = ($sumY - ($b * $sumX)) / $n;
        $nextX = $n + 1;
        $predicted = max(0, round($a + ($b * $nextX)));
        $suggestedRestock = max(0, ($predicted + $lowStockThreshold) - $currentStock);

        return [
            'ready' => true,
            'points' => $points,
            'n' => $n,
            'sumX' => $sumX,
            'sumY' => $sumY,
            'sumX2' => $sumX2,
            'sumXY' => $sumXY,
            'a' => round($a, 2),
            'b' => round($b, 2),
            'nextX' => $nextX,
            'predicted' => $predicted,
            'currentStock' => $currentStock,
            'lowStockThreshold' => $lowStockThreshold,
            'suggestedRestock' => $suggestedRestock,
        ];
    }
}
