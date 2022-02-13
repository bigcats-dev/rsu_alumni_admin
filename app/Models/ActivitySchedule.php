<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class ActivitySchedule extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ACTIVITY_SCHEDULES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ACTIVITY_SCHEDULES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'activity_schedule_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_date','activity_id'
    ];

    public $timestamps = false;

    public function activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','activity_id');
    }

    public function activity_schedule_details()
    {
        return $this->hasMany(ActivityScheduleDetail::class,"activity_schedule_id","activity_schedule_id")
            ->orderBy("time_start");
    }

    public function setScheduleDateAttribute($value)
    {
        $this->attributes['schedule_date'] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }
}
