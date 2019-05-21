<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomersRequest;
use App\Http\Requests\Admin\UpdateCustomersRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

// get lat long 
use GoogleAPIHelper;
use DB;

use Validator;
use Illuminate\Support\Facades\Input;

class CustomersController extends Controller
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
     * Display a listing of Customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('customer_access')) {
            return abort(401);
        }
// echo "<pre>"; print_r (auth()->user()); echo "</pre>"; exit();

        // if (request('show_deleted') == 1) {
        //     if (! Gate::allows('customer_delete')) {
        //         return abort(401);
        //     }
        //     $customers = Customer::onlyTrashed()->get();
        // } else {

        //     if(auth()->user()->role_id ==  config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id ==  config('constants.COMPANY_USER_ROLE_ID'))
        //     {
        //         //get company admin's or user's own company customers if logged in user is company admin or user
        //         $customers = Customer::where('company_id',auth()->user()->company_id)->get();
        //     }
        //     else
        //     {
        //         $customers = Customer::all();
        //     }
            
        // }
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
        return view('admin.customers.index', compact('companies'));
    }
    /**
     * Display a listing of customer using ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableCustomerAjax(Request $request)
    {
        if (! Gate::allows('customer_access')) {
            return abort(401);
        }

        

        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  Customer::select('customers.*','companies.name as company_name')
         ->join('companies','customers.company_id','=','companies.id')
         ->whereNull('companies.deleted_at')
         ->where('companies.status','Active');

        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            $columnArray = array(
                    1 =>'customers.id',
                    2 =>'customers.firstname' ,
                    3 =>'customers.phone' ,
                    4 =>'companies.name' ,
                    5 =>'customers.status'
                );

            if(!empty($request->input('company')))
            {   
                $requestFilterCountQuery->Where('customers.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere(DB::raw("CONCAT(`customers`.`firstname`,' ', `customers`.`lastname`)"), 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.status', 'like', '%' . $searchVal . '%');

                });
            }
        }
        else{
           $columnArray = array(
                    1 =>'customers.id',
                    2 =>'customers.firstname' ,
                    3 =>'customers.phone' ,
                    4 =>'customers.status'
                );

            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $requestFilterCountQuery->where('company_id',auth()->user()->company_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    
                    $query->orWhere(DB::raw("CONCAT(`customers`.`firstname`,' ', `customers`.`lastname`)"), 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.email', 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.status', 'like', '%' . $searchVal . '%');

                });
            } 
        }
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        
        $requestFilterCount = $requestFilterCountQuery->count('customers.id');
        

        $customerQuery = Customer::select('customers.*','companies.name as company_name')
         ->join('companies','customers.company_id','=','companies.id')
         ->whereNull('companies.deleted_at')
         ->where('companies.status','Active')
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            if(!empty($request->input('company')))
            {   
                $customerQuery->Where('customers.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $customerQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere(DB::raw("CONCAT(`customers`.`firstname`,' ', `customers`.`lastname`)"), 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = Customer::select('customers.*')
                            ->join('companies','customers.company_id','=','companies.id')
                            ->whereNull('companies.deleted_at')
                            ->where('companies.status','Active')
                            ->count('customers.id');
        } 
        else 
        {
            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $customerQuery->where('customers.company_id',auth()->user()->company_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $customerQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere(DB::raw("CONCAT(`customers`.`firstname`,' ', `customers`.`lastname`)"), 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.status', 'like', '%' . $searchVal . '%');

                });
            }
            // fetch total count without any filter
            $countRecord = Customer::select('customers.*')
                            ->join('companies','customers.company_id','=','companies.id')
                            ->whereNull('companies.deleted_at')
                            ->where('companies.status','Active')
                            ->where('company_id',auth()->user()->company_id)->count('customers.id');
        } 

        
        
        $customers = $customerQuery->get();
        

        if(!empty($customers)){

            foreach ($customers as $key => $customer) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] = $customer->id;
                $tableField['customer_name'] = ucfirst($customer->firstname)." ".ucfirst($customer->lastname);
                $tableField['company'] =ucfirst($customer->company_name);
                $tableField['phone'] =$customer->phone;
                $tableField['status'] =$customer->status;

                $EditButtons = '';
                if (Gate::allows('customer_edit')) {
                    $EditButtons = '<a href="'.route('admin.customers.edit',$customer->id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                $DeleteButtons = '';
                if (Gate::allows('customer_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.customers.destroy',$customer->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

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
     * Show the form for creating new Customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('customer_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_customer_status = Customer::$enum_status;
            
        return view('admin.customers.create', compact('enum_customer_status', 'companies'));
    }

    /**
     * Store a newly created Customer in storage.
     *
     * @param  \App\Http\Requests\StoreCustomersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('customer_create')) {
            return abort(401);
        }

        $validator = Validator::make($request->all(), [

            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required|min:11|max:11',
            // 'email' => 'required|email|unique:customers,email,"",id,deleted_at,NULL',
            'email' => 'email|nullable',
            'company_id' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            // 'status' => 'required',

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

        $resultLocation=GoogleAPIHelper::getLatLong($request['zipcode']);
        
        $request['location_latitude']=0; 
        $request['location_longitude']=0;   
        if($resultLocation){    
            $request['location_latitude']=$resultLocation['lat'];
            $request['location_longitude']=$resultLocation['lng'];
        }
        // else
        // {
        //     $request['location_latitude']=112;
        //     $request['location_longitude']=113;
        // }

        $customer = Customer::create($request->all());

        // foreach ($request->input('service_requests', []) as $data) {
        //     $customer->service_requests()->create($data);
        // }

        if($request->ajax())
        {
            // get company details and return in ajax response

            $custOptions="<option value=''>".trans('quickadmin.qa_please_select')."</option>";

            // $customers = \App\Customer::where('company_id',$details['companyId'])
            //                     ->where('status','Active')->get();
            // if(count($customers) > 0)
            // {
            //     foreach($customers as $key => $value)
            //     {
            //         $data['custOptions'].="<option value='".$value->id."'>".$value->firstname.' '.$value->lastname."</option>";   
            //     }   
            // }
            return response()->json(array(
                    'success' => true,
                    'message' => 'Customer created successfully!',
                    'custOptions' => $custOptions,
                    // 'selectedCustomer' => $customer->id,
                    'last_inserted_company_id' => $customer->company_id,
                    'last_inserted_customer_id' => $customer->id,
                    'last_inserted_customer_name' => ucfirst($customer->firstname).' '.ucfirst($customer->lastname)
                ));
        }
        else{
            return redirect()->route('admin.customers.index')->with('success','Customer created successfully!');
        }
    }


    /**
     * Show the form for editing Customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('customer_edit')) {
            return abort(401);
        }
        
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Customer::$enum_status;
            
        $customer = Customer::findOrFail($id);

        return view('admin.customers.edit', compact('customer', 'enum_status', 'companies'));
    }

    /**
     * Update Customer in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomersRequest $request, $id)
    {
        if (! Gate::allows('customer_edit')) {
            return abort(401);
        }
        $customer = Customer::findOrFail($id);
        // check customer is assigned in service request
        if($request['status'] == "Inactive"){
            $validator = Validator::make($request->all(), []);
            $checkExistData = ServiceRequest::where('customer_id', $id)->get();

            if(count($checkExistData) > 0)
            {   
                return redirect()->back()->withInput(Input::all())->with(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->add('Assigned', trans('This customer is already assigned in service request.'))

                    ));
                    exit;
            }
        }
        if(isset($customer->zipcode) && isset($request['zipcode']))
        {
            if($customer->zipcode !== $request['zipcode'])
            {
                $resultLocation=GoogleAPIHelper::getLatLong($request['zipcode']);
                
                $request['location_latitude']=0; 
                $request['location_longitude']=0;     
                if($resultLocation){    
                    $request['location_latitude']=$resultLocation['lat'];
                    $request['location_longitude']=$resultLocation['lng'];
                }
                // else
                // {
                //     $request['location_latitude']=112;
                //     $request['location_longitude']=113;
                // }
            }
        }
        // echo "<pre>"; print_r ($request->all()); echo "</pre>"; exit();
        $customer->update($request->all());

        // $serviceRequests           = $customer->service_requests;
        // $currentServiceRequestData = [];
        // foreach ($request->input('service_requests', []) as $index => $data) {
        //     if (is_integer($index)) {
        //         $customer->service_requests()->create($data);
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


        return redirect()->route('admin.customers.index')->with('success','Customer updated successfully!');
    }


    /**
     * Display Customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('customer_view')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');$service_requests = \App\ServiceRequest::where('customer_id', $id)->get();

        $customer = Customer::findOrFail($id);

        return view('admin.customers.show', compact('customer', 'service_requests'));
    }


    /**
     * Remove Customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('customer_delete')) {
            return abort(401);
        }
        $customer = Customer::findOrFail($id);

        // check customer is assigned in service request
        $checkExistData = ServiceRequest::where('customer_id', $id)->get();
        if(count($checkExistData) > 0)
        {   
            return  redirect()->route('admin.customers.index')->withErrors('This customer is already assigned in service request.');
            exit;
        }

        $customer->delete();

        return redirect()->route('admin.customers.index');
    }

    /**
     * Delete all selected Customer at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('customer_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Customer::whereIn('id', $request->input('ids'))->get();

            $not_deleted=0;
            foreach ($entries as $entry) {

                // check customer is assigned in service request
                $checkExistData = ServiceRequest::where('customer_id', $entry->id)->get();
                if(count($checkExistData) > 0)
                {   
                    $not_deleted++;
                }
                else
                {
                    $entry->delete();
                }
            }
            if($not_deleted > 0)
            {
                redirect()->route('admin.customers.index')->withErrors('Some customer is already assigned in service request, so it is not deleted.');
            }
        }
    }


    /**
     * Restore Customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('customer_delete')) {
            return abort(401);
        }
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()->route('admin.customers.index');
    }

    /**
     * Permanently delete Customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('customer_delete')) {
            return abort(401);
        }
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->forceDelete();

        return redirect()->route('admin.customers.index');
    }
    
}
