<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = $request->user();

        if ($user->hasRole($role)) {

            return $next($request);
        }

        return redirect('/')->with('danger', 'you don\'t have the permissions to access this resource.');
    }
}
