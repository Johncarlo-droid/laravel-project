<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetScanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id','user_id','expected_room','scanned_room','latitude','longitude','status','notes'
    ];

    public function item() { return $this->belongsTo(Item::class); }
    public function user() { return $this->belongsTo(User::class); }
}
