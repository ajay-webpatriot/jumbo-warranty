<?php

namespace App\Http\Controllers\Admin;

use App\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ModelHasPermission;// model
use App\ModelHasRole;//model
use App\RoleHasPermission;//model

// permission plugin
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission as perm;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = \App\Role::where('id','!=','1')->where('id','!=','3')->orderBy('title','asc')->get();
        $modules = \App\Module::all();
        $permissions = Permission::all();
        // echo "<pre>"; print_r ($permissions); echo "</pre>"; exit();
        return view('admin.permissions.index', compact('roles', 'permissions','modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // echo "<pre>";
        // print_r($request['permissions_role']);
        // exit;
        // $roles = \App\Role::all();
        // $modules = \App\Module::all();
        // $permissions = Permission::all();

        // $user=auth()->user();
        // $user->givePermissionTo('Access');
        // $user->assignRole('Company Admin');
        // $role = Role::findByName('Super Admin (can create other users)');
        // $role->hasPermissionTo('User Management');

        $data = $this->validate($request, [
            'permissions_role'=>'bail|required'
        ]);

        if(isset($request['permissions_role']))
        {
            foreach ($request['permissions_role'] as $roleKey => $roleValue) {

                $role = Role::findByName($roleKey);

                $role->permissions()->sync([]);
                foreach ($roleValue as $key => $value) {
                   $role->givePermissionTo($value);
                    // $role->syncPermissions($value);
                    
                    // $role->permission()->detach($permissions);
                }
            }
        }
        return redirect()->route('admin.permissions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
    }
}
