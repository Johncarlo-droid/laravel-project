<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $facilities = Facility::query()
            ->when($search !== '', fn ($q) => $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%")->orWhere('location','like',"%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        $reservations = FacilityReservation::with(['facility','user','reviewer'])->latest('start_at')->paginate(10, ['*'], 'reservations_page');
        return view('facilities.index', compact('facilities','reservations','search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:facilities,code'],
            'name' => ['required','string','max:150'],
            'location' => ['nullable','string','max:150'],
            'capacity' => ['nullable','integer','min:0'],
            'resources' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Facility::create($data);
        return redirect()->route('facilities.index')->with('success', 'Facility added successfully.');
    }

    public function edit(Facility $facility)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        return view('facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:facilities,code,'.$facility->id],
            'name' => ['required','string','max:150'],
            'location' => ['nullable','string','max:150'],
            'capacity' => ['nullable','integer','min:0'],
            'resources' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', false);
        $facility->update($data);
        return redirect()->route('facilities.index')->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $facility->delete();
        return redirect()->route('facilities.index')->with('success', 'Facility deleted successfully.');
    }

    public function createReservation()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        return view('facilities.reserve', compact('facilities'));
    }

    public function storeReservation(Request $request)
    {
        $data = $request->validate([
            'facility_id' => ['required','exists:facilities,id'],
            'title' => ['required','string','max:150'],
            'purpose' => ['nullable','string'],
            'resources_needed' => ['nullable','string'],
            'start_at' => ['required','date'],
            'end_at' => ['required','date','after:start_at'],
        ]);

        $conflict = FacilityReservation::where('facility_id', $data['facility_id'])
            ->whereIn('status', ['pending','approved'])
            ->where('start_at', '<', $data['end_at'])
            ->where('end_at', '>', $data['start_at'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_at' => 'Schedule conflict detected. This facility already has a pending or approved reservation in that time range.'])->withInput();
        }

        $data['reservation_no'] = 'FR-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        $data['user_id'] = auth()->id();
        $data['status'] = auth()->user()->canManageFacilities() ? 'approved' : 'pending';
        if ($data['status'] === 'approved') {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }
        FacilityReservation::create($data);
        return redirect()->route('facilities.index')->with('success', 'Reservation submitted successfully.');
    }

    public function approve(FacilityReservation $reservation)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $conflict = FacilityReservation::where('facility_id', $reservation->facility_id)
            ->where('id', '!=', $reservation->id)
            ->where('status', 'approved')
            ->where('start_at', '<', $reservation->end_at)
            ->where('end_at', '>', $reservation->start_at)
            ->exists();
        if ($conflict) {
            return back()->withErrors(['reservation' => 'This reservation conflicts with an already approved schedule.']);
        }
        $reservation->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'rejection_reason' => null]);
        return back()->with('success', 'Reservation approved.');
    }

    public function reject(Request $request, FacilityReservation $reservation)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $data = $request->validate(['rejection_reason' => ['nullable','string','max:500']]);
        $reservation->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'rejection_reason' => $data['rejection_reason'] ?? 'Rejected by FMO/Admin']);
        return back()->with('success', 'Reservation rejected.');
    }
}
