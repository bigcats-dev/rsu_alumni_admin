<?php

namespace App\Permissions;

use App\Models\Permission;
use App\Models\Role;

trait HasPermissionsTrait
{

    public function isSuperAdmin()
    {
        return $this->hasRole("super-administrator");
    }

    public function hasPermissionTo($permission)
    {

        return $this->hasPermissionThroughRole($permission);
    }

    /**
     * check has permission
     *
     * @param \App\Models\MsPermission $permission
     * @return bool
     *
     */
    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->role->role_id == $role->role_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * check has permission
     *
     * @param \App\Models\MsPermission $permission
     * @return bool
     *
     */
    public function hasPermissionThroughRoles($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->user_roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * check has roles
     *
     * @param string $role
     * @return bool
     *
     */
    public function hasRole($role)
    {
        return $this->role->role_slug == $role;
    }

    /**
     * check has roles
     *
     * @param string|array $roles
     * @return bool
     *
     */
    public function hasRoles($roles)
    {
        foreach ((array) $roles as $role) {
            if ($this->user_roles->contains('role_slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * check has permission
     * 
     * @param string|array $permissions
     * @return bool
     * 
     */
    public function hasPermission($permissions)
    {
        $ms_permissions = Permission::whereIn("slug", (array) $permissions)->get();
        if (count($ms_permissions) > 0)
            foreach ($ms_permissions as $permission)
                return $this->hasPermissionThroughRole($permission);

        return false;
    }

    public function user_roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'id', 'role_id', 'user_id');
    }
}
