<?php

namespace Orendo\LaravelPackage\Acl;

use App\Permission;
use App\Role;
use Illuminate\Support\Facades\DB;


class Database
{
    public static function installRolePermission()
    {
        static::createDefaultPermissions();
        static::createDefaultRolesWithPermissions();
    }

    public static function createDefaultPermissions()
    {
        foreach (config('acl.default_permissions') as $value) {
            $gate = new Permission();
            $gate->name = $value[0];
            $gate->label = $value[1];
            $gate->save();
        }
    }

    public static function createDefaultRolesWithPermissions()
    {
        $permissions = Permission::all();

        foreach (config('acl.default_roles') as $value) {
            $roles = new Role;
            $roles->name = $value[0];
            $roles->label = $value[1];
            $roles->save();
            foreach ($permissions as $permission) {
                $roles->givePermissionTo($permission->name);
            }
        }
    }
}