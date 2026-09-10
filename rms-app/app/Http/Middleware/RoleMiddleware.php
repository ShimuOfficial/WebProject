<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        $roleTable = config('permission.table_names.roles', 'roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
        $hasPermissionTables = Schema::hasTable($roleTable) && Schema::hasTable($modelHasRolesTable);

        if (!$user instanceof User) {
            abort(403, 'Unauthorized Access. You do not have the required permissions.');
        }

        if (in_array($user->role, $roles, true) || $user->role === 'admin') {
            return $next($request);
        }

        if ($hasPermissionTables && ($user->hasRole('admin') || $user->hasAnyRole($roles))) {
            return $next($request);
        }

        abort(403, 'Unauthorized Access. You do not have the required permissions.');
    }
}
