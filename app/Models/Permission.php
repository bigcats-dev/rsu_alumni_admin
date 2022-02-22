<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'permission_id';
    protected $fillable = [
        'name',
        'slug',
    ];
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permissions','permission_id','role_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menu_id','menu_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope("order", function (Builder $builder) {
            $builder->orderBy("permissions.sort", "asc");
        });
    }
}
