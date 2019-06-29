<?php

namespace App\Http\Controllers;

use App\Mail\RegisterUser;
use App\Permission;
use App\Profile;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRolePermission(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required|alpha_dash',
            'role' => 'required|alpha'
        ]);

        $role = Role::whereName($request->role)->firstOrFail();

        if ($role->hasPermission($request->permission)) {
            $role->removePermissionFrom($request->permission);

            Log::notice('Permission: Role '.$role->label.' Permission removed: '.$request->permission);

            return response()->json([
                'status' => 'Permission '. $request->permission .' for '. $role->label .' removed'
            ]);
        }

        $permission = Permission::whereName($request->permission)->firstOrFail();

        $role->givePermissionTo($permission->name);

        Log::notice('Permission: Role '.$role->label.' Permission added: '.$permission->name);

        return response()->json([
            'status' => 'Permission '. $permission->name .' for '. $role->label .' added'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function changeUserRole(Request $request)
    {
        $this->validate($request, [
            'user' => 'required',
            'role' => 'required|alpha'
        ]);

        $defaultRole = config('acl.default_member_role');

        if ($request->role == $defaultRole) {
            return response(['message' => $defaultRole.' can\'t be changed !'], 400);
        }

        $user = User::whereName($request->user)->firstOrFail();

        if ($user->hasRole($request->role)) {
            $user->removeRoleFrom($request->role);

            Log::notice('Account: User '.$user->name.' '.$user->email.' Role removed: '.$request->role);

            return response()->json([
                'status' => 'Role '. $request->role .' for '. $user->name .' removed'
            ]);
        }

        $role = Role::whereName($request->role)->firstOrFail();

        $user->giveRoleTo($role->name);

        Log::notice('Account: User '.$user->name.' '.$user->email.' Role added: '.$role->name);

        return response()->json([
            'status' => 'Role '. $role->name .' for '. $user->name .' added'
        ]);
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function changeUserStatus(Request $request)
    {
        $user = User::whereName($request->username)->firstOrFail();

        if ($user->active) {
            $user->active = false;
            $user->save();

            Log::warning('AccountStatus: User '.$user->name.' '.$user->email.' deactivated');

            return response()->json([
                'status' => 'User Status changed !'
            ]);
        }

        if (!$user->active) {
            $user->active = true;
            $user->save();

            Log::warning('AccountStatus: User '.$user->name.' '.$user->email.' activated');

            return response()->json([
                'status' => 'User Status changed !'
            ]);
        }

        return false;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function storeAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image',
            'user' => 'required'
        ]);

        $file = $request->file('avatar');

        $ext = $file->guessClientExtension();

        $profile = Profile::where('user_id', $request->user)->firstOrFail();

        if (!empty($profile->avatar)) {
            Storage::delete('public/' . $profile->avatar);
        }

        $file->storeAs('public/profile/' . $profile->profilename, $profile->profilename . "." . "{$ext}");
        $avatar = 'profile/' . $profile->profilename . '/' . $profile->profilename . '.' . $ext;

        $profile->update(['avatar' => $avatar]);

        return response([], 204);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAvatar(Request $request)
    {
        $this->validate($request, [
            'user' => 'required'
        ]);

        $profile = Profile::where('user_id', $request->user)->firstOrFail();

        Storage::delete('public/' . $profile->avatar);

        $profile->avatar = null;
        $profile->save();

        return response([], 204);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeUserProfile(Request $request)
    {
        $this->validate($request, [
           'firstname' => '',
           'lastname' => '',
           'jobtitle' => '',
           'company' => ''
        ]);

        $profile = Profile::where('profilename', $request->username)->firstOrFail();
        (array_key_exists('firstname', $request->toArray())) ? $profile->firstname = $request->firstname : '';
        (array_key_exists('lastname', $request->toArray())) ? $profile->lastname = $request->lastname : '';
        (array_key_exists('jobtitle', $request->toArray())) ? $profile->jobtitle = $request->jobtitle : '';
        (array_key_exists('company', $request->toArray())) ? $profile->company = $request->company : '';
        $profile->save();

        Log::info('AccountProfile: Profile '.$profile->profilename.' changed');

        return response()->json([
            'status' => 'Profile '. $request->username .' changed.'
        ]);
    }
}
