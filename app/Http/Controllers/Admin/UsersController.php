<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class UsersController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageUser')) {
                return abort(404);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (! Gate::allows('user_access')) {
            return abort(401);
        }
        // if(auth()->user()->role_id ==  config('constants.COMPANY_ADMIN_ROLE_ID'))
        // {
        //     $users = User::where('role_id',config('constants.COMPANY_USER_ROLE_ID'))->where('company_id',auth()->user()->company_id)->get();
        // }
        // else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
        // {
        //     $users = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->where('service_center_id',auth()->user()->service_center_id)->get();
        // }
        // else
        // {
            $users = User::where('role_id',config('constants.ADMIN_ROLE_ID'))->orderby('id','DESC')->get();
        // }
        
        
        

        return view('admin.users.index', compact('users'));
    }
    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        
        $enum_status = User::$enum_status;
        $logged_userRole_id= auth()->user()->role_id;  
        return view('admin.users.create', compact('enum_status','logged_userRole_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }

        $data = $request->all();
        $data['role_id'] = config('constants.ADMIN_ROLE_ID');
        $user = User::create($data);


        return redirect()->route('admin.users.index')->with('success','Admin User added successfully!');
    }


    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }

        $enum_status = User::$enum_status;
            
        $user = User::findOrFail($id);
        $logged_userRole_id= auth()->user()->role_id;
        return view('admin.users.edit', compact('user', 'enum_status','logged_userRole_id'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('success','Admin User updated successfully!');
    }


    /**
     * Display User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('user_view')) {
            return abort(401);
        }
        
        $roles = \App\Role::get()->pluck('title', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');$service_requests = \App\ServiceRequest::where('customer_id', $id)->get();$service_requests = \App\ServiceRequest::where('technician_id', $id)->get();

        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user', 'service_requests', 'service_requests'));
    }


    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
