<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class ActivityScheduleDetail extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ACTIVITY_SCHEDULE_DETAILS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ACTIVITY_SCHEDULE_DES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'activity_schedule_detail_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time_start','time_end','detail','activity_schedule_id'
    ];

    public $timestamps = false;
    
    public function activity_schedule()
    {
        return $this->belongsTo(ActivitySchedule::class,'activity_schedule_id','activity_schedule_id');
    }
}
