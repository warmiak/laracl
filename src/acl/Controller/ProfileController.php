<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::where('confirmed', 1)->with('profiles')->paginate(15);

        return view('profile.index', compact('users'));
    }

    /**
     * @param $name
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($name)
    {
        $user = User::whereName($name)->with('profiles')->firstOrFail();


        return view('profile.detail', compact('user'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        $user = User::where('email', auth()->user()->email)->with('profiles')->firstOrFail();

        return view('profile.edit', compact('user'));
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => '',
            'email' => '',
            'firstname' => '',
            'lastname' => '',
            'birthday' => 'date',
        ]);

        dd($request->all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     *
     * @todo Resize Avatar Images
     */
    public function storeAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image'
        ]);

        $file = $request->file('avatar');

        $ext = $file->guessClientExtension();

        $profile = Profile::where('user_id', auth()->user()->id)->firstOrFail();

        if (!empty($profile->avatar)) {
            Storage::delete('public/' . $profile->avatar);
        }

        $file->storeAs('public/profile/' . $profile->profilename, $profile->profilename . "." . "{$ext}");
        $avatar = '/storage/profile/' . $profile->profilename . '/' . $profile->profilename . '.' . $ext;

        $profile->update(['avatar' => $avatar]);

        return response([], 204);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAvatar()
    {
        $profile = Profile::where('user_id', auth()->user()->id)->firstOrFail();

        Storage::delete('public/' . $profile->avatar);

        $profile->avatar = null;
        $profile->save();

        return response([], 204);
    }
}
