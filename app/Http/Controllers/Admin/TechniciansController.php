<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTechniciansRequest;
use App\Http\Requests\Admin\UpdateTechniciansRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class TechniciansController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageServiceCenter')) {
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

        $query = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                        ->orderby('name')->whereHas('service_center', function ($query) {
                                        $query->where('status', 'Active');
                                });

        //get service center's own technician if logged in user is technician
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')){
            $query->where('service_center_id',auth()->user()->service_center_id);
        }
        //if logged in user is super admin or admin then show all technicians
        $users = $query->get();
        return view('admin.technicians.index', compact('users'));
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
        $service_centers = \App\ServiceCenter::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
        $logged_userRole_id= auth()->user()->role_id;  
        return view('admin.technicians.create', compact('enum_status', 'service_centers','logged_userRole_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreTechniciansRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTechniciansRequest $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $data = $request->all();
        $data['role_id'] = config('constants.TECHNICIAN_ROLE_ID');
        $user = User::create($data);

        // foreach ($request->input('service_requests', []) as $data) {
        //     $user->service_requests()->create($data);
        // }


        return redirect()->route('admin.technicians.index')->with('success','Technician created successfully!');
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
        
        // $roles = \App\Role::get()->pluck('title', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $service_centers = \App\ServiceCenter::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
            
        $user = User::findOrFail($id);
        $logged_userRole_id= auth()->user()->role_id;
        return view('admin.technicians.edit', compact('user', 'enum_status', 'service_centers','logged_userRole_id'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTechniciansRequest $request, $id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->update($request->all());

        // $serviceRequests           = $user->service_requests;
        // $currentServiceRequestData = [];
        // foreach ($request->input('service_requests', []) as $index => $data) {
        //     if (is_integer($index)) {
        //         $user->service_requests()->create($data);
        //     } else {
        //         $id                          = explode('-', $index)[1];
        //         $currentServiceRequestData[$id] = $data;
        //     }
        // }
        // foreach ($serviceRequests as $item) {
        //     if (isset($currentServiceRequestData[$item->id])) {
        //         $item->update($currentServiceRequestData[$item->id]);
        //     } else {
        //         $item->delete();
        //     }
        // }

        return redirect()->route('admin.technicians.index')->with('success','Technician updated successfully!');
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

        return view('admin.technicians.show', compact('user', 'service_requests', 'service_requests'));
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

        return redirect()->route('admin.technicians.index');
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
