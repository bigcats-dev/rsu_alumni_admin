<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ACTIVITIES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ACTIVITIES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'objective', 'nature_of_operation', 'participant_properties', 'location', 'max_participants', 'unlimited_participants',
        'expenses', 'free_activities', 'approved', 'status', 'user_create_id', 'officers', 'note', 'send_mail_type', 'home_page', 'user_update_id',
        'priority','approved_at','user_approve_id'
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        "officers" => "array",
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'home_page' => 1,
        'priority' => 1,
    ];

    public function setOfficersAttribute($value)
    {
        $officers = [];
        foreach ($value as $array_item) {
            if (!is_null($array_item["name"])) {
                $officers[] = $array_item;
            }
        }
        $this->attributes["officers"] = json_encode($officers);
    }

    public function activity_schedules()
    {
        return $this->hasMany(ActivitySchedule::class, 'activity_id', 'activity_id')
            ->orderBy('schedule_date');
    }

    public function image()
    {
        return $this->hasOne(ActivityImage::class,'activity_id','activity_id');
    }
}
