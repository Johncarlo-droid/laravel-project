@extends('layouts.admin', ['title' => 'Activity Proposal Details'])
@section('content')
<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:18px">{{ $proposal->proposal_no }}</h2>
                <div class="module-note">{{ $proposal->title }}</div>
            </div>
            <span class="status {{ $proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending') }}">{{ $proposal->statusLabel() }}</span>
        </div>

        <div class="row g-2 mb-3 tiny">
            <div class="col-md-6"><strong>Organization:</strong> {{ $proposal->organization_name }}</div>
            <div class="col-md-6"><strong>Requested By:</strong> {{ $proposal->user->name ?? 'N/A' }} @if($proposal->requester_position)({{ $proposal->requester_position }})@endif</div>
            <div class="col-md-6"><strong>Venue:</strong> {{ $proposal->facility->name ?? 'N/A' }} — {{ $proposal->facility->location ?? '' }} @if($proposal->venue_other_note)<span class="tiny-2">({{ $proposal->venue_other_note }})</span>@endif</div>
            <div class="col-md-6"><strong>Schedule:</strong> {{ optional($proposal->start_at)->format('Y-m-d H:i') }} – {{ optional($proposal->end_at)->format('Y-m-d H:i') }}</div>
            <div class="col-md-6"><strong>Day(s) of Activity:</strong> {{ $proposal->activity_days ?: 'N/A' }}</div>
            <div class="col-md-6"><strong>Expected Attendees:</strong> {{ $proposal->participants_count }}</div>
            @if($proposal->speaker_name)<div class="col-12"><strong>Speaker:</strong> {{ $proposal->speaker_name }}</div>@endif
            <div class="col-md-6"><strong>Venue Slot:</strong> {{ ucfirst($proposal->reservation->status ?? 'N/A') }} @if($proposal->reservation && $proposal->reservation->isPrePlotted())<span class="tiny text-muted">(pre-plotted — not yet confirmed)</span>@endif</div>
            <div class="col-12"><strong>Other Items Needed and Services:</strong> {{ $proposal->equipment_needed ?: 'None specified' }}</div>
            <div class="col-12"><strong>Program Flow:</strong><br>{!! nl2br(e($proposal->program_flow)) !!}</div>
        </div>

        <div class="mt-3 tiny">
            <div><strong>Prepared By — Adviser / Program Chair:</strong> {{ $proposal->adviserSigner->name ?? ($proposal->adviser->name ?? 'Assigned') . ' — pending' }} @if($proposal->adviser_signed_at) — signed {{ $proposal->adviser_signed_at->format('Y-m-d H:i') }} @endif</div>
            <div><strong>Noted By — Dean / Principal:</strong> {{ $proposal->departmentSigner->name ?? ($proposal->departmentApprover->name ?? 'Assigned') . ' — pending' }} @if($proposal->department_signed_at) — signed {{ $proposal->department_signed_at->format('Y-m-d H:i') }} @endif</div>
            <div><strong>Noted By — SDAO:</strong> {{ $proposal->sdaoSigner->name ?? ($proposal->sdao->name ?? 'Assigned') . ' — pending' }} @if($proposal->sdao_signed_at) — signed {{ $proposal->sdao_signed_at->format('Y-m-d H:i') }} @endif</div>
            <div><strong>Reviewed By — Facilities Management:</strong> {{ $proposal->fmoSigner->name ?? ($proposal->facilitiesMgmt->name ?? 'Assigned') . ' — pending' }} @if($proposal->fmo_signed_at) — signed {{ $proposal->fmo_signed_at->format('Y-m-d H:i') }} @endif</div>
            <div><strong>Reviewed By — Academic Director:</strong> {{ $proposal->academicDirectorSigner->name ?? ($proposal->academicDirector->name ?? 'Assigned') . ' — pending' }} @if($proposal->academic_director_signed_at) — signed {{ $proposal->academic_director_signed_at->format('Y-m-d H:i') }} @endif</div>
            <div><strong>Approved By — Executive Director:</strong> {{ $proposal->executiveSigner->name ?? ($proposal->executiveDirector->name ?? 'Assigned') . ' — pending' }} @if($proposal->executive_signed_at) — signed {{ $proposal->executive_signed_at->format('Y-m-d H:i') }} @endif</div>
        </div>
        <div class="tiny text-muted mt-2">Digital signature = the approver's authenticated account confirming approval; no wet ink or physical routing required.</div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title" style="font-size:16px">Actions</h3>

        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        @if($proposal->status === 'rejected')
            <div class="alert alert-danger">Rejected at "{{ $proposal->statusLabel() }}" stage by {{ $proposal->rejecter->name ?? 'N/A' }}: {{ $proposal->rejection_reason }}</div>
        @endif

        @php $user = auth()->user(); @endphp

        @if($proposal->isAwaitingAdviser() && ($user->isAdmin() || $user->id === $proposal->adviser_id))
            <form method="POST" action="{{ route('activity-proposals.approve-adviser', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Adviser/Program Chair</button>
            </form>
        @endif

        @if($proposal->isAwaitingNoted())
            @if(!$proposal->department_signed_at && ($user->isAdmin() || $user->id === $proposal->department_approver_id))
            <form method="POST" action="{{ route('activity-proposals.sign-dean', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Dean/Principal</button>
            </form>
            @endif
            @if(!$proposal->sdao_signed_at && ($user->isAdmin() || $user->id === $proposal->sdao_id))
            <form method="POST" action="{{ route('activity-proposals.sign-sdao', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as SDAO</button>
            </form>
            @endif
            <div class="tiny text-muted mb-2">Both Dean/Principal and SDAO must sign before this moves to the Reviewed By stage.</div>
        @endif

        @if($proposal->isAwaitingReview())
            @if(!$proposal->fmo_signed_at && ($user->isAdmin() || $user->isFmo()))
            <form method="POST" action="{{ route('activity-proposals.sign-facilities', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Facilities Management</button>
            </form>
            @endif
            @if(!$proposal->academic_director_signed_at && ($user->isAdmin() || $user->id === $proposal->academic_director_id))
            <form method="POST" action="{{ route('activity-proposals.sign-academic-director', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Academic Director</button>
            </form>
            @endif
            <div class="tiny text-muted mb-2">Both Facilities Management and Academic Director must sign before this moves to Executive Director for final approval.</div>
        @endif

        @if($proposal->isAwaitingExecutive() && ($user->isAdmin() || $user->id === $proposal->executive_director_id))
            <form method="POST" action="{{ route('activity-proposals.approve-executive', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Final Approve & Confirm Venue</button>
            </form>
        @endif

        @if(!in_array($proposal->status, ['approved','rejected']))
            @php
                $canReject = $user->isAdmin()
                    || ($proposal->isAwaitingAdviser() && $user->id === $proposal->adviser_id)
                    || ($proposal->isAwaitingNoted() && in_array($user->id, [$proposal->department_approver_id, $proposal->sdao_id]))
                    || ($proposal->isAwaitingReview() && ($user->isFmo() || $user->id === $proposal->academic_director_id))
                    || ($proposal->isAwaitingExecutive() && $user->id === $proposal->executive_director_id);
            @endphp
            @if($canReject)
            <form method="POST" action="{{ route('activity-proposals.reject', $proposal) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="rejection_reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Proposal</button>
            </form>
            @endif
        @endif

        @if($proposal->status === 'approved')
            <div class="alert alert-success">Fully approved. Venue is confirmed — no other reservation can be approved for this facility during this time.</div>
        @endif

        <a class="btn-soft w-100 justify-content-center mt-2" href="{{ route('activity-proposals.index') }}"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
