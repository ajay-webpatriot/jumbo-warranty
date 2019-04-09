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
use Validator;
use Illuminate\Support\Facades\Input;
use DB;

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

        // $query = User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
        //                 ->orderby('name')->whereHas('service_center', function ($query) {
        //                                 $query->where('status', 'Active');
        //                         });

        // //get service center's own technician if logged in user is technician
        // if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')){
        //     $query->where('service_center_id',auth()->user()->service_center_id);
        // }
        // //if logged in user is super admin or admin then show all technicians
        // $users = $query->get();

        $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        return view('admin.technicians.index', compact('service_centers'));
    }

    /**
     * Display a listing of technicians using ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableTechnicianAjax(Request $request)
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        

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
         ->where('users.role_id',config('constants.TECHNICIAN_ROLE_ID'));

        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            $columnArray = array(
                    1 => 'users.id',
                    2 =>'users.name' ,
                    3 =>'service_centers.name' ,
                    4 =>'users.phone' ,
                    5 =>'users.email' ,
                    6 =>'users.status' 
                );

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
        }
        else{
            $columnArray = array(
                    1 => 'users.id',
                    2 =>'users.name' ,
                    3 =>'users.phone' ,
                    4 =>'users.email' ,
                    5 =>'users.status' 
                );

            if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                $requestFilterCountQuery->where('service_center_id',auth()->user()->service_center_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

                });
            } 
        }
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        
        $requestFilterCount = $requestFilterCountQuery->count('users.id');
        

        $techniciansQuery = User::select('users.*','service_centers.name as service_center_name')
         ->join('service_centers','users.service_center_id','=','service_centers.id')
         ->join('roles','users.role_id','=','roles.id')
         ->whereNull('service_centers.deleted_at')
         ->where('service_centers.status','Active')
         ->where('users.role_id',config('constants.TECHNICIAN_ROLE_ID'))
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            if(!empty($request->input('service_center')))
            {   
                $techniciansQuery->Where('users.service_center_id', $request['service_center']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $techniciansQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = User::select('users.*','service_centers.name as service_center_name')
                 ->join('service_centers','users.service_center_id','=','service_centers.id')
                 ->join('roles','users.role_id','=','roles.id')
                 ->whereNull('service_centers.deleted_at')
                 ->where('service_centers.status','Active')
                ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->count('users.id');
        } 
        else 
        {
            if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                $techniciansQuery->where('service_center_id',auth()->user()->service_center_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $techniciansQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = User::select('users.*','service_centers.name as service_center_name')
                 ->join('service_centers','users.service_center_id','=','service_centers.id')
                 ->join('roles','users.role_id','=','roles.id')
                 ->whereNull('service_centers.deleted_at')
                 ->where('service_centers.status','Active')
                 ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->where('service_center_id',auth()->user()->service_center_id)->count('users.id');
        } 

        
        
        $technicians = $techniciansQuery->get();
        

        if(!empty($technicians)){

            foreach ($technicians as $key => $technicians) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] = $technicians->id;
                $tableField['technician_name'] =$technicians->name;
                $tableField['service_center'] = $technicians->service_center_name;
                $tableField['phone'] =$technicians->phone;
                $tableField['email'] =$technicians->email;
                $tableField['status'] =$technicians->status;

                if (Gate::allows('user_edit')) {
                    $EditButtons = '<a href="'.route('admin.technicians.edit',$technicians->id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                if (Gate::allows('user_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.technicians.destroy',$technicians->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

                    <input name="_method" type="hidden" value="DELETE">
                    <input type="hidden"
                               name="_token"
                               value="'.csrf_token().'">
                    <input type="submit" class="btn btn-xs btn-danger" value="Delete" />
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
        //     "data"            => array()   
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
        $enum_technician_status = User::$enum_status;

        return view('admin.technicians.create', compact('enum_technician_status', 'service_centers','logged_userRole_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreTechniciansRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [

            'service_center_id' => 'required',
            'name' => 'required',
            'phone' => 'required|min:11|max:11',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'status' => 'required',

        ]);
        if ($validator->fails()) {

            if($request->ajax())
            {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
                ));
            }
            else
            {
                return redirect()->back()->withInput(Input::all())->with(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()

                ));
                exit;
            }
        
        }
        $data['role_id'] = config('constants.TECHNICIAN_ROLE_ID');
        $user = User::create($data);

        // foreach ($request->input('service_requests', []) as $data) {
        //     $user->service_requests()->create($data);
        // }

        if($request->ajax())
        {
            return response()->json(array(
                    'success' => true,
                    'message' => 'Technician created successfully!'
                ));
        }
        else{
            return redirect()->route('admin.technicians.index')->with('success','Technician created successfully!');
        }
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
