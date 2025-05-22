<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        if ($user->role == $role) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
    }
}