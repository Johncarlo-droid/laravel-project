@extends('layouts.admin', ['title' => 'Asset Scan Monitoring'])

@section('content')
<div class="module-head"><div><h2 class="module-title">Mismatch Detection and Monitoring</h2><div class="module-note">Scanning is performed exclusively through the mobile app by housekeeping/asset custodians (GPS + QR code). This page is a read-only monitoring view of everything they scan.</div></div></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

@if($unresolvedCount > 0)
<div class="alert alert-danger">{{ $unresolvedCount }} unresolved mismatch(es) — an asset was scanned somewhere other than its assigned room and hasn't been relocated/confirmed yet.</div>
@endif

<div class="page-tabs mb-3">
  <a href="{{ route('asset-scans.index', ['filter' => 'all']) }}" class="{{ $filter === 'all' ? 'active' : '' }}">All Scans</a>
  <a href="{{ route('asset-scans.index', ['filter' => 'unresolved']) }}" class="{{ $filter === 'unresolved' ? 'active' : '' }}">Unresolved Mismatches</a>
  <a href="{{ route('asset-scans.index', ['filter' => 'resolved']) }}" class="{{ $filter === 'resolved' ? 'active' : '' }}">Resolved</a>
  <a href="{{ route('asset-scans.index', ['filter' => 'matched']) }}" class="{{ $filter === 'matched' ? 'active' : '' }}">Matched</a>
</div>

<div class="data-panel">
  <div class="table-responsive"><table class="data-table"><thead><tr><th>Date</th><th>Asset</th><th>Expected Room</th><th>Scanned Room</th><th>GPS Distance</th><th>Status</th><th>Scanned By</th><th>Notes</th><th></th></tr></thead><tbody>
  @forelse($logs as $log)
    <tr>
      <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
      <td>{{ $log->item->item_code ?? 'N/A' }}<div class="tiny">{{ $log->item->name ?? '' }}</div></td>
      <td>{{ $log->expected_room ?: 'N/A' }}</td>
      <td>{{ $log->scanned_room ?: 'N/A' }}</td>
      <td>{{ $log->distance_meters !== null ? $log->distance_meters.' m' : '—' }}</td>
      <td>
        <span class="status {{ $log->status === 'matched' ? 'approved' : ($log->resolved_at ? 'pending' : 'low') }}">
          {{ $log->status === 'matched' ? 'Matched' : ($log->resolved_at ? 'Mismatch (Resolved)' : 'Mismatch (Open)') }}
        </span>
        @if($log->resolved_at)<div class="tiny">by {{ $log->resolver->name ?? 'N/A' }} on {{ $log->resolved_at->format('M d, h:i A') }}</div>@endif
      </td>
      <td>{{ $log->user->name ?? 'System' }}</td>
      <td>{{ $log->notes }}</td>
      <td>
        @if($log->isUnresolvedMismatch())
        <form method="POST" action="{{ route('asset-scans.resolve', $log) }}">@csrf
          <button class="btn-soft small-btn"><i class="bi bi-check-lg"></i> Mark Resolved</button>
        </form>
        @endif
      </td>
    </tr>
  @empty<tr><td colspan="9" class="empty-state">No scan logs yet. Scans will appear here once housekeeping starts scanning assets on mobile.</td></tr>@endforelse
  </tbody></table></div>
  <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
