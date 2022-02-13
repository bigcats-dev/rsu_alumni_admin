<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearBook extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'YEARBOOKS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'YEARBOOKS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'yearbook_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'detail', 'hyperlink', 'approved', 'status', 'user_create_id', 'note', 'user_update_id',
        'approved_at', 'user_approve_id', 'year', 'faculty_id', 'major_id'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
    ];

    public function files()
    {
        return $this->hasMany(YearBookFile::class,'yearbook_id','yearbook_id');
    }

    public function pdf()
    {
        return $this->hasMany(YearBookFile::class,'yearbook_id','yearbook_id')
            ->where("file_type",'application/pdf');
    }

    public function image()
    {
        return $this->hasOne(YearBookFile::class,'yearbook_id','yearbook_id')
            ->whereIn("file_type",['image/jpeg','image/png','image/jpg']);
    }
}
