<?php

namespace App\Http\Middleware;

use Closure;

class UserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user->isActive($user)) {

            return $next($request);
        }

        return redirect('/')->with('danger', 'Your Account is inactive or not confirmed.');
    }
}
