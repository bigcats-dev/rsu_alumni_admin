<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingNew extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'TRAINING_NEWS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'TRAINING_NEWS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'training_news_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'introduction', 'detail', 'start_date', 'end_date', 'location', 'files', 'approved', 'status', 'priority', 'user_create_id',
        'note', 'send_mail_type', 'home_page','user_update_id','user_approve_id','approved_at'
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

    public function image()
    {
        return $this->hasOne(TrainingNewImage::class,'training_news_id','training_news_id');
    }
}
