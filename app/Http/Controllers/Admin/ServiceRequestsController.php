<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequestsRequest;
use App\Http\Requests\Admin\UpdateServiceRequestsRequest;

// models
use App\ServiceRequest;
use App\ServiceRequestLog;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

// get lat long & distance
use GoogleAPIHelper;
use Dompdf\Dompdf;
use SendMailHelper;

class ServiceRequestsController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageServiceRequest')) {
                return abort(404);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of ServiceRequest.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('service_request_access')) {
            return abort(401);
        }
        

        // if (request('show_deleted') == 1) {
        //     if (! Gate::allows('service_request_delete')) {
        //         return abort(401);
        //     }
        //     $service_requests = ServiceRequest::onlyTrashed()->get();
        // } else {
        //     if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->get();
        //     }
        //     else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('technician_id',auth()->user()->id)->get();
        //     }
        //     else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('company_id',auth()->user()->company_id)->get();
        //     }
        //     else
        //     {
        //         $service_requests = ServiceRequest::all();
        //     }
            
        // }

        return view('admin.service_requests.index', compact('service_requests'));

        // $data=array('subject' => 'Request Creation Receive',
        //             'user_name' => 'Hinal patel'

        //             );
        // $subject="123";
        // $user_name="1my name 23";
        // return view('admin.emails.service_request', compact('subject','user_name'));
    }

    public function DataTableServiceRequestAjax(Request $request)
    { 
          
        $columnArray = array();
        // echo "<pre>";
        // print_r(auth()->user()->role_id);
        // echo "</pre>";
        // exit();
        
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){
            $columnArray = array(
                0 => 'service_requests.id',
                1 =>'customers.firstname' ,
                2 =>'service_requests.service_type' ,
                3 =>'service_centers.name' ,
                4 =>'products.name' ,
                5 =>'service_requests.amount' ,
                6 =>'service_requests.status'
            );
        }else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID')){
            
            // 0 offset is skipped for checkbox
            $columnArray = array(
                1 => 'service_requests.id',
                2 =>'companies.name' ,
                3 =>'customers.firstname' ,
                4 =>'service_requests.service_type' ,
                5 =>'products.name' ,
                6 =>'service_requests.amount' ,
                7 =>'service_requests.status'
            );
        }else{
            // admin and super admin

            // 0 offset is skipped for checkbox
            $columnArray = array(
                1 => 'service_requests.id',
                2 =>'companies.name' ,
                3 =>'customers.firstname' ,
                4 =>'service_requests.service_type' ,
                5 =>'service_centers.name' ,
                6 =>'products.name' ,
                7 =>'service_requests.amount' ,
                8 =>'service_requests.status'
            );
        }
        
        
        $limit = $request->input('length');

        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
