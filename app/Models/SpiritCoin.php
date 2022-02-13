<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiritCoin extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'SPIRIT_COIN';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'SPIRIT_COIN_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'spirit_coin_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'detail', 'approved', 'status', 'user_create_id', 'note', 'active', 'user_update_id',
        'priority', 'approved_at', 'user_approve_id'
    ];
  
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'active' => 1,
        'priority' => 1,
    ];

    public function image()
    {
        return $this->hasOne(SpiritCoinImage::class,"spirit_coin_id","spirit_coin_id");
    }
}
