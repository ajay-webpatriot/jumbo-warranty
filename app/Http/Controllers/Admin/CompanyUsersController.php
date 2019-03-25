<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyUsersRequest;
use App\Http\Requests\Admin\UpdateCompanyUsersRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class CompanyUsersController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageCompany')) {
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

        // show all company users
        // $query = User::where('role_id',config('constants.COMPANY_USER_ROLE_ID'))
        //                 ->orderby('name');

        // //get company admin's own company users if logged in user is company admin
        // if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID')){
        //     $query->where('company_id',auth()->user()->company_id);
        // }                
        // $users = $query->get();

        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
        return view('admin.company_users.index', compact('companies'));
    }

    /**
     * Display a listing of company admin using ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableCompanyUserAjax(Request $request)
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        

        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  User::select('users.*','companies.name as company_name')
         ->join('companies','users.company_id','=','companies.id')
         ->join('roles','users.role_id','=','roles.id')
         ->where('companies.status','Active')
         ->where('users.role_id',config('constants.COMPANY_USER_ROLE_ID'));

        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            $columnArray = array(
                    1 => 'users.id',
                    2 =>'companies.name' ,
                    3 =>'users.name' ,
                    4 =>'users.phone' ,
                    5 =>'users.email' ,
                    6 =>'users.status' 
                );

            if(!empty($request->input('company')))
            {   
                $requestFilterCountQuery->Where('users.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');

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

            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID'))
            {
                $requestFilterCountQuery->where('company_id',auth()->user()->company_id);
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
        

        $company_usersQuery = User::select('users.*','companies.name as company_name')
         ->join('companies','users.company_id','=','companies.id')
         ->join('roles','users.role_id','=','roles.id')
         ->where('companies.status','Active')
         ->where('users.role_id',config('constants.COMPANY_USER_ROLE_ID'))
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            if(!empty($request->input('company')))
            {   
                $company_usersQuery->Where('users.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $company_usersQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = User::select('users.*')
                ->join('companies','users.company_id','=','companies.id')
                ->join('roles','users.role_id','=','roles.id')
                ->where('companies.status','Active')
                ->where('role_id',config('constants.COMPANY_USER_ROLE_ID'))->count('users.id');
        } 
        else 
        {
            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID'))
            {
                $company_usersQuery->where('company_id',auth()->user()->company_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $company_usersQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('users.name', 'like', '%' . $searchVal . '%');

                    $query->orWhere('users.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('users.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = User::select('users.*')
                ->join('companies','users.company_id','=','companies.id')
                ->join('roles','users.role_id','=','roles.id')
                ->where('companies.status','Active')
                ->where('role_id',config('constants.COMPANY_USER_ROLE_ID'))->where('company_id',auth()->user()->company_id)->count('users.id');
        } 

        
        
        $company_users = $company_usersQuery->get();
        

        if(!empty($company_users)){

            foreach ($company_users as $key => $company_user) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] = $company_user->id;
                $tableField['company_name'] = $company_user->company_name;
                $tableField['company_user_name'] =$company_user->name;
                $tableField['phone'] =$company_user->phone;
                $tableField['email'] =$company_user->email;
                $tableField['status'] =$company_user->status;

                if (Gate::allows('user_edit')) {
                    $EditButtons = '<a href="'.route('admin.company_users.edit',$company_user->id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                if (Gate::allows('user_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.company_users.destroy',$company_user->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

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
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
        $logged_userRole_id= auth()->user()->role_id;  
        return view('admin.company_users.create', compact('enum_status', 'companies','logged_userRole_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreCompanyUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompanyUsersRequest $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $data = $request->all();
        $data['role_id'] = config('constants.COMPANY_USER_ROLE_ID');
        $user = User::create($data);

        return redirect()->route('admin.company_users.index')->with('success','Company Users created successfully!');
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
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = User::$enum_status;
            
        $user = User::findOrFail($id);
        $logged_userRole_id= auth()->user()->role_id;
        return view('admin.company_users.edit', compact('user', 'enum_status', 'companies','logged_userRole_id'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateCompanyUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompanyUsersRequest $request, $id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->update($request->all());
        return redirect()->route('admin.company_users.index')->with('success','Company Users updated successfully!');
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

        return view('admin.company_users.show', compact('user', 'service_requests', 'service_requests'));
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

        return redirect()->route('admin.company_users.index');
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
