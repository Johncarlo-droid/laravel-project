<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_no','user_id','facility_id','title','purpose','resources_needed',
        'start_at','end_at','status','reviewed_by','reviewed_at','rejection_reason','activity_proposal_id'
    ];

    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime', 'reviewed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function activityProposal() { return $this->belongsTo(ActivityProposal::class); }

    public function isPrePlotted(): bool
    {
        return $this->status === 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
