<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'menu_id';
    protected $fillable = [
        'name',
        'route',
        'icon',
        'prefix',
        'parent_id',
        'sort',
    ];
    public $timestamps = false;

    public function childs()
    {
        return $this->hasMany(Menu::class,'parent_id','menu_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class,"menu_id","menu_id");
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope("order", function (Builder $builder) {
            $builder->orderBy("sort", "asc");
        });
    }

    public function scopeParent($query)
    {
        return $query->whereNull("parent_id");
    }
}
