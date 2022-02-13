<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
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
    protected $table = 'FACULTYS';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'faculty_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'faculty_code', 'faculty_name_th', 'status', 'faculty_name_en','remark'
    ];
    

    public $timestamps = true;

    public function majors()
    {
        return $this->hasMany(Major::class,'faculty_id','faculty_id');
    }
}
