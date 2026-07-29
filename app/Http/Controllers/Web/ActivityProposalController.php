<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityProposal;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\User;
use App\Notifications\ActivityProposalStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityProposalController extends Controller
{
    // Matches "OTHER ITEMS NEEDED AND SERVICES" checklist on the School Facilities Reservation Form
    public const EQUIPMENT_OPTIONS = ['Table', 'Chairs', 'ITSO Services', 'Sound System', 'Flag', 'Janitors', 'Electricians', 'Others'];

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ActivityProposal::with(['user', 'department', 'adviser', 'departmentApprover', 'sdao', 'facilitiesMgmt', 'academicDirector', 'executiveDirector', 'facility']);

        if ($user->isAdmin()) {
            // sees everything
        } elseif ($user->isAdviserApprover()) {
            $query->where('adviser_id', $user->id);
        } elseif ($user->isDeanApprover()) {
            $query->where('department_approver_id', $user->id);
        } elseif ($user->isSdaoApprover()) {
            $query->where('sdao_id', $user->id);
        } elseif ($user->isFmo()) {
            $query->where(function ($q) use ($user) {
                $q->where('facilities_mgmt_id', $user->id)->orWhereNull('facilities_mgmt_id');
            })->whereIn('status', ['pending_review', 'pending_executive', 'approved', 'rejected']);
        } elseif ($user->isAcademicDirectorApprover()) {
            $query->where('academic_director_id', $user->id);
        } elseif ($user->isExecutiveApprover()) {
            $query->where('executive_director_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $proposals = $query->latest()->paginate(10);
        return view('activity_proposals.index', compact('proposals'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $advisers = User::where('role', 'approver')->where('approver_type', 'adviser')->orderBy('name')->get();
        $deansPrincipals = User::where('role', 'approver')->where('approver_type', 'dean')->orderBy('name')->get();
        $sdaoOfficers = User::where('role', 'approver')->where('approver_type', 'sdao')->orderBy('name')->get();
        $facilitiesStaff = User::where('role', 'fmo')->orderBy('name')->get();
        $academicDirectors = User::where('role', 'approver')->where('approver_type', 'academic_director')->orderBy('name')->get();
        $executiveDirectors = User::where('role', 'approver')->where('approver_type', 'executive')->orderBy('name')->get();

        return view('activity_proposals.create', compact('facilities', 'advisers', 'deansPrincipals', 'sdaoOfficers', 'facilitiesStaff', 'academicDirectors', 'executiveDirectors') + [
            'equipmentOptions' => self::EQUIPMENT_OPTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'organization_name' => ['required', 'string', 'max:150'],
            'requester_position' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'speaker_name' => ['nullable', 'string', 'max:150'],
            'activity_days' => ['nullable', 'string', 'max:150'],
            'program_flow' => ['required', 'string'],
            'participants_count' => ['required', 'integer', 'min:1'],
            'equipment_needed' => ['nullable', 'array'],
            'facility_id' => ['required', 'exists:facilities,id'],
            'venue_other_note' => ['nullable', 'string', 'max:150'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'adviser_id' => ['required', 'exists:users,id'],
            'department_approver_id' => ['required', 'exists:users,id'],
            'sdao_id' => ['required', 'exists:users,id'],
            'facilities_mgmt_id' => ['required', 'exists:users,id'],
            'academic_director_id' => ['required', 'exists:users,id'],
            'executive_director_id' => ['required', 'exists:users,id'],
        ]);

        $user = auth()->user();

        $reservation = FacilityReservation::create([
            'reservation_no' => 'FR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => $user->id,
            'facility_id' => $data['facility_id'],
            'title' => $data['title'],
            'purpose' => 'Activity Proposal: ' . $data['title'],
            'resources_needed' => implode(', ', $data['equipment_needed'] ?? []),
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'status' => 'pending',
        ]);

        $hasConflict = FacilityReservation::where('facility_id', $data['facility_id'])
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_at', '<', $data['end_at'])
            ->where('end_at', '>', $data['start_at'])
            ->exists();

        $proposal = ActivityProposal::create(array_merge($data, [
            'proposal_no' => 'AP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'equipment_needed' => implode(', ', $data['equipment_needed'] ?? []),
            'facility_reservation_id' => $reservation->id,
            'status' => 'pending_adviser',
        ]));

        $reservation->update(['activity_proposal_id' => $proposal->id]);

        if ($proposal->adviser) {
            $proposal->adviser->notify(new ActivityProposalStatusNotification(
                $proposal, 'New Activity Proposal Awaiting Your Signature',
                $user->name . ' submitted "' . $proposal->title . '" for the "Prepared By" adviser signature.'
            ));
        }

        $message = 'Proposal submitted and routed digitally: Adviser -> Noted By (Dean/Principal and SDAO) -> Reviewed By (Facilities Mgmt. and Academic Director) -> Approved By (Executive Director). Your venue slot is pre-plotted while it is routed.';
        if ($hasConflict) {
            $message .= ' Note: another request already has this venue pre-plotted or approved for an overlapping time.';
        }

        return redirect()->route('activity-proposals.show', $proposal)->with('success', $message);
    }

    public function show(ActivityProposal $activityProposal)
    {
        $activityProposal->load(['user', 'department', 'adviser', 'departmentApprover', 'sdao', 'facilitiesMgmt', 'academicDirector', 'executiveDirector', 'facility', 'reservation', 'adviserSigner', 'departmentSigner', 'sdaoSigner', 'fmoSigner', 'academicDirectorSigner', 'executiveSigner', 'rejecter']);
        return view('activity_proposals.show', ['proposal' => $activityProposal]);
    }

    public function approveAdviser(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingAdviser() && ($user->isAdmin() || $user->id === $p->adviser_id), 403);

        $p->update(['adviser_signed_by' => $user->id, 'adviser_signed_at' => now(), 'status' => 'pending_noted']);
        $this->notifyBoth($p, $p->departmentApprover, $p->sdao, 'Activity Proposal Awaiting "Noted By" Signature', 'The adviser signed "' . $p->title . '". It now needs to be noted.');

        return back()->with('success', 'Signed as Adviser/Program Chair. Forwarded for Noted By signatures (Dean/Principal & SDAO).');
    }

    public function signDean(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingNoted() && ($user->isAdmin() || $user->id === $p->department_approver_id), 403);
        $p->update(['department_signed_by' => $user->id, 'department_signed_at' => now()]);
        $this->advanceIfBothSigned($p, 'department_signed_at', 'sdao_signed_at', 'pending_review', [$p->facilitiesMgmt, $p->academicDirector], '"Reviewed By" stage');
        return back()->with('success', 'Signed as Dean/Principal.');
    }

    public function signSdao(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingNoted() && ($user->isAdmin() || $user->id === $p->sdao_id), 403);
        $p->update(['sdao_signed_by' => $user->id, 'sdao_signed_at' => now()]);
        $this->advanceIfBothSigned($p, 'department_signed_at', 'sdao_signed_at', 'pending_review', [$p->facilitiesMgmt, $p->academicDirector], '"Reviewed By" stage');
        return back()->with('success', 'Signed as SDAO.');
    }

    public function signFacilities(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingReview() && ($user->isAdmin() || $user->isFmo()), 403);
        $p->update(['fmo_signed_by' => $user->id, 'fmo_signed_at' => now()]);
        $this->advanceIfBothSigned($p, 'fmo_signed_at', 'academic_director_signed_at', 'pending_executive', [$p->executiveDirector], '"Approved By" stage');
        return back()->with('success', 'Signed as Facilities Management.');
    }

    public function signAcademicDirector(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingReview() && ($user->isAdmin() || $user->id === $p->academic_director_id), 403);
        $p->update(['academic_director_signed_by' => $user->id, 'academic_director_signed_at' => now()]);
        $this->advanceIfBothSigned($p, 'fmo_signed_at', 'academic_director_signed_at', 'pending_executive', [$p->executiveDirector], '"Approved By" stage');
        return back()->with('success', 'Signed as Academic Director.');
    }

    public function approveExecutive(ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        abort_unless($p->isAwaitingExecutive() && ($user->isAdmin() || $user->id === $p->executive_director_id), 403);

        $reservation = $p->reservation;
        if ($reservation) {
            $conflict = FacilityReservation::where('facility_id', $reservation->facility_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'approved')
                ->where('start_at', '<', $reservation->end_at)
                ->where('end_at', '>', $reservation->start_at)
                ->exists();

            if ($conflict) {
                return back()->withErrors(['reservation' => 'This venue and time slot is already confirmed for another activity. Coordinate an alternate room or time before approving.']);
            }
            $reservation->update(['status' => 'approved', 'reviewed_by' => $user->id, 'reviewed_at' => now(), 'rejection_reason' => null]);
        }

        $p->update(['executive_signed_by' => $user->id, 'executive_signed_at' => now(), 'status' => 'approved']);
        $p->user->notify(new ActivityProposalStatusNotification($p, 'Activity Proposal Approved', 'Your activity proposal "' . $p->title . '" is fully approved. The venue slot is now confirmed.'));

        return back()->with('success', 'Approved as Executive Director. Venue slot is now confirmed.');
    }

    public function reject(Request $request, ActivityProposal $activityProposal)
    {
        $p = $activityProposal;
        $user = auth()->user();
        $canReject = $user->isAdmin()
            || ($p->isAwaitingAdviser() && $user->id === $p->adviser_id)
            || ($p->isAwaitingNoted() && in_array($user->id, [$p->department_approver_id, $p->sdao_id]))
            || ($p->isAwaitingReview() && ($user->isFmo() || $user->id === $p->academic_director_id))
            || ($p->isAwaitingExecutive() && $user->id === $p->executive_director_id);
        abort_unless($canReject, 403);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $p->update([
            'status' => 'rejected', 'rejected_stage' => $p->status, 'rejected_by' => $user->id,
            'rejected_at' => now(), 'rejection_reason' => $data['rejection_reason'],
        ]);

        if ($p->reservation) {
            $p->reservation->update(['status' => 'rejected', 'reviewed_by' => $user->id, 'reviewed_at' => now(), 'rejection_reason' => $data['rejection_reason']]);
        }

        $p->user->notify(new ActivityProposalStatusNotification($p, 'Activity Proposal Rejected', 'Your activity proposal "' . $p->title . '" was rejected: ' . $data['rejection_reason']));

        return back()->with('success', 'Proposal rejected and requester notified.');
    }

    private function advanceIfBothSigned(ActivityProposal $p, string $fieldA, string $fieldB, string $nextStatus, array $notifyUsers, string $stageLabel): void
    {
        $p->refresh();
        if ($p->{$fieldA} && $p->{$fieldB}) {
            $p->update(['status' => $nextStatus]);
            foreach (array_filter($notifyUsers) as $notifyUser) {
                $notifyUser->notify(new ActivityProposalStatusNotification($p, 'Activity Proposal Awaiting ' . $stageLabel, 'Both required signatures on "' . $p->title . '" are complete. It now needs your signature for the ' . $stageLabel . '.'));
            }
        }
    }

    private function notifyBoth(ActivityProposal $p, $userA, $userB, string $subject, string $message): void
    {
        foreach (array_filter([$userA, $userB]) as $notifyUser) {
            $notifyUser->notify(new ActivityProposalStatusNotification($p, $subject, $message));
        }
    }
}
