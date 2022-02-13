<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory , UserTrait;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'SOCIALS';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'SOCIALS_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'social_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'hyperlink', 'user_create_id', 'user_update_id', 'active', 'priority','icon_name','icon_origin_name','icon_type',
        'icon_size','icon_path'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => 1,
        'priority' => 1,
    ];

    protected $appends = ["icon"];

    public function getIconAttribute()
    {   
        if (is_null($this->name)) return null;
        
        return (object)[
            "file_path" => $this->icon_path,
            "file_origin_name" => $this->icon_origin_name,
            "file_size" => $this->icon_size,
        ];
    }
}
