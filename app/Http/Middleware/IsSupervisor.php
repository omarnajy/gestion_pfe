<?php
// app/Http/Middleware/IsSupervisor.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSupervisor
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isSupervisor()) {
            return redirect('/')->with('error', 'Accès réservé aux encadreurs.');
        }

        return $next($request);
    }
}