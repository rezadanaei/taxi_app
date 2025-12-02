<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsPassenger
{
   
    public function handle(Request $request, Closure $next) 
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'برای ثبت درخواست سفر، ابتدا وارد شوید.');
        }

        if (Auth::user()->type !== 'passenger') {
            return redirect()->route('home')->with('error', 'شما به عنوان مسافر مجاز به ثبت سفر نیستید.');
        }

        return $next($request);
    }
}