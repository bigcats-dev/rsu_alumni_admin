<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiritCoinActivityImage extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'SPIRIT_COIN_ACTIVITY_IMAGES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'SPIRIT_COIN_AC_IMAGE_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name', 'file_origin_name', 'file_type', 'file_size','spirit_coin_activity_id','file_path'
    ];

    public $timestamps = false;
}
