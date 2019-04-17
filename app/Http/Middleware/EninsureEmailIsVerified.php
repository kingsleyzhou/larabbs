<?php

namespace App\Http\Middleware;

use Closure;

class EninsureEmailIsVerified
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
        //判断用户是否经过邮箱验证中间件，满足3个条件：1、如果用户已经登录；2、并且还未验证Email；3、并且访问的不是email验证相关url 和退出的url
        if ($request->user() && !$request->user()->hasVerifiedEmail() && !$request->is('email/*','logout')){
            return $request->expectsJson() ? abort(403,'Your email address is not verified') : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
