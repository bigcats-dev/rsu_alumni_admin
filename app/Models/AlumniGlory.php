<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class AlumniGlory extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ALUMNI_GLORIES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ALUMNI_GLORIES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'alumni_glory_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'year', 'faculty_id', 'major_id', 'position', 'company', 'education_level_id', 'award_date', 'award_type_id', 'award_sub_type_id',
        'approved', 'status', 'active', 'user_create_id', 'user_update_id', 'user_approve_id', 'approved_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'approved' => 0,
        'active' => 1,
    ];

    public function setAwardDateAttribute($value)
    {
        $this->attributes['award_date'] = Helper::isEmptyOrNull($value)
            ? null
            : Helper::convertToDateEn($value);
    }

    public function image()
    {
        return $this->hasOne(AlumniGloryImage::class, 'alumni_glory_id', 'alumni_glory_id');
    }
}
