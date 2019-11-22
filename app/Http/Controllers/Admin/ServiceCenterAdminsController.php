<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCenterAdminsRequest;
use App\Http\Requests\Admin\UpdateServiceCenterAdminsRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class ServiceCenterAdminsController extends Controller
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

        // show all service center admin
        // $query = User::where('role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))
        //                 ->orderby('name')->whereHas('service_center', function ($query) {
        //                                 $query->where('status', 'Active');
        //                         });

    
        // $users = $query->get();

        $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        return view('admin.service_center_admins.index', compact('service_centers'));
    }
    /**
     * Display a listing of service center admin using ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableServiceCenterAdminAjax(Request $request)
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }
        $columnArray = array(
                1 => 'users.id',
                2 =>'service_centers.name' ,
                3 =>'users.name' ,
                4 =>'users.phone' ,
                5 =>'users.email' ,
                6 =>'users.status' 
            );
        $limit = $request->input('length');

        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');


        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  User::select('users.*','service_centers.name as service_center_name')
         ->join('service_centers','users.service_center_id','=','service_centers.id')
         ->join('roles','users.role_id','=','roles.id')
         ->whereNull('service_centers.deleted_at')
         ->where('service_centers.status','Active')
         ->where('users.role_id',config('constants.SERVICE_ADMIN_ROLE_ID'));

        if(!empty($request->input('service_center')))
        {   
            $requestFilterCountQuery->Where('users.service_center_id', $request['service_center']);
        }

        //Search from table
        if(!empty($request->input('search.value')))
        { 
            $searchVal = $request['search']['value'];
            $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                
                $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');

                $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

            });
        }
        $requestFilterCount = $requestFilterCountQuery->count('users.id');
        

        $service_center_adminsQuery = User::select('users.*','service_centers.name as service_center_name')
         ->join('service_centers','users.service_center_id','=','service_centers.id')
         ->join('roles','users.role_id','=','roles.id')
         ->whereNull('service_centers.deleted_at')
         ->where('service_centers.status','Active')
         ->where('users.role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        if(!empty($request->input('service_center')))
        {   
            $service_center_adminsQuery->Where('users.service_center_id', $request['service_center']);
        }

        //Search from table
        if(!empty($request->input('search.value')))
        { 
            $searchVal = $request['search']['value'];
            $service_center_adminsQuery->where(function ($query) use ($searchVal) {

                
                $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');

                $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

            });
        }
        
        $service_center_admins = $service_center_adminsQuery->get();

        // fetch total count without any filter
        $countRecord = User::select('users.*','service_centers.name as service_center_name')
         ->join('service_centers','users.service_center_id','=','service_centers.id')
         ->join('roles','users.role_id','=','roles.id')
         ->whereNull('service_centers.deleted_at')
         ->where('service_centers.status','Active')
         ->where('users.role_id',config('constants.SERVICE_ADMIN_ROLE_ID'))->count('users.id');
        if(!empty($service_center_admins)){
            
            foreach ($service_center_admins as $key => $service_center_admin) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] = $service_center_admin->id;
                $tableField['service_center'] = $service_center_admin->service_center_name;
                $tableField['admin_name'] =$service_center_admin->name;
                $tableField['phone'] =$service_center_admin->phone;
                $tableField['email'] =$service_center_admin->email;
                $tableField['status'] =$service_center_admin->status;

                if (Gate::allows('user_edit')) {
                    $EditButtons = '<a href="'.route('admin.service_center_admins.edit',$service_center_admin->id).'" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>';
                }
                if (Gate::allows('user_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.service_center_admins.destroy',$service_center_admin->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

                    <input name="_method" type="hidden" value="DELETE">
                    <input type="hidden"
                               name="_token"
                               value="'.csrf_token().'">
                    <button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                    </form>';
                }

                $tableField['action'] = $ViewButtons.' '.$EditButtons.' '.$DeleteButtons;
                $tableFieldData[] = $tableField;
            }
           
        }
               
        $json_data = array(
            "draw"            => intval($request['draw']),  
            "recordsTotal"    => intval($countRecord),  
            "recordsFiltered" => intval($requestFilterCount),
            "data"            => $tableFieldData   
            );
        // $json_data = array(
        //     "draw"            => intval($request['draw']),  
        //     "recordsTotal"    => 0,  
        //     "recordsFiltered" => 0,
        //     "data"            => $tableFieldData   
        //     );

        echo json_encode($json_data);

       
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
        $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
        $logged_userRole_id= auth()->user()->role_id;  
        return view('admin.service_center_admins.create', compact('enum_status', 'service_centers','logged_userRole_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreServiceCenterAdminsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceCenterAdminsRequest $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $data = $request->all();
        $data['role_id'] = config('constants.SERVICE_ADMIN_ROLE_ID');
        $user = User::create($data);

        // foreach ($request->input('service_requests', []) as $data) {
        //     $user->service_requests()->create($data);
        // }


        return redirect()->route('admin.service_center_admins.index')->with('success','Service Center Admin created successfully!');
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
        $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
            
        $user = User::findOrFail($id);
        $logged_userRole_id= auth()->user()->role_id;
        return view('admin.service_center_admins.edit', compact('user', 'enum_status', 'service_centers','logged_userRole_id'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceCenterAdminsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceCenterAdminsRequest $request, $id)
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

        return redirect()->route('admin.service_center_admins.index')->with('success','Service Center Admin updated successfully!');
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

        return view('admin.service_center_admins.show', compact('user', 'service_requests', 'service_requests'));
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

        return redirect()->route('admin.service_center_admins.index');
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
