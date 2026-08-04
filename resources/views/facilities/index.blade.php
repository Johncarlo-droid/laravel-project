@extends('layouts.admin', ['title' => 'Facilities Management'])

@section('content')
<div class="module-head">
  <div><h2 class="module-title">Facilities Management Office</h2><div class="module-note">Digital venue reservations with schedule conflict detection and approval workflow.</div></div>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn-primaryx" href="{{ route('facilities.reserve') }}"><i class="bi bi-calendar-plus"></i> Reserve Facility</a>
    @if(auth()->user()->canManageFacilities())<a class="btn-primaryx" href="{{ route('facilities.create') }}"><i class="bi bi-building-add"></i> Add Facility</a>@endif
  </div>
</div>

<div class="surface p-3 mb-3">
  <form class="search-strip" method="GET">
    <i class="bi bi-search"></i><input class="search-input" name="search" value="{{ $search }}" placeholder="Search facility, code, or location">
    <button class="btn-soft small-btn" type="submit">Search</button>
  </form>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>Code</th><th>Facility</th><th>Location</th><th>Capacity</th><th>Resources</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
      @forelse($facilities as $facility)
        <tr>
          <td>{{ $facility->code }}</td><td>{{ $facility->name }}</td><td>{{ $facility->location ?? 'N/A' }}</td><td>{{ $facility->capacity }}</td><td>{{ $facility->resources ?? 'N/A' }}</td>
          <td><span class="status {{ $facility->is_active ? 'approved' : 'low' }}">{{ $facility->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td>@if(auth()->user()->canManageFacilities())<a href="{{ route('facilities.edit', $facility) }}" class="btn-soft small-btn">Edit</a>@else — @endif</td>
        </tr>
      @empty<tr><td colspan="7" class="empty-state">No facilities found.</td></tr>@endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $facilities->links() }}</div>
</div>

<div class="surface p-3">
  <div class="module-head mb-2"><div><h2 class="module-title">Reservation Requests</h2><div class="module-note">Pending and approved schedules are checked to prevent overlapping reservations.</div></div></div>
  <div class="table-responsive">
    <table class="data-table">
      <thead><tr><th>No.</th><th>Requester</th><th>Facility</th><th>Title</th><th>Schedule</th><th>Status</th><th>FMO Action</th></tr></thead>
      <tbody>
      @forelse($reservations as $reservation)
        <tr>
          <td>{{ $reservation->reservation_no }}</td>
          <td>{{ $reservation->user->name ?? 'N/A' }}</td>
          <td>{{ $reservation->facility->name ?? 'N/A' }}</td>
          <td>{{ $reservation->title }}<div class="tiny">{{ $reservation->purpose }}</div></td>
          <td>{{ $reservation->start_at->format('M d, Y h:i A') }}<br><span class="tiny">to {{ $reservation->end_at->format('M d, Y h:i A') }}</span></td>
          <td><span class="status {{ $reservation->status === 'approved' ? 'approved' : ($reservation->status === 'rejected' ? 'low' : 'pending') }}">{{ ucfirst($reservation->status) }}</span></td>
          <td>
            @if(auth()->user()->canManageFacilities() && $reservation->status === 'pending')
              <form class="d-inline" method="POST" action="{{ route('facilities.reservations.approve', $reservation) }}">@csrf<button class="btn-approve">Approve</button></form>
              <form class="d-inline" method="POST" action="{{ route('facilities.reservations.reject', $reservation) }}">@csrf<input type="hidden" name="rejection_reason" value="Schedule or resource unavailable"><button class="btn-reject">Reject</button></form>
            @else
              <span class="tiny">{{ $reservation->reviewer->name ?? 'No action yet' }}</span>
            @endif
          </td>
        </tr>
      @empty<tr><td colspan="7" class="empty-state">No reservation requests yet.</td></tr>@endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $reservations->links() }}</div>
</div>
@endsection
