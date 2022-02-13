<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class AwardType extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'AWARD_TYPES';
    /**
     * The sequence name
     *
     * @var string
     *
     */
    public $sequence = 'AWARD_TYPES_ID_SEQ';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'award_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'award_type_name', 'active', 'award_sub_type_id',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => 1,
    ];

    public function award_sub_types()
    {
        return $this->hasMany(AwardType::class, "award_sub_type_id", "award_type_id");
    }

    public function scopeNoneSubType($query)
    {
        return $query->whereNull("award_sub_type_id");
    }

    public function scopeSubType($query, $id = null)
    {
        if (is_null($id)) return $query->whereNotNull("award_sub_type_id");
        else return $query->where("award_sub_type_id", $id);
    }

    public function scopeActive($query)
    {
        return $query->where("active",1);
    }

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('award_type_id');
        });
    }
}
