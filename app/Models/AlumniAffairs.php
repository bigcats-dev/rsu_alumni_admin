<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class AlumniAffairs extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ALUMNI_AFFAIRS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ALUMNI_AFFAIRS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'alumni_affairs_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','introduction','detail','start_date','end_date','approved','status','user_create_id','user_update_id','user_approve_id',
        'approved_at','hyperlink','hyperlink_type'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'approved' => 0,
    ];

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }

    public function image()
    {
        return $this->hasOne(AlumniAffairsImage::class,'alumni_affairs_id','alumni_affairs_id');
    }
}
