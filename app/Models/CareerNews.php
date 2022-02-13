<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class CareerNews extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'CAREER_NEWS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'CAREER_NEWS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'career_news_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'introduction', 'detail', 'start_date', 'end_date', 'location', 'files', 'approved', 'status', 'priority',
        'note', 'send_mail_type','user_create_id','user_update_id','user_approve_id','approved_at','link'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'priority' => 1,
    ];

    protected $appends = [];

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

    public function files()
    {
        return $this->hasMany(CareerNewsFile::class,'career_news_id','career_news_id');
    }

    public function pdf()
    {
        return $this->hasMany(CareerNewsFile::class,'career_news_id','career_news_id')
            ->where("file_type",'application/pdf');
    }

    public function image()
    {
        return $this->hasOne(CareerNewsFile::class,'career_news_id','career_news_id');
    }
}
