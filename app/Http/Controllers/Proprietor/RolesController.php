<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use App\Seeders\DefaultPermissionsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index()
    {
        $school = Auth::user()->school;
        setPermissionsTeamId($school->id);

        $roles       = Role::where('team_id', $school->id)->with('permissions')->get();
        $permissions = Permission::all()->groupBy(fn($p) => explode('-', $p->name)[0]);

        return view('dashboards.proprietor.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'permissions' => 'array',
        ]);

        $school = Auth::user()->school;
        setPermissionsTeamId($school->id);

        // Prevent duplicate role names within same school
        $exists = Role::where('team_id', $school->id)
                      ->where('name', $request->name)
                      ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A role with this name already exists.']);
        }

        $role = Role::create([
            'name'       => $request->name,
            'guard_name' => 'web',
            'team_id'    => $school->id,
        ]);

        if ($request->permissions) {
            $perms = Permission::whereIn('name', $request->permissions)->get();
            $role->syncPermissions($perms);
        }

        return back()->with('success', "Role '{$role->name}' created successfully.");
    }

    public function update(Request $request, Role $role)
    {
        $school = Auth::user()->school;

        // Ensure role belongs to this school
        abort_if($role->team_id !== $school->id, 403);

        $request->validate(['permissions' => 'array']);

        setPermissionsTeamId($school->id);

        $perms = Permission::whereIn('name', $request->permissions ?? [])->get();
        $role->syncPermissions($perms);

        return back()->with('success', "Role '{$role->name}' permissions updated.");
    }

    public function destroy(Role $role)
    {
        $school = Auth::user()->school;
        abort_if($role->team_id !== $school->id, 403);

        // Prevent deleting core roles
        if (in_array($role->name, ['admin', 'staff'])) {
            return back()->withErrors(['role' => 'Cannot delete core system roles.']);
        }

        $role->delete();

        return back()->with('success', "Role deleted.");
    }

    /**
     * Assign a Spatie role to a user (called from user management).
     */
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string',
        ]);

        $school = Auth::user()->school;
        setPermissionsTeamId($school->id);

        $user = \App\Models\User::where('school_id', $school->id)
                                 ->findOrFail($request->user_id);

        $role = Role::where('team_id', $school->id)
                    ->where('name', $request->role)
                    ->firstOrFail();

        // Sync so user only ever has ONE role at a time
        $user->syncRoles([$role]);

        return back()->with('success', "Role '{$role->name}' assigned to {$user->name}.");
    }
}