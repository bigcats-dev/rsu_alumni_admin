<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class SpiritCoinActivity extends Model
{
    use HasFactory;
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'SPIRIT_COIN_ACTIVITIES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'SPIRIT_COIN_ACTIVTIES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'spirit_coin_activity_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'detail', 'introduction', 'start_date', 'end_date', 'location', 'approved', 'status', 'user_create_id', 'note',
        'user_update_id', 'approved_at', 'user_approve_id'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
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
        return $this->hasOne(SpiritCoinActivityImage::class, "spirit_coin_activity_id", "spirit_coin_activity_id");
    }
}
