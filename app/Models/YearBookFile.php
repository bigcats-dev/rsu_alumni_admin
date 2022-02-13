<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearBookFile extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'YEARBOOK_FILES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'YEARBOOK_FILES_ID_SEQ';
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
        'file_name', 'file_origin_name', 'file_type', 'file_size','yearbook_id','file_path'
    ];

    public $timestamps = false;
}
