<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ActivityRoom extends Model
{
    use HasFactory;
    protected $table = "ACTIVITY_ROOMS";
    // sequence name
    public $sequence = "ACTIVITY_ROOMS_ID_SEQ";
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "activity_room_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "type", "price", "detail", "room_group_uid", "room_sub_group_uid", "room_uid","activity_id"
    ];
    public $timestamps = true;
    /**
     * The other attribute
     * 
     * @var array
     */
    protected $appends = ["book", "room","type_name"];
    /**
     * The attribite enum
     *
     * @var array
     */
    protected const TYPENAME = [
        "1" => "มหาวิทยาลัยรังสิต",
        "2" => "ภายนอกมหาวิทยาลัย",
        "" => "ไม่ได้ระบุสถานที่",
    ];

    public function getTypeNameAttribute()
    {
        return Arr::get(self::TYPENAME, $this->type);
    }

    public static function getTypeName($type){
        if (Arr::has(self::TYPENAME,$type))
            return Arr::get(self::TYPENAME, $type);
        else
            return "ไม่ได้ระบุสถานที่";
    }
    
    public function getBookAttribute()
    {
        return self::getTypeName($this->type)
            .($this->type == "2" 
                ? sprintf(" , ราคา %.2f , รายละเอียดเพิ่มเติม %s",$this->price,$this->detail)
                : $this->activity->room_schedule);
    }
    public function getRoomAttribute()
    {
        return Room::where(DB::raw("convert(ROOM_UID,'utf8','us7ascii')"), $this->room_uid)->first();
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class,"activity_id","activity_id");
    }
}
