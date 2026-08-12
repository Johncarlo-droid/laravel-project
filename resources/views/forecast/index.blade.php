@extends('layouts.admin', ['title' => 'Linear Regression Forecasting'])

@section('content')
<div class="module-head">
  <div><h2 class="module-title">Linear Regression-Based Consumption Forecasting</h2><div class="module-note">Forecasts future OPEX consumption from monthly historical usage data.</div></div>
</div>

<div class="surface p-3 mb-3">
  <form method="GET" class="row g-3 align-items-end">
    <div class="col-md-8"><label class="form-label">OPEX Item</label><select name="item_id" class="form-select">@foreach($items as $item)<option value="{{ $item->id }}" @selected($selectedItem?->id === $item->id)>{{ $item->item_code }} — {{ $item->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><button class="btn-primaryx"><i class="bi bi-graph-up-arrow"></i> Compute Forecast</button></div>
  </form>
</div>

<div class="data-panel mb-3">
  <h2 class="module-title mb-2">Forecasted Consumption Overview</h2>
  <div class="module-note mb-2">All OPEX items with enough historical usage data to forecast, sorted by most urgent restock need. @if(auth()->user()->isSuperAdmin())Super Admin: click "View Details" to manage/delete an item's underlying usage log entries below.@endif</div>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Item</th><th>Predicted Next-Month Demand</th><th>Current Stock</th><th>Suggested Restock</th><th></th></tr></thead>
      <tbody>
      @forelse($allForecasts as $row)
        <tr>
          <td>{{ $row['item']->item_code }} — {{ $row['item']->name }}</td>
          <td>{{ $row['forecast']['predicted'] }} {{ $row['item']->unit }}</td>
          <td>{{ $row['forecast']['currentStock'] }} {{ $row['item']->unit }}</td>
          <td><span class="status {{ $row['forecast']['suggestedRestock'] > 0 ? 'low' : 'approved' }}">{{ $row['forecast']['suggestedRestock'] }} {{ $row['item']->unit }}</span></td>
          <td><a href="{{ route('forecast.index', ['item_id' => $row['item']->id]) }}" class="btn-soft small-btn">View Details</a></td>
        </tr>
      @empty
        <tr><td colspan="5" class="empty-state">No OPEX items have enough historical usage data yet (need at least 2 different calendar months logged per item).</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($selectedItem)
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

@if($forecast)
<div class="surface p-3 mb-3">
  <h2 class="module-title mb-2">Log Historical Usage</h2>
  <div class="module-note mb-2">
    @if(!$forecast['ready'])
      The regression needs at least two different calendar months of usage data before it can compute a trend. Rather than waiting for real time to pass, record past months' usage directly — this is standard practice for bootstrapping a forecasting model with historical data, the same way any live system would once it has been in use for a while.
    @else
      Add another month's figures anytime to extend the trend line.
    @endif
  </div>
  <form method="POST" action="{{ route('forecast.usage-logs.store') }}" class="row g-3 align-items-end">
    @csrf
    <input type="hidden" name="item_id" value="{{ $selectedItem->id }}">
    <div class="col-md-4">
      <label class="form-label">Usage Month</label>
      <input type="date" name="usage_date" class="form-control" max="{{ now()->toDateString() }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Quantity Used</label>
      <input type="number" name="quantity_used" class="form-control" min="1" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Remarks (optional)</label>
      <input type="text" name="remarks" class="form-control" placeholder="e.g. April consumption">
    </div>
    <div class="col-md-2">
      <button class="btn-primaryx w-100"><i class="bi bi-plus-lg"></i> Add</button>
    </div>
  </form>
  @if(!$forecast['ready'])
  <div class="tiny mt-2">Tip: add one entry dated last month and one dated this month (or any two different months) to instantly have enough data for a demo-ready forecast.</div>
  @endif
</div>
@endif

<div class="panel-grid-2">
  <div class="data-panel">
    <h2 class="module-title mb-2">Historical Monthly Usage</h2>
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>x</th><th>Month</th><th>Usage (y)</th><th>x²</th><th>xy</th></tr></thead>
        <tbody>
        @forelse($forecast['points'] as $point)
          <tr><td>{{ $point['x'] }}</td><td>{{ $point['period'] }}</td><td>{{ $point['y'] }}</td><td>{{ $point['x'] * $point['x'] }}</td><td>{{ $point['x'] * $point['y'] }}</td></tr>
        @empty<tr><td colspan="5" class="empty-state">No usage data yet.</td></tr>@endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="data-panel">
    <h2 class="module-title mb-2">Forecast Result</h2>
    @if(!$forecast['ready'])
      <div class="alert alert-warning">{{ $forecast['message'] }}</div>
    @else
      <div class="row g-3">
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Σx</div><div class="fs-4 fw-bold">{{ $forecast['sumX'] }}</div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Σy</div><div class="fs-4 fw-bold">{{ $forecast['sumY'] }}</div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Slope (b)</div><div class="fs-4 fw-bold">{{ $forecast['b'] }}</div></div></div>
        <div class="col-6"><div class="report-stat"><div class="tiny-2">Intercept (a)</div><div class="fs-4 fw-bold">{{ $forecast['a'] }}</div></div></div>
      </div>
      <hr>
      <p class="mb-1"><strong>Equation:</strong> y = {{ $forecast['a'] }} + {{ $forecast['b'] }}x</p>
      <p class="mb-1"><strong>Next period x:</strong> {{ $forecast['nextX'] }}</p>
      <p class="mb-1"><strong>Forecasted demand:</strong> {{ $forecast['predicted'] }} {{ $selectedItem->unit }}</p>
      <p class="mb-1"><strong>Current stock:</strong> {{ $forecast['currentStock'] }} {{ $selectedItem->unit }}</p>
      <p class="mb-1"><strong>Suggested restock:</strong> <span class="status {{ $forecast['suggestedRestock'] > 0 ? 'low' : 'approved' }}">{{ $forecast['suggestedRestock'] }} {{ $selectedItem->unit }}</span></p>
    @endif
  </div>
</div>

<div class="data-panel mt-3">
  <h2 class="module-title mb-2">Recent Usage Log Entries</h2>
  <div class="module-note mb-2">Individual records behind the monthly totals above. @if(auth()->user()->isSuperAdmin())As Super Admin, you can delete a wrong/duplicate entry here — the forecast recalculates automatically.@else Only Super Admin accounts can delete entries here.@endif</div>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Date</th><th>Quantity Used</th><th>Source</th><th>Remarks</th>@if(auth()->user()->isSuperAdmin())<th></th>@endif</tr></thead>
      <tbody>
      @forelse($usageLogs as $log)
        <tr>
          <td>{{ \Carbon\Carbon::parse($log->usage_date)->format('M d, Y') }}</td>
          <td>{{ $log->quantity_used }} {{ $selectedItem->unit }}</td>
          <td><span class="tiny-2">{{ $log->source === 'manual_backfill' ? 'Manual entry' : 'Auto-logged from requisition' }}</span></td>
          <td>{{ $log->remarks }}</td>
          @if(auth()->user()->isSuperAdmin())
          <td>
            <form method="POST" action="{{ route('forecast.usage-logs.destroy', $log) }}" onsubmit="return confirm('Delete this usage log entry? This will recalculate the forecast.');">
              @csrf @method('DELETE')
              <button class="btn-soft small-btn text-danger"><i class="bi bi-trash"></i> Delete</button>
            </form>
          </td>
          @endif
        </tr>
      @empty
        <tr><td colspan="5" class="empty-state">No usage log entries yet for this item.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
