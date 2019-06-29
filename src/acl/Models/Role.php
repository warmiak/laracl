<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'label'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @param $permission
     * @return mixed
     */
    public function hasPermission($permission) {
        return $this->permissions->contains('name', $permission);
    }

    /**
     * @param Permission $permission
     * @return Model
     */
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save(
            Permission::whereName($permission)->firstOrFail()
        );
    }

    /**
     * @param Permission $permission
     * @return int
     */
    public function removePermissionFrom($permission)
    {
        return $this->permissions()->detach(
            Permission::whereName($permission)->firstOrFail()
        );
    }
}
