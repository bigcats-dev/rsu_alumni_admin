<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniGloryImage extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ALUMNI_GLORY_IMAGES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ALUMNI_GLORY_IMAGES_ID_SEQ';
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
        'file_name', 'file_origin_name', 'file_type', 'file_size','alumni_glory_id','file_path'
    ];

    public $timestamps = false;
}
