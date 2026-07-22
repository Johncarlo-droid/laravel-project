@extends('layouts.admin', ['title' => 'Asset Scan Monitoring'])

@section('content')
<div class="module-head"><div><h2 class="module-title">Mismatch Detection and Reporting</h2><div class="module-note">Manual web version for QR scan validation. Mobile app/API can submit the same fields later.</div></div></div>

<div class="surface p-3 mb-3">
  <form method="POST" action="{{ route('asset-scans.store') }}">@csrf
    <div class="row g-3 align-items-end">
      <div class="col-md-5"><label class="form-label">CAPEX Asset</label><select name="item_id" class="form-select" required><option value="">Select scanned asset</option>@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->item_code }} — {{ $item->name }} (Assigned: {{ $item->room_assigned ?: 'N/A' }})</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Current / Scanned Room</label><input name="scanned_room" class="form-control" placeholder="Example: 719" required></div>
      <div class="col-md-2"><label class="form-label">Latitude</label><input name="latitude" class="form-control" placeholder="optional"></div>
      <div class="col-md-2"><label class="form-label">Longitude</label><input name="longitude" class="form-control" placeholder="optional"></div>
      <div class="col-12"><label class="form-label">Notes</label><input name="notes" class="form-control" placeholder="Optional remarks from housekeeping/admin"></div>
      <div class="col-12"><button class="btn-primaryx"><i class="bi bi-qr-code-scan"></i> Save Scan Result</button></div>
    </div>
  </form>
</div>

<div class="data-panel">
  <div class="table-responsive"><table class="data-table"><thead><tr><th>Date</th><th>Asset</th><th>Expected Room</th><th>Scanned Room</th><th>Status</th><th>Scanned By</th><th>Notes</th></tr></thead><tbody>
  @forelse($logs as $log)
    <tr><td>{{ $log->created_at->format('M d, Y h:i A') }}</td><td>{{ $log->item->item_code ?? 'N/A' }}<div class="tiny">{{ $log->item->name ?? '' }}</div></td><td>{{ $log->expected_room ?: 'N/A' }}</td><td>{{ $log->scanned_room ?: 'N/A' }}</td><td><span class="status {{ $log->status === 'matched' ? 'approved' : 'low' }}">{{ ucfirst($log->status) }}</span></td><td>{{ $log->user->name ?? 'System' }}</td><td>{{ $log->notes }}</td></tr>
  @empty<tr><td colspan="7" class="empty-state">No scan logs yet.</td></tr>@endforelse
  </tbody></table></div>
  <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
