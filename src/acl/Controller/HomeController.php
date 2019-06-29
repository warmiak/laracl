<?php

namespace App\Http\Controllers;

use App\Mail\RegisterUser;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    /**
     * Create a new Controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function validateEmail($token)
    {
        $user = User::where('email_token', $token)->first();
        $username = Auth::user()->email;

        if (!$user)
        {
            return redirect('/')->with('danger', "Your E-Mail could't be verified.");
        }

        if ($user->email != $username)
        {
            return redirect('/')->with('danger', "Something happend wrong.");
        }


        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->profilename = $user->name;
        $profile->save();

        $user->confirmed = true;
        $user->active = true;
        $user->email_token = null;
        $user->save();

        $user->giveRoleTo(config('acl.default_member_role'));

        return redirect()->route('profile_show', ['name' => $user->name])->with('success', "E-Mail successfuly confirmed.");
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function resendEmailVerification(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        if (!$user || $user->isActive($user))
        {
            return redirect('/')->with('danger', "Your E-Mail could't be resented. Please contact the Administrator of the Page.");
        }

        if ($user && !$user->isActive($user))
        {
            $hash = hash_hmac('sha512', str_random(128), config('app.key'));
            $email = $user->email;

            $user->email_token = $hash;
            $user->save();

            Mail::to($email)->send(new RegisterUser($hash));

            return redirect('/')->with('success', 'We resend an E-Mail to you with an Activation Link');
        }

        return false;
    }
}
