<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'GALLERIES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'GALLERIES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'gallery_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name', 'file_origin_name', 'file_type', 'file_size', 'album_id', 'file_path','cover_page'
    ];

    protected $appends = ["url", "action_del"];

    public function getUrlAttribute()
    {
        return asset("storage/" . $this->file_path);
    }

    public function getActionDelAttribute()
    {
        return route("album.gallery.destroy", ["gallery" => $this->gallery_id]);
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }
}
