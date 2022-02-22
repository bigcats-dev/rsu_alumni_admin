<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'role_name_th',
        'role_slug',
        'role_level',
    ];

    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permissions','role_id','permission_id');
    }

    public function hasPermission($permission)
    {
        return $this->permissions->contains("slug", $permission);
    }

    public function scopeNotSuperAdmin($query)
    {
        return $query->where("role_slug" , "<>" ,"super-administrator");
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope("order", function (Builder $builder) {
            $builder->orderBy("roles.role_id", "asc");
        });
    }
}