// echo $order;exit;
        // $order = $request->input('order');
        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";
        // exit();
        if (! Gate::allows('service_request_access')) {
            return abort(401);
        }

        if (request('show_deleted') == 1) {
            if (! Gate::allows('service_request_delete')) {
                return abort(401);
            }
            $service_requests = ServiceRequest::onlyTrashed()->get();
        } else{
        // else {
        //     if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->get();
        //     }
        //     else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('technician_id',auth()->user()->id)->get();
        //     }
        //     else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        //     {
        //         $service_requests = ServiceRequest::where('company_id',auth()->user()->company_id)->get();
        //     }
        //     else
        //     {
        //         $service_requests = ServiceRequest::all();
        //     }
            
        // }

            $tableFieldData = [];
            $ViewButtons = '';
            $EditButtons = '';
            $DeleteButtons = '';

            $service_requestsQuery = ServiceRequest::select('customers.firstname','service_centers.name as sname','products.name as pname','service_requests.amount','service_requests.service_type','service_requests.status','companies.name as cname','service_requests.id')
            ->leftjoin('companies','service_requests.company_id','=','companies.id')
            ->leftjoin('roles','service_requests.technician_id','=','roles.id')
            ->leftjoin('customers','service_requests.customer_id','=','customers.id')
            ->leftjoin('products','service_requests.product_id','=','products.id')
            ->leftjoin('service_centers','service_requests.service_center_id','=','service_centers.id')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir);

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                {
                    $service_requestsQuery->orWhere('companies.name', 'like', '%' . $request['search']['value'] . '%');

                }else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){

                    $service_requestsQuery->orWhere('service_centers.name', 'like', '%' . $request['search']['value'] . '%');

                }else{

                    $service_requestsQuery->orWhere('companies.name', 'like', '%' . $request['search']['value'] . '%');
                    $service_requestsQuery->orWhere('service_centers.name', 'like', '%' . $request['search']['value'] . '%');
                }
                $service_requestsQuery->orWhere('customers.firstname', 'like', '%' . $request['search']['value'] . '%');
                $service_requestsQuery->orWhere('products.name', 'like', '%' . $request['search']['value'] . '%');
                $service_requestsQuery->orWhere('service_requests.amount', 'like', '%' . $request['search']['value'] . '%');
                $service_requestsQuery->orWhere('service_requests.service_type', 'like', '%' . $request['search']['value'] . '%');
            }
            
            $service_requests = $service_requestsQuery->get();
            
        }
        if(!empty($service_requests)){

            $countRecoard = ServiceRequest::count();

            foreach ($service_requests as $key => $SingleServiceRequest) {

                if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID')){

                    $tableField['company_name'] =$SingleServiceRequest->cname;
                    if (Gate::allows('service_request_delete')) {
                        // $tableField['checkbox'] = '<input type="checkbox" class="dt-body-center" style="text-align: center;" name="checkbox_'.$key.'">';
                        $tableField['checkbox'] = '';
                    }

                }else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){

                    $tableField['service_center'] =$SingleServiceRequest->sname;

                }else{
                    $tableField['service_center'] =$SingleServiceRequest->sname;
                    $tableField['company_name'] =$SingleServiceRequest->cname;

                    if (Gate::allows('service_request_delete')) {
                        // $tableField['checkbox'] = '<input type="checkbox" class="dt-body-center" style="text-align: center;" name="checkbox_'.$key.'">';
                        $tableField['checkbox'] = '';
                    }
                }
                $tableField['sr_no'] = $SingleServiceRequest->id;
                $tableField['customer'] = $SingleServiceRequest->firstname;
                $tableField['service_type'] =$SingleServiceRequest->service_type;
                $tableField['product'] =$SingleServiceRequest->pname;
                $tableField['amount'] =$SingleServiceRequest->amount;
                $tableField['request_status'] =$SingleServiceRequest->status;

                if (Gate::allows('service_request_view')) {
                    $ViewButtons = '<a href="'.route('admin.service_requests.show',$SingleServiceRequest->id).'" class="btn btn-xs btn-primary">View</a>';
                }
                if (Gate::allows('service_request_edit')) {
                    $EditButtons = '<a href="'.route('admin.service_requests.edit',$SingleServiceRequest->id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                if (Gate::allows('service_request_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.service_requests.destroy',$SingleServiceRequest->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

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
            "recordsTotal"    => intval($countRecoard),  
            "recordsFiltered" => intval($countRecoard),
            "data"            => $tableFieldData   
            );

        echo json_encode($json_data);
    
    }

    /**
     * Show the form for creating new ServiceRequest.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('service_request_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $customers = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $customers = \App\Customer::get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $parts=array();
            $products=array(''=>trans('quickadmin.qa_please_select')); 

            $customers = \App\Customer::where('company_id',auth()->user()->company_id)
                                        ->where('status','Active')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
                                        
            $product_parts = \App\AssignPart::where('company_id',auth()->user()->company_id)
                                ->with('product_parts')->get();

            $company_products = \App\AssignProduct::where('company_id',auth()->user()->company_id)
                                ->with('product_id')->get();
            if(count($product_parts) > 0)
            {
                foreach($product_parts as $key => $value)
                {
                    $parts[$value->product_parts->id]=$value->product_parts->name;
                }   
            }
            if(count($company_products) > 0)
            {
                foreach($company_products as $key => $value)
                {
                    foreach($value->product_id as $details)
                    {
                        $products[$details->id]=$details->name;
                    }
                }
            }                            


        }
        else
        {
            $customers=array(''=>trans('quickadmin.qa_please_select'));
            $products = \App\Product::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            $parts = \App\ProductPart::get()->pluck('name', 'id');
        }

        $distance_charge=\App\ManageCharge::get()->first();
        $km_charge =$distance_charge->km_charge;

        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $technicians = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        

        $enum_service_type = ServiceRequest::$enum_service_type;
                    $enum_call_type = ServiceRequest::$enum_call_type;
                    $enum_call_location = ServiceRequest::$enum_call_location;
                    $enum_priority = ServiceRequest::$enum_priority;
                    $enum_is_item_in_warrenty = ServiceRequest::$enum_is_item_in_warrenty;
                    $enum_mop = ServiceRequest::$enum_mop;
                    $enum_status = ServiceRequest::$enum_status;
        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');
        
        return view('admin.service_requests.create', compact('enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName','km_charge'));
    }

    /**
     * Store a newly created ServiceRequest in storage.
     *
     * @param  \App\Http\Requests\StoreServiceRequestsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceRequestsRequest $request)
    {
        if (! Gate::allows('service_request_create')) {
            return abort(401);
        }
        if($request['service_center_id'] == "" && isset($request['suggested_service_center']))
        {
            if($request['suggested_service_center'] != "")
            {
                $request['service_center_id'] = $request['suggested_service_center'];
            }
        }

        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+(($request['additional_charges'] == "")?0:number_format((float)$request['additional_charges'], 2, '.', ''));

        // convert to json
        $request['additional_charges']= json_encode(array($request['additional_charges_title'] => number_format((float)$request['additional_charges'], 2, '.', '')));

        $distance_charge=\App\ManageCharge::get()->first();

        $request['km_distance'] = ($request['km_distance'] == "") ? 0 : number_format((float)$request['km_distance'], 2, '.', '');
        $request['km_charge'] = ($request['km_charge'] == "") ? number_format((float)$distance_charge->km_charge, 2, '.', '') : number_format((float)$request['km_charge'], 2, '.', '');

        $request['transportation_charge'] = ($request['transportation_charge'] == "") ? 0 : number_format((float)$request['transportation_charge'], 2, '.', '');
        // echo "<pre>"; print_r ($request->all()); echo "</pre>"; exit();
        
        // if($request['service_center_id'] != "" && $request['customer_id'] != "")
        // {
        //     // calculate transportation charges

        //     $centerDetail=\App\ServiceCenter::findOrFail($request['service_center_id']);
        //     $customerDetail=\App\Customer::findOrFail($request['customer_id']);

        //     $supportedCenterDetail=\App\ServiceCenter::Where('supported_zipcode', 'like', '%' . $customerDetail->zipcode . '%')->where('id',$request['service_center_id'])->get();
        //     if(count($supportedCenterDetail) <= 0)// && $customerDetail->zipcode != $centerDetail->zipcode)
        //     {

        //         // calculate transportation charges for unsupported zipcode
        //         $customer_latitude=$customerDetail->location_latitude;
        //         $customer_longitude=$customerDetail->location_longitude;

        //         $center_latitude=$centerDetail->location_latitude;
        //         $center_longitude=$centerDetail->location_longitude;
                
        //         $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

        //         $request['km_distance']=$distance;

        //         $distance_charge=\App\ManageCharge::get()->first();
        //         $request['km_charge']=$distance_charge->km_charge;
        //         $total_amount+=($distance*$distance_charge->km_charge);
        //     }
        // }

        $total_amount+=$request['transportation_charge'];
        if($request['service_center_id'] != "")
        {
            $request['status'] ="Assigned";
        }
        
        $request['amount']=$total_amount;  
        // calculate total amount work end


        $service_request = ServiceRequest::create($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));
        SendMailHelper::sendRequestCreationMail($service_request->id);

        // service request log for new request
        $insertServiceRequestLogArr = array(
                                        'action_made'     =>   "Service request is created.",
                                        'action_made_company'     =>   "Service request is created.",
                                        'action_made_service_center'     =>   "Service request is created.", 
                                        'service_request_id'   =>   $service_request->id,
                                        'user_id'   =>   auth()->user()->id
                                    );
        ServiceRequestLog::create($insertServiceRequestLogArr);

        if($request['status'] == "Assigned")
        {
            // service request log for assigned status
            $insertServiceRequestLogArr = array(
                                        'action_made'     =>   "Status is changed from New to Assigned.",
                                        'action_made_company'     =>   "Status is changed from New to Assigned.",
                                        'action_made_service_center'     =>   "Status is changed from New to Assigned.", 
                                        'service_request_id'   =>   $service_request->id,
                                        'user_id'   =>   auth()->user()->id
                                    );
            ServiceRequestLog::create($insertServiceRequestLogArr);
        }
        
            

        return redirect()->route('admin.service_requests.index');
    }


    /**
     * Show the form for editing ServiceRequest.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('service_request_edit')) {
            return abort(401);
        }
        // SendMailHelper::sendRequestCreationMail($id);
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
        // $products = \App\Product::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $parts = \App\ProductPart::get()->pluck('name', 'id');
// echo "<pre>"; print_r ($products); echo "</pre>"; exit();
        $enum_service_type = ServiceRequest::$enum_service_type;
                    $enum_call_type = ServiceRequest::$enum_call_type;
                    $enum_call_location = ServiceRequest::$enum_call_location;
                    $enum_priority = ServiceRequest::$enum_priority;
                    $enum_is_item_in_warrenty = ServiceRequest::$enum_is_item_in_warrenty;
                    $enum_mop = ServiceRequest::$enum_mop;
                    // $enum_status = ServiceRequest::$enum_status;
            
        $service_request = ServiceRequest::findOrFail($id);
        if($service_request['service_type'] == "repair")
        {
            $enum_status = ServiceRequest::$enum_repair_status;
        }
        else
        {
            $enum_status = ServiceRequest::$enum_installation_status;
        }
        $additional_charge_array=json_decode($service_request['additional_charges']);
        $additional_charge_title="";
        $additional_charges="";
        if(!empty($additional_charge_array))
        {
            // Worked to display json value in edit page
            foreach ($additional_charge_array as $key => $value) {
                $additional_charge_title=str_replace('_empty_', '', $key);
                $additional_charges=$value;
            }
        }
        
        $service_request['additional_charges']=$additional_charges;

        if($service_request['service_center_id'] != "")
        {
            // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            $technicians = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                                    ->where('status','Active')
                                    ->where('service_center_id',$service_request['service_center_id'])
                                    ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        }
        else
        {
            $technicians=array(''=>trans('quickadmin.qa_please_select'));
        }

        $custAddressData = \App\Customer::where('id',$service_request['customer_id'])
                                        ->where('status','Active')
                                        ->get()->first();
          
        $parts=array();
        $products=array(''=>trans('quickadmin.qa_please_select'));                             
        if($service_request['company_id'] != "")
        {
            $customers = \App\Customer::where('company_id',$service_request['company_id'])
                                        ->where('status','Active')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

            $product_parts = \App\AssignPart::where('company_id',$service_request['company_id'])
                                ->with('product_parts')->get();

            $company_products = \App\AssignProduct::where('company_id',$service_request['company_id'])
                                ->with('product_id')->get();
            if(count($product_parts) > 0)
            {
                foreach($product_parts as $key => $value)
                {
                    $parts[$value->product_parts->id]=$value->product_parts->name;
                }   
            }
            if(count($company_products) > 0)
            {
                foreach($company_products as $key => $value)
                {
                    foreach($value->product_id as $details)
                    {
                        $products[$details->id]=$details->name;
                    }
                }
            }

        }
        else
        {
            $customers=array(''=>trans('quickadmin.qa_please_select'));
        }
             

        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');

        // get service log accroding to logged in user
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
        {
            $service_request_logs = ServiceRequestLog::select('service_request_id','action_made_service_center as action_made','created_at','user_id')
                                    ->where('service_request_id',$id)->get();
        }
        else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $service_request_logs = ServiceRequestLog::select('service_request_id','action_made_company as action_made','created_at','user_id')
                                    ->where('service_request_id',$id)->get();
        }
        else
        {
            $service_request_logs = ServiceRequestLog::where('service_request_id',$id)->get();
        }

        return view('admin.service_requests.edit', compact('service_request', 'enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName', 'service_request_logs', 'custAddressData','additional_charge_title'))->with('no', 1);
        // $user_name=ucwords('user name');
        // $subject='sub';
        // return view('admin.emails.service_request_detail_email', compact('service_request', 'enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName', 'service_request_logs', 'custAddressData','additional_charge_title','user_name','subject'))->with('no', 1);
    }

    /**
     * Update ServiceRequest in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceRequestsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceRequestsRequest $request, $id)
    {
        if (! Gate::allows('service_request_edit')) {
            return abort(401);
        }
        
        
        
        $service_request = ServiceRequest::findOrFail($id);

        if($request['service_center_id'] == "" && isset($request['suggested_service_center']))
        {
            if($request['suggested_service_center'] != "")
            {
                $request['service_center_id'] = $request['suggested_service_center'];
            }
        }
        if(isset($service_request->status) && isset($request['status'])){

           // insert service request log on status change 
           if($service_request->status !== $request['status']){
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Status is changed from ".$service_request->status." to ".$request['status'].".", 
                                                    'action_made_company'     =>  "Status is changed from ".$service_request->status." to ".$request['status'].".", 
                                                    'action_made_service_center'     =>  "Status is changed from ".$service_request->status." to ".$request['status'].".", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['company_id'] != "")
        {
            // insert service request log on company change 
            if($service_request->company_id != $request['company_id']){

                $company=\App\Company::where('id',$request['company_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Company assigned(".$company->name.").", 
                                                    'action_made_company'     =>  "Company assigned(".$company->name.").",
                                                    'action_made_service_center'     =>  "Company assigned.", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['customer_id'] != "")
        {
            // insert service request log on customer change 
            if($service_request->customer_id != $request['customer_id']){

                $customer=\App\Customer::where('id',$request['customer_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Customer assigned(".$customer->firstname.' '.$customer->lastname.").",
                                                    'action_made_company'     =>  "Customer assigned(".$customer->firstname.' '.$customer->lastname.").",
                                                    'action_made_service_center'     =>  "Customer assigned.", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['product_id'] != "")
        {
            // insert service request log on product change 
            if($service_request->product_id != $request['product_id']){

                $product=\App\Product::where('id',$request['product_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Product assigned(".$product->name.").", 
                                                    'action_made_company'     =>  "Product assigned(".$product->name.").", 
                                                    'action_made_service_center'     => "Product assigned(".$product->name.").", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['technician_id'] != "")
        {
            // insert service request log on technician change 
            if($service_request->technician_id != $request['technician_id']){

                $technician=\App\User::where('id',$request['technician_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Technician assigned(".$technician->name.").", 
                                                    'action_made_company'     =>  "Technician assigned.",
                                                    'action_made_service_center'     =>  "Technician assigned(".$technician->name.").", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['service_center_id'] != "")
        {
            
            if($service_request->status == "New")
            {
                $request['status']="Assigned";

                // service request log for assigned status
                $insertServiceRequestLogArr = array(
                                            'action_made'     =>   "Status is changed from New to Assigned.",
                                            'action_made_company'     =>   "Status is changed from New to Assigned.",
                                            'action_made_service_center'     =>   "Status is changed from New to Assigned.", 
                                            'service_request_id'   =>   $id,
                                            'user_id'   =>   auth()->user()->id
                                        );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }

            if($service_request->service_center_id != $request['service_center_id']){
                // insert service request log on service center change 
                $service_center=\App\ServiceCenter::where('id',$request['service_center_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Service center assigned(".$service_center->name.").", 
                                                    'action_made_company'     =>  "Service center assigned.",
                                                    'action_made_service_center'     =>  "Service center assigned(".$service_center->name.").",
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }  

        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+(($request['additional_charges'] == "")?0:number_format((float)$request['additional_charges'], 2, '.', ''));

        // convert to json
        $request['additional_charges']= json_encode(array($request['additional_charges_title'] => number_format((float)$request['additional_charges'], 2, '.', '')));


        $distance_charge=\App\ManageCharge::get()->first();

        $request['km_distance'] = ($request['km_distance'] == "") ? 0 : number_format((float)$request['km_distance'], 2, '.', '');
        $request['km_charge'] = ($request['km_charge'] == "") ? number_format((float)$distance_charge->km_charge, 2, '.', '') : number_format((float)$request['km_charge'], 2, '.', '');

        $request['transportation_charge'] = ($request['transportation_charge'] == "") ? 0 : number_format((float)$request['transportation_charge'], 2, '.', '');

        $total_amount+=$request['transportation_charge'];
        // if($request['service_type'] == 'repair')
        // {
        //     if($request['service_center_id'] != "" && $request['customer_id'] != "")
        //     {
        //         // calculate transportation charges for different city

        //         $centerDetail=\App\ServiceCenter::findOrFail($request['service_center_id']);
        //         $customerDetail=\App\Customer::findOrFail($request['customer_id']);
                
        //         $supportedCenterDetail=\App\ServiceCenter::Where('supported_zipcode', 'like', '%' . $customerDetail->zipcode . '%')->where('id',$request['service_center_id'])->get();
        //         if(count($supportedCenterDetail) <= 0)// && $customerDetail->zipcode != $centerDetail->zipcode)
        //         {
        //             // calculate transportation charges for unsupported zipcode
        //             $customer_latitude=$customerDetail->location_latitude;
        //             $customer_longitude=$customerDetail->location_longitude;

        //             $center_latitude=$centerDetail->location_latitude;
        //             $center_longitude=$centerDetail->location_longitude;
                    
        //             $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

        //             $request['km_distance']=$distance;

        //             $distance_charge=\App\ManageCharge::get()->first();
        //             $request['km_charge']=$distance_charge->km_charge;
        //             $total_amount+=($distance*$distance_charge->km_charge);
                    
                   
        //         }
        //     } 
        // }


        $request['amount']=$total_amount;  
        // calculate total amount work end

        $request_status=$service_request->status;
        

        $service_request->update($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));
        if($request_status != $request['status'])
        {
            //send mail on every status change
            $msg='Status is changed from '.$request_status.' to '.$request['status'].'.';
            // echo $id;exit;
            SendMailHelper::sendRequestUpdateMail($id,$msg);
        }
        
        if($request['status'] == "Closed")
        {
            // return $this->createReceiptPDF($request->all());
            return $this->createReceiptPDF($id);
        }
        else
        {
            return redirect()->route('admin.service_requests.index');
        }
        // return redirect()->route('admin.service_requests.index');
    }


    /**
     * Display ServiceRequest.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('service_request_view')) {
            return abort(401);
        }
        $service_request = ServiceRequest::findOrFail($id);

        $additional_charge_array=json_decode($service_request['additional_charges']);
        $additional_charge_title="";
        $additional_charges="";
        if(!empty($additional_charge_array))
        {
            // Worked to display json value in edit page
            foreach ($additional_charge_array as $key => $value) {
                $additional_charge_title=str_replace('_empty_', '', $key);
                $additional_charges=$value;
            }
        }
        
        $service_request['additional_charges']=$additional_charges;


        $service_request_logs = ServiceRequestLog::where('service_request_id',$id)->get();
        return view('admin.service_requests.show', compact('service_request', 'service_request_logs','additional_charge_title'))->with('no', 1);
    }


    /**
     * Remove ServiceRequest from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('service_request_delete')) {
            return abort(401);
        }
        $service_request = ServiceRequest::findOrFail($id);
        if(isset($service_request->service_center_id))
        {
            if($service_request->service_center_id != "" )
            {    
                if(auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.ADMIN_ROLE_ID'))
                {
                    // if service center is assigned, company admin and company user can not delete request, ONLY admin or super admin can delete the request
                    return  redirect()->route('admin.service_requests.index')->withErrors("Service center is already assigned.");
                }
            }
        }
        
        $service_request->delete();

        return redirect()->route('admin.service_requests.index');
    }

    /**
     * Delete all selected ServiceRequest at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {   
        if (! Gate::allows('service_request_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = ServiceRequest::whereIn('id', $request->input('ids'))->get();

            $not_deleted=0;
            foreach ($entries as $entry) {

                if($entry->service_center_id != "")
                {

                    // if service center is assigned, company admin and company user can not delete request, ONLY admin or super admin can delete the request
                    if(auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.ADMIN_ROLE_ID'))
                    {
                        $not_deleted++;  
                    }
                    else
                    {
                        $entry->delete();
                    }
                    
                    
                }
                else
                {
                    $entry->delete();

                }
            }
            if($not_deleted > 0)
            {
                redirect()->route('admin.service_requests.index')->withErrors("Some request is not deleted.");
            }
        }
    }


    /**
     * Restore ServiceRequest from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('service_request_delete')) {
            return abort(401);
        }
        $service_request = ServiceRequest::onlyTrashed()->findOrFail($id);
        $service_request->restore();

        return redirect()->route('admin.service_requests.index');
    }

    /**
     * Permanently delete ServiceRequest from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('service_request_delete')) {
            return abort(401);
        }
        $service_request = ServiceRequest::onlyTrashed()->findOrFail($id);
        $service_request->forceDelete();

        return redirect()->route('admin.service_requests.index');
    }
    public function createReceiptPDF($id)
    {
        $request = ServiceRequest::findOrFail($id);
        $request_parts=$request->parts->pluck('id')->toArray();

        if($request['service_center_id'] != "" && 
            $request['customer_id'] != ""
            )
        {
            $centerDetail=\App\ServiceCenter::findOrFail($request['service_center_id']);
            if($request['technician_id'] != "")
            {
                $technicianDetail=\App\User::findOrFail($request['technician_id']);
            }
            $customerDetail=\App\Customer::findOrFail($request['customer_id']);
            $companyDetail=\App\Company::findOrFail($request['company_id']);
            $productDetail=\App\Product::findOrFail($request['product_id']);
           
            
            $compCustHTML="<div style='float:left;width:50%;'>
                    <b>Company: ".$companyDetail->name."</b>
                    <div>".$companyDetail->address_1."</div>
                    <div>".$companyDetail->address_2."</div>
                    <div>".$companyDetail->city.",".$companyDetail->state." - ".$companyDetail->zipcode."</div>
                    <b>Customer: ".$customerDetail->firstname." ".$customerDetail->lastname."</b>
                    <div>".$customerDetail->address_1."</div>
                    <div>".$customerDetail->city.",".$customerDetail->state." - ".$customerDetail->zipcode."</div>
                    <div>Phone: ".$customerDetail->phone."</div>
                </div>";
            

            $technician= ($request['technician_id'] != "")? "<div><b>Technician: ".$technicianDetail->name."</b></div>":"";   
            $centerHTML="<div style='float:left;width:50%;'>
                            <b>Service Center: ".$centerDetail->name."</b>
                            <div>".$centerDetail->address_1."</div>
                            <div>".$centerDetail->address_2."</div>
                            <div>".$centerDetail->city.",".$centerDetail->state." - ".$centerDetail->zipcode."</div>
                            ".$technician."
                        </div>";

            $installation_charge=($request['installation_charge'] != "" && $request['installation_charge'] != 0)? "<tr><td colspan='2'>Installation Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['installation_charge'],2)."</td></tr>":"";

            $service_charge=($request['service_charge'] != "" && $request['service_charge'] != 0)? "<tr><td colspan='2'>Service Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['service_charge'],2)."</td></tr>":"";

            // $km_distance=($request['km_distance'] != "" && $request['km_distance'] != 0)? "<tr><td colspan='2'>Distance</td><td class='price'>".$request['km_distance']."</td></tr>":"";

            $km_charge="";
            if($request['km_distance'] != "" && $request['km_distance'] != 0)
            {
                $km_charge=($request['km_charge'] != "" && $request['km_charge'] != 0)? "<tr><td colspan='2'>Transportation Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['km_charge'] * $request['km_distance'],2)."<br/>(".number_format($request['km_charge'],2)." rs per km)</td></tr>":"";
            }    
            

            $additional_charges=($request['additional_charges'] != "" && $request['additional_charges'] != 0)? "<tr><td colspan='2'>Additional Charge </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['additional_charges'],2)."</td></tr>":"";

            $total_amount="<tr><td colspan='2'><b>Total amount</b></td><td class='price'><b><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['amount'],2)."</b></td></tr>";

            $parts_used="";
            if($request['service_type'] == "repair" && count($request_parts) > 0)
            {
                $obj= new ServiceRequest();
                $parts= $obj->getServiceRequestParts($request_parts);  
                $parts_used="<tr><td>Parts Used</td><td colspan='2'>".$parts->name."</td></tr>";  
            }
            
            $productHTML="<div><table class='table' style='width:100%;'>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>".$productDetail->name."</td>
                                        <td>".$productDetail->category->name."</td>
                                        <td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($productDetail->price,2)."</td>
                                    </tr>
                                    <tr>
                                    <td style='border:0;'></td>
                                    </tr>
                                    <tr>
                                    <td style='border:0;'></td>
                                    </tr><tr>
                                    <td style='border:0;'></td>
                                    </tr>
                                    ".$parts_used."
                                    ".$installation_charge."
                                    ".$service_charge."
                                    ".$km_charge."
                                    ".$additional_charges."
                                    
                                    ".$total_amount."
                                </tbody>
                        </table></div>";

            // final html of PDF    
            $html="<html>
                    <head>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
                        <style type='text/css'>
                           
                            /* .price{
                                color:#120CEA;
                            } */
                        </style>
                    </head>";
            $html.="<body>
                    <h1 style='text-align:center;'>Bill Receipt</h1>";
            
            $html.="<div style='height:18%;margin-top:5%;'>".$compCustHTML.$centerHTML."</div>";
            $html.=$productHTML;

            $html.="</body></html>";
            // echo $html;exit;
            // print_r ($request); exit(); 
            
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation
            // $dompdf->setPaper('A4', 'landscape');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        }
        else
        {
            return redirect()->route('admin.service_requests.index');
        }
        
    }
    public function requestCharge(Request $request)
    {
        
        // ajx function to get service request charge details
        
        $details=$request->all();

        $data['installation_charge']=0;
        $data['service_charge']=0;
        $data['statusOptions']="";
        if($details['serviceType'] == "installation")
        {
            $enum_status = ServiceRequest::$enum_installation_status;
            foreach($enum_status as $key => $value)
            {
                $data['statusOptions'].="<option value='".$key."'>".$value."</option>";   
            } 
            if($details['companyId'])
            {
                $companyDetails=\App\Company::findOrFail($details['companyId']);
                $data['installation_charge']=$companyDetails->installation_charge;
            }
            

        }
        else if($details['serviceType'] == "repair")
        {
            $enum_status = ServiceRequest::$enum_repair_status;
            foreach($enum_status as $key => $value)
            {
                $data['statusOptions'].="<option value='".$key."'>".$value."</option>";   
            } 
            if($details['productId'])
            {
                $productDetails=\App\Product::findOrFail($details['productId']);
                $data['service_charge']=$productDetails->category->service_charge;
            }
            
            
        }
        
        echo json_encode($data);
        exit;
    }
    public function getCompanyDetails(Request $request)
    {
        
        // ajx function to get customers, product and parts of particular company
        
        $details=$request->all();

        $data['custOptions']="<option value=''>".trans('quickadmin.qa_please_select')."</option>";
        $data['partOptions']="";
        $data['productOptions']="<option value=''>".trans('quickadmin.qa_please_select')."</option>";

        if($details['companyId'] != "")
        {
            $customers = \App\Customer::where('company_id',$details['companyId'])
                                ->where('status','Active')->get();

            $product_parts = \App\AssignPart::where('company_id',$details['companyId'])
                                ->with('product_parts')->get();

            $products = \App\AssignProduct::where('company_id',$details['companyId'])
                                ->with('product_id')->get();
            // echo count($product_parts);
            //             echo "<pre>"; print_r ($product_parts); echo "</pre>"; exit();        
            if(count($customers) > 0)
            {
                foreach($customers as $key => $value)
                {
                    $data['custOptions'].="<option value='".$value->id."'>".$value->firstname.' '.$value->lastname."</option>";   
                }   
            }
            if(count($product_parts) > 0)
            {
                foreach($product_parts as $key => $value)
                {
                    $data['partOptions'].="<option value='".$value->product_parts->id."'>".$value->product_parts->name."</option>";   
                    
                }   
            }
            if(count($products) > 0)
            {
                foreach($products as $key => $value)
                {
                    // echo "<pre>"; print_r ($value->product_id); echo "</pre>"; exit();
                    foreach($value->product_id as $details)
                    {
                        $data['productOptions'].="<option value='".$details->id."'>".$details->name."</option>";   
                    }
                }   
            }
        }
        echo json_encode($data);
        exit;
    }
    public function getTransporationCharge(Request $request)
    {
        // ajx function to get transportation charges if customer is in supported zipcode area
        
        $details=$request->all();

        if($details['customerId'] != "" && $details['serviceCenterId'] != "")
        {
            $centerDetail=\App\ServiceCenter::findOrFail($details['serviceCenterId']);
            $customerDetail=\App\Customer::findOrFail($details['customerId']);
            
            $supportedCenterDetail=\App\ServiceCenter::Where('supported_zipcode', 'like', '%' . $customerDetail->zipcode . '%')->where('id',$details['serviceCenterId'])->get();

            $distance_charge=\App\ManageCharge::get()->first();
            $data['km_charge']=$distance_charge->km_charge;

            if(count($supportedCenterDetail) <= 0)// && $customerDetail->zipcode != $centerDetail->zipcode)
            {
                // calculate transportation charges for unsupported zipcode
                $customer_latitude=$customerDetail->location_latitude;
                $customer_longitude=$customerDetail->location_longitude;

                $center_latitude=$centerDetail->location_latitude;
                $center_longitude=$centerDetail->location_longitude;
                
                $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

                $data['km_distance']=$distance;

                
                $data['transportation_amount']=($distance*$distance_charge->km_charge);
                $data['supported'] = false;
               
            }
            else
            {
                $data['supported'] = true;
            }
        }
        echo json_encode($data);
        exit;
    }

    public function getSuggestedServiceCenter(Request $request)
    {
        
        // ajx function to get suggested service center for particular customer
        $details=$request->all();
        $data['service_centers']=array();
        if($details['customerId'] != "")
        {
            $customer = \App\Customer::where('id',$details['customerId'])
                                ->where('status','Active')->get()->first();

           
            // echo "<pre>"; print_r ($customer); echo "</pre>"; exit();
            if(count($customer) > 0)
            {
                $data['service_centers'] = \App\ServiceCenter::Where('supported_zipcode', 'like', '%' . $customer->zipcode . '%')->get();
            }
        }
        echo json_encode($data);
        exit;
    
    }

    public function getCustomerAddress(Request $request)
    {
        
        // ajx function to get customer address
        
        $details=$request->all();
        $data['address']="";
        if($details['customerId'] != "")
        {
            $customer = \App\Customer::where('id',$details['customerId'])
                                ->where('status','Active')->get()->first();
            // echo "<pre>"; print_r ($customer); echo "</pre>"; exit();
            if(count($customer) > 0)
            {
                $data['address'].=$customer->address_1."<br/>";
                if(!empty($customer->address_2))
                {
                    $data['address'].=$customer->address_2."<br/>";
                }
                $data['address'].=$customer->city."<br/>".$customer->state."-".$customer->zipcode;  
            }
        }
        echo json_encode($data);
        exit;
    
    }

    public function getTechnicians(Request $request)
    {
        
        // ajx function to get technicians of particular service center
        
        $details=$request->all();
        $data['options']="<option value=''>".trans('quickadmin.qa_please_select')."</option>";
        if($details['serviceCenterId'] != "")
        {
            $query = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                        ->orderby('name');

            $query->where('service_center_id',$details['serviceCenterId']);
            $query->where('status','Active');
            
            $technicians = $query->get();
            if(count($technicians) > 0)
            {
                foreach($technicians as $key => $value)
                {
                    $data['options'].="<option value='".$value->id."'>".$value->name."</option>";
                    
                }
                
            }
        }
        echo json_encode($data);
        exit;
    
    }




}
