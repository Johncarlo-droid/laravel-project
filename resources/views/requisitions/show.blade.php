@extends('layouts.admin', ['title' => 'Requisition Details'])
@section('content')
<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:18px">{{ $requisition->requisition_no }}</h2>
                <div class="module-note">Charge slip requisition details</div>
            </div>
            <span class="status {{ str_contains($requisition->status,'approved') ? 'approved' : ($requisition->status === 'rejected' ? 'low' : 'pending') }}">{{ $requisition->statusLabel() }}</span>
        </div>

        @if(auth()->user()->isSuperAdmin())
        <form method="POST" action="{{ route('requisitions.destroy', $requisition) }}" class="mb-2" onsubmit="return confirm('Delete this requisition permanently? This cannot be undone.');">
            @csrf @method('DELETE')
            <button class="btn-soft small-btn text-danger"><i class="bi bi-trash"></i> Delete Requisition (Super Admin)</button>
        </form>
        @endif

        <table class="kv-table">
            <tr><th><i class="bi bi-signpost-2 me-1"></i>Branch</th><td>{{ $requisition->branch ?: 'NU Clark' }}</td></tr>
            <tr><th><i class="bi bi-building me-1"></i>Department</th><td>{{ $requisition->department->name ?? 'N/A' }}</td></tr>
            <tr><th><i class="bi bi-wallet2 me-1"></i>Charge To</th><td>{{ $requisition->charge_to_budget_item ?: 'N/A' }}</td></tr>
            <tr><th><i class="bi bi-upc-scan me-1"></i>CSF No.</th><td>{{ $requisition->csf_no ?: 'N/A' }}</td></tr>
            <tr><th><i class="bi bi-person me-1"></i>Requested By</th><td>{{ $requisition->requested_by_name ?: ($requisition->user->name ?? 'N/A') }}</td></tr>
            <tr><th><i class="bi bi-calendar-event me-1"></i>Date Requested</th><td>{{ optional($requisition->requested_at)->format('Y-m-d H:i') ?: 'N/A' }}</td></tr>
            <tr><th><i class="bi bi-card-text me-1"></i>Purpose</th><td class="kv-wide">{{ $requisition->purpose ?: 'N/A' }}</td></tr>
        </table>

        <table class="data-table">
            <thead><tr><th>Item</th><th>Requested</th><th>Approved</th><th>Available Stock</th><th>Remarks</th></tr></thead>
            <tbody>
                @foreach($requisition->items as $item)
                <tr>
                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity_requested }}</td>
                    <td>{{ $item->quantity_approved ?? '-' }}</td>
                    <td>{{ $item->item->quantity ?? '-' }}</td>
                    <td>{{ $item->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="module-note mb-2" style="font-weight:700;color:var(--ink-900);font-size:12.5px">Approval Trail</div>
        <ul class="approval-timeline">
            <li class="{{ $requisition->assetReviewer ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $requisition->assetReviewer ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Asset Management</div>
                        <div class="step-name">{{ $requisition->assetReviewer->name ?? 'Awaiting review' }}</div>
                    </div>
                    <span class="step-meta {{ $requisition->assetReviewer ? 'signed' : 'pending' }}">{{ $requisition->assetReviewer ? 'Reviewed' : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $requisition->deanApprover ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $requisition->deanApprover ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">College Dean</div>
                        <div class="step-name">{{ $requisition->deanApprover->name ?? 'Awaiting approval' }}</div>
                    </div>
                    <span class="step-meta {{ $requisition->deanApprover ? 'signed' : 'pending' }}">{{ $requisition->deanApprover ? 'Approved' : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $requisition->executiveApprover ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $requisition->executiveApprover ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Executive Director</div>
                        <div class="step-name">{{ $requisition->executiveApprover->name ?? 'Awaiting approval' }}</div>
                    </div>
                    <span class="step-meta {{ $requisition->executiveApprover ? 'signed' : 'pending' }}">{{ $requisition->executiveApprover ? 'Approved' : 'Pending' }}</span>
                </div>
            </li>
        </ul>
    </div>

    <div class="surface p-3">
        <h3 class="module-title" style="font-size:16px">Actions</h3>

        @if($requisition->status === 'rejected')
            <div class="alert alert-danger">Rejected: {{ $requisition->rejection_reason }}</div>
        @endif

        @if(auth()->user()->isAdmin() && $requisition->isAwaitingAssetManagement())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">
                @csrf
                <p class="tiny">Asset Management may cut the requested quantity based on available stock.</p>
                @foreach($requisition->items as $line)
                    <div class="border rounded-3 p-2 mb-2">
                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $line->id }}">
                        <div class="tiny mb-1"><strong>{{ $line->item->name }}</strong> · Requested {{ $line->quantity_requested }} · Available {{ $line->item->quantity }}</div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Approved Qty</label>
                                <input type="number" name="items[{{ $loop->index }}][quantity_approved]" class="form-control" min="0" max="{{ $line->quantity_requested }}" value="{{ old('items.'.$loop->index.'.quantity_approved', $line->quantity_approved ?? min($line->quantity_requested, $line->item->quantity)) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="items[{{ $loop->index }}][remarks]" class="form-control" value="{{ old('items.'.$loop->index.'.remarks', $line->remarks) }}" placeholder="Example: cut to available stock">
                            </div>
                        </div>
                    </div>
                @endforeach
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Forward to College Dean</button>
            </form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @elseif(auth()->user()->isDeanApprover() && $requisition->isAwaitingCollegeDean())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">@csrf<button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Approve and Forward to Executive Director</button></form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @elseif(auth()->user()->isExecutiveApprover() && $requisition->isAwaitingExecutiveDirector())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">@csrf<button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Final Approve Requisition</button></form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @else
            <div class="empty-state">No action available for your account at the current stage.</div>
        @endif
    </div>
</div>
@endsection
