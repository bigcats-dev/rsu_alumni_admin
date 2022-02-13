<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'ALBUMS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'ALBUMS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'album_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'detail', 'approved', 'status', 'user_create_id', 'note', 'active', 'user_update_id',
        'approved_at', 'user_approve_id'
    ];
  
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'active' => 1,
        'approved' => 0,
    ];

    public function gallerys()
    {
        return $this->hasMany(Gallery::class,'album_id','album_id');
    }
}
