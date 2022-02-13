<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'create_datetime';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'update_datetime';
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'MAJORS';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'major_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'major_code', 'major_name_th', 'status', 'major_name_en','remark'
    ];
    

    public $timestamps = true;

    public function faculty()
    {
        return $this->belongsTo(Faculty::class,'faculty_id','faculty_id');
    }
}
