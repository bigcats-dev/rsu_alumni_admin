<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivityRoomSchedule extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     * 
     */
    protected $table = "ACTIVITY_ROOM_SCHEDULES";
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = "ACTIVITY_ROOM_SCHEDULES_ID_SEQ";
    /**
     * The primary key associated with the table.
     *
     * @var string
     * 
     */
    protected $primaryKey = "activity_room_schedules_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * 
     */
    protected $fillable = [
        "date_start", "date_end", "room_group_uid", "room_sub_group_uid", "room_uid", "description","price"
    ];
     /**
     * The attributes set timestamp.
     *
     * @var boolean
     * 
     */
    public $timestamps = true;
    /**
     * The attributes that should be cast.
     *
     * @var array
     * 
     */
    protected $casts = [
        "price" => "float",
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class,"activity_id","activity_id");
    }

    public function setDateStartAttribute($value)
    {
        $this->attributes["date_start"] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }

    public function setDateEndAttribute($value)
    {
        $this->attributes["date_end"] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }
    public function getBookAttribute()
    {
        return Room::where(DB::raw("convert(ROOM_UID,'utf8','us7ascii')"), $this->room_uid)->first();
    }
}
