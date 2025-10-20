<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class CheckEmployeePermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Get employee record for this user (assuming user email matches employee email)
        $employee = Employee::where('email', $user->email)->first();
        
        if (!$employee) {
            // If no employee record found, allow access (for system users)
            return $next($request);
        }

        // Check if employee has the required permission
        if ($employee->hasPermission($permission)) {
            return $next($request);
        }

        // If permission denied, redirect with error
        return redirect()->back()->with('error', 'You do not have permission to access this feature.');
    }
}