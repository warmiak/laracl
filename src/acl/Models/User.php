<?php

namespace App;

use App\Profile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profiles()
    {
        return $this->hasOne(Profile::class);
    }



    /**
     * @param $user
     * @return bool
     */
    public function isActive($user)
    {
        $active = $user->active;
        $confirmed = $user->confirmed;

        if ($active && $confirmed) {
            return true;
        }

        return false;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        foreach ($role as $value) {
            if ($this->hasRole($value->name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $role
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function giveRoleTo($role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     * @param $role
     * @return int
     */
    public function removeRoleFrom($role)
    {
        return $this->roles()->detach(
            Role::whereName($role)->firstOrFail()
        );
    }
}
