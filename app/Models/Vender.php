<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vender extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'VENDERS';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'vender_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vender_id', 'corporation_name', 'corporation_no', 'address', 'email', 'approved', 'status', 'note', 'active', 'user_create_id', 'user_update_id',
        'approved_at', 'user_approve_id', 'coordinators', 'tel_1', 'tel_2', 'tel_3'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'active' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        "coordinators" => "array",
    ];

    public function setCoordinatorsAttribute($value)
    {
        $officers = [];
        foreach ($value as $array_item) {
            if (!is_null($array_item["name"])) {
                $officers[] = $array_item;
            }
        }
        $this->attributes["coordinators"] = json_encode($officers);
    }

    public function files()
    {
        return $this->hasMany(VenderFile::class, "vender_id", "vender_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,"vender_id","id");
    }

     
}
