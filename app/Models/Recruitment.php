<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class Recruitment extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'RECRUITMENTS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'RECRUITMENTS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'recruitment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company', 'business_type', 'position', 'number_of_applications', 'nature_of_work', 'qualification', 'gender', 'age', 'education',
        'experience', 'other_qualification', 'salary', 'workplace', 'end_date', 'contact_name', 'tel', 'mobile', 'email', 'line_id', 'files',
        'approved', 'status', 'user_create_id', 'user_update_id', 'user_approve_id', 'approved_at', 'send_mail_type', 'note', 'active'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'active' => 1,
        'approved' => 0,
    ];

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }

    public function image()
    {
        return $this->hasOne(RecruitmentImage::class,'recruitment_id','recruitment_id');
    }


}
