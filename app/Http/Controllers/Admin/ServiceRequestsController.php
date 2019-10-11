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
use Session;
use DB;
use Log;

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
        // echo "<pre>"; 
        // print_r(session::all());exit;
        // echo "<pre>"; print_r (session::all()); echo "</pre>"; exit();

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

        // filter dropdown details
       
       
        $companies = \App\Company::select(DB::raw('CONCAT(UCASE(LEFT(name, 1)),SUBSTRING(name, 2)) as name'),'id')->where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');
        $total_paid_amount = 0;
        $total_due_amount = 0;
        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            //if logged in user is company admin and company user
            $products=array(''=>trans('quickadmin.qa_show_all')); 
            $customers = \App\Customer::where('company_id',auth()->user()->company_id)
                                        ->where('status','Active')
                                        ->select(DB::raw('CONCAT(CONCAT(UCASE(LEFT(customers.firstname, 1)),SUBSTRING(customers.firstname, 2))," ",CONCAT(UCASE(LEFT(customers.lastname, 1)),SUBSTRING(customers.lastname, 2))) as firstname'),'id')
                                        ->orderBy('firstname')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
                                        
            $company_products = \App\AssignProduct::where('company_id',auth()->user()->company_id)
                                ->whereHas('product', function ($q) {
                                        $q->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product->name;
                                });
            
            if(count($company_products) > 0)
            {
                foreach($company_products as $key => $value)
                {
                    $products[$value->product->id]=$value->product->name;
                }
            }             
        }
        else
        {
            $customers=array(''=>trans('quickadmin.qa_show_all'));
            $products=array(''=>trans('quickadmin.qa_show_all'));
            // if($request->session()->has('filter_company')){
            if(!empty(session('filter_company'))){

                // if company filter is applied
                $customers=\App\Customer::where('status','Active')
                                        ->where('company_id',session('filter_company'))
                                        ->select(DB::raw('CONCAT(CONCAT(UCASE(LEFT(customers.firstname, 1)),SUBSTRING(customers.firstname, 2))," ",CONCAT(UCASE(LEFT(customers.lastname, 1)),SUBSTRING(customers.lastname, 2))) as firstname'),'id')
                                        ->orderBy('firstname')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_show_all'), '');

                $products=array(''=>trans('quickadmin.qa_show_all')); 
                $company_products = \App\AssignProduct::where('company_id',session('filter_company'))
                                    ->whereHas('product', function ($q) {
                                        $q->where('status', 'Active');
                                    })->get()
                                    ->sortBy(function($value, $key) {
                                      return $value->product->name;
                                    });
                
                if(count($company_products) > 0)
                {
                    foreach($company_products as $key => $value)
                    {
                        $products[$value->product->id]=ucfirst($value->product->name);
                    }
                } 
            }
            else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                // fetch product and customer of assigned request to service center admin for filter functionality
                $service_requests = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->get();
                if(count($service_requests) > 0)
                {
                    foreach($service_requests as $key => $value)
                    {
                        if($value->customer->status == "Active"){
                            $customers[$value->customer_id]=ucfirst($value->customer->firstname).' '.ucfirst($value->customer->lastname);
                        }
                        if($value->product->status == "Active")
                        {
                            $products[$value->product_id]=ucfirst($value->product->name);
                        }
                        
                    }
                }   
                $total_paid_amount = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',auth()->user()->service_center_id)->where('is_paid','1')->sum('amount');

                $total_due_amount = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',auth()->user()->service_center_id)->where('is_paid','0')->sum('amount');
            }
            else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                // fetch product and customer of assigned request to technician for filter functionality
                $service_requests = ServiceRequest::where('technician_id',auth()->user()->id)->get();
                if(count($service_requests) > 0)
                {
                    foreach($service_requests as $key => $value)
                    {
                        if($value->customer->status == "Active"){
                            $customers[$value->customer_id]=ucfirst($value->customer->firstname).' '.ucfirst($value->customer->lastname);
                        }
                        if($value->product->status == "Active")
                        {
                            $products[$value->product_id]=ucfirst($value->product->name);
                        }
                    }
                }  
            }
            

            
        }
        $serviceCenterName = \App\ServiceCenter::where('id',auth()->user()->service_center_id)->where('status','Active')->get()->pluck('name');
        
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
        {
            $technicians = \App\User::select(DB::raw('CONCAT(UCASE(LEFT(name, 1)),SUBSTRING(name, 2)) as name'),'id')
                                    ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                                    ->where('status','Active')
                                    ->where('service_center_id',auth()->user()->service_center_id)
                                    ->orderBy('name')
                                    ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
            $service_centers = '';
        }
        else
        {

            $service_centers = \App\ServiceCenter::select(DB::raw('CONCAT(UCASE(LEFT(name, 1)),SUBSTRING(name, 2)) as name'),'id')->where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
            if(!empty(session('filter_service_center')))
            {
                $technicians = \App\User::select(DB::raw('CONCAT(UCASE(LEFT(name, 1)),SUBSTRING(name, 2)) as name'),'id')
                                    ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                                    ->where('status','Active')
                                    ->where('service_center_id',session('filter_service_center'))
                                    ->orderBy('name')
                                    ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');

                $total_paid_amount = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',session('filter_service_center'))->where('is_paid','1')->sum('amount');

                $total_due_amount = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',session('filter_service_center'))->where('is_paid','0')->sum('amount');
            }
            else
            {
                $technicians=array(''=>trans('quickadmin.qa_show_all'));
            }
            
        }
        $request_stauts = ServiceRequest::$enum_status;
        asort($request_stauts); // sort array
        $request_stauts = ['' => trans('quickadmin.qa_show_all')] + $request_stauts;

        $request_type = ['' => trans('quickadmin.qa_show_all')] + ServiceRequest::$enum_service_type;

        return view('admin.service_requests.index', compact('companies', 'customers', 'products', 'companyName', 'serviceCenterName', 'service_centers', 'technicians','request_stauts','request_type','total_paid_amount','total_due_amount'));

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
        
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){
            $columnArray = array(
                0 => 'service_requests.id',
                1 =>'customers.firstname' ,
                2 =>'service_requests.service_type' ,
                3 =>'products.name' ,
                4 =>'service_requests.amount' ,
                5 =>'service_requests.created_at',
                6 =>'service_requests.created_by',
                7 =>'service_requests.status',
                8 =>'service_requests.is_paid',
                
            );
        }else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID')){
            
            // 0 offset is skipped for checkbox
            $columnArray = array(
                1 => 'service_requests.id',
                // 2 =>'companies.name' ,
                2 =>'customers.firstname' ,
                3 =>'service_requests.service_type' ,
                4 =>'products.name' ,
                // 5 =>'service_requests.amount' ,
                5 =>'service_requests.status',
                6 =>'service_requests.created_at',
                7 =>'service_requests.created_by',
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
                8 =>'service_requests.status',
                9 =>'service_requests.is_paid',
                10 =>'service_requests.created_at',
                11 =>'service_requests.created_by'
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

            $serviceRequestObj = new ServiceRequest();  
            $requestFilterCount =  $serviceRequestObj->getFilterRequestsCount($request->all());

            $enum_status_color = ServiceRequest::$enum_status_color_code;

            $service_requestsQuery = ServiceRequest::select('customers.firstname as fname','customers.phone','service_centers.name as sname','products.name as pname','service_requests.amount','service_requests.created_at','service_requests.service_type','service_requests.is_accepted','service_requests.created_by','users.name as createdbyName','service_requests.status','companies.name as cname','service_requests.id','service_requests.is_paid',DB::raw('CONCAT(CONCAT(UCASE(LEFT(customers.firstname, 1)),SUBSTRING(customers.firstname, 2))," ",CONCAT(UCASE(LEFT(customers.lastname, 1)),SUBSTRING(customers.lastname, 2))) as firstname'))
            ->leftjoin('users','service_requests.created_by','=','users.id')
            ->leftjoin('companies','service_requests.company_id','=','companies.id')
            ->leftjoin('roles','service_requests.technician_id','=','roles.id')
            ->leftjoin('customers','service_requests.customer_id','=','customers.id')
            ->leftjoin('products','service_requests.product_id','=','products.id')
            ->leftjoin('service_centers','service_requests.service_center_id','=','service_centers.id');

            if(!empty($request['startdate']) && isset($request['startdate'])){
                $service_requestsQuery->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$request['startdate']."' AND '".$request['enddate']."'");
            }
            
            $service_requestsQuery->whereNull('companies.deleted_at')
            ->whereNull('customers.deleted_at')
            ->whereNull('products.deleted_at')
            ->Where('companies.status','Active')
            ->Where('customers.status','Active')
            ->Where('products.status','Active')
            // ->Where('service_centers.status','Active')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir);

            // fetch service request according to logged in user
            if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                $service_requestsQuery->Where('service_requests.service_center_id', auth()->user()->service_center_id);
            }
            else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                $service_requestsQuery->Where('service_requests.technician_id', auth()->user()->id);
            }
            else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $service_requestsQuery->Where('service_requests.company_id', auth()->user()->company_id);
            }

            // filter data from table and store into session variable
            if(!empty($request->input('company')))
            {   
                $request->session()->put('filter_company', $request['company']);
                $service_requestsQuery->Where('service_requests.company_id', $request['company']);
            }
            else
            {
                $request->session()->forget('filter_company');
            }
            if(!empty($request->input('customer')))
            {   
                $request->session()->put('filter_customer', $request['customer']);
                $service_requestsQuery->Where('service_requests.customer_id', $request['customer']);
            }
            else
            {
                $request->session()->forget('filter_customer');
            }
            if(!empty($request->input('product')))
            {   
                $request->session()->put('filter_product', $request['product']);
                $service_requestsQuery->Where('service_requests.product_id', $request['product']);
            }
            else
            {
                $request->session()->forget('filter_product');
            }
            if(!empty($request->input('serviceCenter')))
            {   
                $request->session()->put('filter_service_center', $request['serviceCenter']);
                $service_requestsQuery->Where('service_requests.service_center_id', $request['serviceCenter']);
            }
            else
            {
                $request->session()->forget('filter_service_center');
            }
            if(!empty($request->input('technician')))
            {   
                $request->session()->put('filter_technician', $request['technician']);
                $service_requestsQuery->Where('service_requests.technician_id', $request['technician']);
            }
            else
            {
                $request->session()->forget('filter_technician');
            }
            
            if(!empty($request->input('status')))
            {   
                $request->session()->put('filter_request_status', $request['status']);
                $service_requestsQuery->Where('service_requests.status', $request['status']);
            }
            else
            {
                $request->session()->forget('filter_request_status');
            }

            if(!empty($request->input('type')))
            {   
                $request->session()->put('filter_request_type', $request['type']);
                $service_requestsQuery->Where('service_requests.service_type', $request['type']);
            }
            else
            {
                $request->session()->forget('filter_request_type');
            }
            

            // if(!empty($request->input('startdate')) && !empty($request->input('enddate')))
            // {   
            //     $request->session()->put('filter_start_date', $request->input('startdate'));
            //     $request->session()->put('filter_end_date', $request->input('enddate'));
            //     $service_requestsQuery->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$request->input('enddate')."' AND '".$request->input('enddate')."'");
                
            // }
            // else
            // {
            //     $request->session()->forget('filter_start_date');
            //     $request->session()->forget('filter_end_date');
            // }
           
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $service_requestsQuery->where(function ($query) use ($searchVal) {

                    $RequestedId = trim($searchVal,'JW');
                    $clearRequestId = ltrim($RequestedId, '0');

                    if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                    {
                        $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                        $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');
                    }

                    if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID')){
                        $query->orWhere('service_requests.amount', 'like', '%' . $searchVal . '%');
                    }
                    
                    $query->orWhere(DB::raw("CONCAT(`customers`.`firstname`,' ', `customers`.`lastname`)"), 'like', '%' . $searchVal . '%');
                    $query->orWhere('customers.phone', 'like', '%' . $searchVal . '%');
                    $query->orWhere('products.name', 'like', '%' . $searchVal . '%');
                    // $query->orWhere('service_requests.amount', 'like', '%' . $searchVal . '%');
                    $query->orWhere('service_requests.service_type', 'like', '%' . $searchVal . '%');
                    $query->orWhere('service_requests.id', 'like', '%' . $clearRequestId . '%');
                    $query->orWhere('service_requests.status', 'like', '%' . $searchVal . '%');

                });
            }
            
            $service_requests = $service_requestsQuery->get();

        }

        // fetch total count according to logged in user
        $countRecordQuery = ServiceRequest::select('*')
                            ->leftjoin('companies','service_requests.company_id','=','companies.id')
                            ->leftjoin('roles','service_requests.technician_id','=','roles.id')
                            ->leftjoin('customers','service_requests.customer_id','=','customers.id')
                            ->leftjoin('products','service_requests.product_id','=','products.id')
                            ->leftjoin('service_centers','service_requests.service_center_id','=','service_centers.id')
                            ->whereNull('companies.deleted_at')
                            ->whereNull('customers.deleted_at')
                            ->whereNull('products.deleted_at')
                            ->Where('companies.status','Active')
                            ->Where('customers.status','Active')
                            ->Where('products.status','Active')
                            // ->Where('service_centers.status','Active')
                            ;
        if(!empty($service_requests)){

            if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                $countRecordQuery->Where('service_requests.service_center_id', auth()->user()->service_center_id);
            }
            else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                $countRecordQuery->Where('service_requests.technician_id', auth()->user()->id);
            }
            else if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $countRecordQuery->Where('service_requests.company_id', auth()->user()->company_id);
            }
            $countRecord = $countRecordQuery->count('service_requests.id');

            foreach ($service_requests as $key => $SingleServiceRequest) {

                $ViewButtons = '';
                $EditButtons = '';
                $DeleteButtons = '';

                if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID')){

                    // $tableField['company_name'] =$SingleServiceRequest->cname;
                    if (Gate::allows('service_request_delete')) {
                        // $tableField['checkbox'] = '<input type="checkbox" class="dt-body-center" style="text-align: center;" name="checkbox_'.$key.'">';
                        $tableField['checkbox'] = '';
                    }

                }else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){

                    $tableField['service_center'] =(!empty($SingleServiceRequest->sname))?ucfirst($SingleServiceRequest->sname):'<div style="text-align:center;">-</div>';
                    $tableField['amount'] = '<i class="fa fa-rupee"></i> '.number_format($SingleServiceRequest->amount,2);

                    $paidStatus = 'Due';
                    if($SingleServiceRequest->is_paid == 1 && $SingleServiceRequest->status == "Closed" ){
                        $paidStatus = 'Paid';
                    }
                    $tableField['amount_paid'] = ucfirst($paidStatus);

                }else{
                    $tableField['service_center'] =(!empty($SingleServiceRequest->sname))?ucfirst($SingleServiceRequest->sname):'<div style="text-align:center;">-</div>';
                    $tableField['company_name'] =ucfirst($SingleServiceRequest->cname);

                    $paidStatus = 'Due';
                    if($SingleServiceRequest->is_paid == 1 && $SingleServiceRequest->status == "Closed" ){
                        $paidStatus = 'Paid';
                    }
                    $tableField['amount_paid'] = ucfirst($paidStatus);

                    if (Gate::allows('service_request_delete')) {
                        // $tableField['checkbox'] = '<input type="checkbox" class="dt-body-center" style="text-align: center;" name="checkbox_'.$key.'">';
                        $tableField['checkbox'] = '';
                    }
                    $tableField['amount'] ='<i class="fa fa-rupee"></i> '.number_format($SingleServiceRequest->amount,2);
                    
                }
                $tableField['sr_no'] = 'JW'.sprintf("%04d", $SingleServiceRequest->id);
                $tableField['customer'] = $SingleServiceRequest->firstname."<br/>(".$SingleServiceRequest->phone.")";
                $tableField['service_type'] =ucfirst($SingleServiceRequest->service_type);
                $tableField['product'] =ucfirst($SingleServiceRequest->pname);

                // $tableField['request_status'] =$SingleServiceRequest->status;

                $tableStatusColor = '';
                if($SingleServiceRequest->status != ''){
                    $tableStatusColor = '<span class="headerTitle" style="color:'.$enum_status_color[$SingleServiceRequest->status].'">'.$SingleServiceRequest->status.'</span>';
                }
                $tableField['request_status'] = $tableStatusColor;
                
                if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){
                    $createdByName = '-';
                    if($SingleServiceRequest->createdbyName != ''){
                        $createdByName = $SingleServiceRequest->createdbyName;
                    }
                    $tableField['created_by'] = ucfirst($createdByName);
                }

                $tableField['created_at'] = date('d/m/Y',strtotime($SingleServiceRequest->created_at));

                if (Gate::allows('service_request_view')) {
                    $ViewButtons = '<a href="'.route('admin.service_requests.show',$SingleServiceRequest->id).'" class="btn btn-xs btn-primary">View</a>';
                }

                if((auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')) 
                || (auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID') && $SingleServiceRequest->status != 'Closed') 
                || (auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID') && $SingleServiceRequest->is_accepted == 1 && $SingleServiceRequest->status != 'Closed') ) 
                {

                    if (Gate::allows('service_request_edit')) {
                        $EditButtons = '<a href="'.route('admin.service_requests.edit',$SingleServiceRequest->id).'" class="btn btn-xs btn-info">Edit</a>';
                    }
                    
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
            "recordsTotal"    => intval($countRecord),  
            "recordsFiltered" => intval($requestFilterCount),
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
        
        $companies = \App\Company::where('status','Active')
                                ->orderBy('name')
                                ->get()->pluck('name', 'id')
                                ->prepend(trans('quickadmin.qa_please_select'), '');
        // $customers = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $customers = \App\Customer::get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $parts=array();
            $products=array(''=>trans('quickadmin.qa_please_select')); 

            $customers = \App\Customer::where('company_id',auth()->user()->company_id)
                                        ->where('status','Active')
                                        ->select(DB::raw('CONCAT(CONCAT(UCASE(LEFT(customers.firstname, 1)),SUBSTRING(customers.firstname, 2))," ",CONCAT(UCASE(LEFT(customers.lastname, 1)),SUBSTRING(customers.lastname, 2))) as firstname'),'id')
                                        ->orderBy('firstname')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
                                        
            $product_parts = \App\AssignPart::where('company_id',auth()->user()->company_id)
                                ->whereHas('product_parts', function ($q) {
                                        $q->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product_parts->name;
                                });

            $company_products = \App\AssignProduct::where('company_id',auth()->user()->company_id)
                                ->whereHas('product', function ($q) {
                                        $q->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product->name;
                                });

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
                    $products[$value->product->id]=$value->product->name;
                }
            }                            


        }
        else
        {
            $customers=array(''=>trans('quickadmin.qa_please_select'));
            $products = \App\Product::where('status','Active')
                                    ->orderBy('name')
                                    ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            $company_products = $products;
            $parts = \App\ProductPart::where('status','Active')
                                    ->orderBy('name')
                                    ->get()
                                    ->pluck('name', 'id');
        }

        $distance_charge=\App\ManageCharge::get()->where('status','Active')->first();
        $km_charge = 0;
        if(!empty($distance_charge))
        {
            $km_charge =$distance_charge->km_charge;
        }

        $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $technicians = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        

        $enum_service_type = ServiceRequest::$enum_service_type;
                    $enum_call_type = ServiceRequest::$enum_call_type;
                    $enum_call_location = ServiceRequest::$enum_call_location;
                    $enum_priority = ServiceRequest::$enum_priority;
                    $enum_is_item_in_warrenty = ServiceRequest::$enum_is_item_in_warrenty;
                    $enum_mop = ServiceRequest::$enum_mop;
                    $enum_status = ServiceRequest::$enum_status;
        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');
        
        $enum_company_status = \App\Company::$enum_status;
        $enum_customer_status = \App\Customer::$enum_status;
        $enum_service_center_status = \App\ServiceCenter::$enum_status;
        $enum_technician_status = \App\User::$enum_status;

        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');
        // Config::get('constants.PRE_ADDITIONAL_CHARGES_FOR');

        $ProductAssignMessage = '';
        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            if(count($company_products) == 0){
                $ProductAssignMessage = 'There are no products assigned to the company. Please contact Administrator.';
            }
        }
        
        return view('admin.service_requests.create', compact('enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName','km_charge', 'enum_company_status', 'enum_customer_status', 'enum_service_center_status', 'enum_technician_status','pre_additional_charge_array','ProductAssignMessage'));
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
       
        if($request['call_type'] == "Warranty"){

            if($request['warranty_card_number'] != '' && $request['online_serial_number'] != ''){
                $request['warranty_card_number'] = $request['warranty_card_number'];
                $request['online_serial_number'] = $request['online_serial_number'];
            }
        }else{
            $request['warranty_card_number'] = '';
            $request['online_serial_number'] = '';
        }
        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+(($request['additional_charges'] == "")?0:number_format((float)$request['additional_charges'], 2, '.', ''));

        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');

        $predefine_additional_charge_array = [];
        $actual_value = '';
        $predefine_additional_charge_array['option'] = [];
        if(isset($request['existingAdditional_charge_for']) && $request['existingAdditional_charge_for'] != '' ){

            foreach ($request['existingAdditional_charge_for'] as $key => $existingAdditional_charge_for_Vlaue) {

                if($existingAdditional_charge_for_Vlaue != ''){

                    $actual_value = ($pre_additional_charge_array[$existingAdditional_charge_for_Vlaue] == "")?0:number_format((float)$request['existingAdditional_charge'][$key], 2, '.', '');

                    $total_amount+=$actual_value;

                    $predefine_additional_charge_array['option'][$key]= array($pre_additional_charge_array[$existingAdditional_charge_for_Vlaue] => number_format((float)$actual_value, 2, '.', ''));

                }
                
            }
            if(isset($predefine_additional_charge_array['option']))
            {
                $predefine_additional_charge_array['option'] = array_values($predefine_additional_charge_array['option']);
            }
            
        }
        // convert to json
        $predefine_additional_charge_array['other'] = array($request['additional_charges_title'] => number_format((float)$request['additional_charges'], 2, '.', ''));
    
        // array_push( $predefine_additional_charge_array, $actual_additional_charge );

        $request['additional_charges']= json_encode($predefine_additional_charge_array);
        
        $distance_charge=\App\ManageCharge::get()->where('status','Active')->first();
        $km_charge = 0;
        if(!empty($distance_charge))
        {
            $km_charge =$distance_charge->km_charge;
        }
        $request['km_distance'] = ($request['km_distance'] == "") ? 0 : number_format((float)$request['km_distance'], 2, '.', '');
        $request['km_charge'] = ($request['km_charge'] == "") ? number_format((float)$km_charge, 2, '.', '') : number_format((float)$request['km_charge'], 2, '.', '');

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

        //         $distance_charge=\App\ManageCharge::get()->where('status','Active')->first();
        //         $request['km_charge']=$distance_charge->km_charge;
        //         $total_amount+=($distance*$distance_charge->km_charge);
        //     }
        // }

        $total_amount+=$request['transportation_charge'];
        if($request['service_center_id'] != "")
        {
            $request['status'] ="Service center assigned";
        }
        if($request['technician_id'] != "")
        {
            $request['status'] ="Technician assigned";
        }
        
        $request['amount']=$total_amount;
        // calculate total amount work end
        
        if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){
            $request['created_by'] = auth()->user()->id;  
        }

        $service_request = ServiceRequest::create($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));
        SendMailHelper::sendRequestCreationMail($service_request->id);

        // SendMailHelper::sendRequestCreationMail(195);

        // service request log for new request
        $insertServiceRequestLogArr = array(
                                        'action_made'     =>   "Service request is created.",
                                        'action_made_company'     =>   "Service request is created.",
                                        'action_made_service_center'     =>   "Service request is created.", 
                                        'service_request_id'   =>   $service_request->id,
                                        'user_id'   =>   auth()->user()->id
                                    );
        $LastInsertedId = ServiceRequestLog::create($insertServiceRequestLogArr);

        if($request['status'] == "Service center assigned" || $request['status'] == "Technician assigned")
        {
            // service request log for assigned status
            $insertServiceRequestLogArr = array(
                                        'action_made'     =>   "Status is changed from New to ".$request['status'].".",
                                        'action_made_company'     =>   "Status is changed from New to ".$request['status'].".",
                                        'action_made_service_center'     =>   "Status is changed from New to ".$request['status'].".", 
                                        'service_request_id'   =>   $service_request->id,
                                        'user_id'   =>   auth()->user()->id
                                    );
            ServiceRequestLog::create($insertServiceRequestLogArr);
                                   
            
        }

        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')){

            if($request->technician_id != '' && $request->technician_id != 0 && !empty($request->technician_id) && $request->technician_id != NULL){
                $this->sendPushNotificationTechnician($request->technician_id,$service_request->id);
            }
        }  

        return redirect()->route('admin.service_requests.index')->with('success','Service Request created successfully!');
    }

    // public function serviceRequestMail(Type $var = null)
    // {
    //     SendMailHelper::sendRequestCreationMail($service_request->id);
    // }


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
        $companies = \App\Company::where('status','Active')
                                ->orderBy('name')
                                ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
        $service_centers = \App\ServiceCenter::where('status','Active')
                                            ->orderBy('name')
                                            ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
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

        $userDetail = '';
        if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){

            if($service_request->created_by != ''){
                $userDetail=\App\User::findOrFail($service_request->created_by);
            }
             
        }

        if($service_request['service_type'] == "repair")
        {
            if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                $enum_status = ServiceRequest::$enum_technician_repair_status;
            }
            else
            {
                $enum_status = ServiceRequest::$enum_repair_status;
            }
            
        }
        else
        {
            if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                $enum_status = ServiceRequest::$enum_technician_installation_status;
            }
            else
            {
                $enum_status = ServiceRequest::$enum_installation_status;
            }
        }
        $enum_status_color = ServiceRequest::$enum_status_color_code;

        $additional_charge_array=json_decode($service_request['additional_charges']);
        
        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');

        $additional_charge_title = [];
        $additional_charge = [];

        if(isset($additional_charge_array->option) && !empty($additional_charge_array->option)){
                
                
            foreach ($additional_charge_array->option as $OptionKey => $value) {
               
                $AdditionalChargeTitle =  key((array)$value);
               
                foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                   
                    if($AdditionalChargeTitle === $arr_val){
                        $additional_charge_title['option'][$OptionKey] = $PreArrayKey;
                        $additional_charge['option'][$OptionKey] = $value->$arr_val;
                    }
                }
            }
        }  
        
        if(isset($additional_charge_array->other) && !empty($additional_charge_array->other)){

            foreach ($additional_charge_array->other as $key => $value) {
                
                $additional_charge_title['other'] = str_replace('_empty_', '', $key);
                $additional_charge['other'] = $value;
            }                                      
        }
        // $additional_charge_title=[];
        // $additional_charges=[];
        // if(!empty($additional_charge_array))
        // {

        //     $keyVlaue = [];
        //     $AmountVlaue = [];
        //     if(isset($additional_charge_array->option)){
        //       foreach ($additional_charge_array->option as $key => $value) {

        //         $keyVlaue[$key] = key((array)$value);
        //         $Vlaue = $keyVlaue[$key];
        //         $AmountVlaue[] = $value->$Vlaue;

        //       } 
        //     }

        //     if(isset($additional_charge_array->other)){
        //       if (strpos(key((array)$additional_charge_array->other),'_empty_') === false) {
        //         array_push($keyVlaue,key((array)$additional_charge_array->other));
        //         $value = key((array)$additional_charge_array->other);
        //         $AmountVlaue[] = $additional_charge_array->other->$value;
        //       }
        //     }
        //     $additional_charge_title=$keyVlaue;
        //     $additional_charges=$AmountVlaue;

        //     // $keyVlaue = [];
        //     // foreach ($additional_charge_array as $key => $value) {
        //     //   $keyVlaue[$key] = key((array)$value);
        //     // }
        //     // // Worked to display json value in edit page
        //     // foreach ($additional_charge_array as $key => $value) {
        //     //     $key = $keyVlaue[$key];
        //     //     $additional_charge_title[]=str_replace('_empty_', '', $key);
        //     //     $additional_charges[]=$value->$key;
        //     // }
        // }
        
        // echo "<pre>";
        // print_r($additional_charge_title);
        // echo "</pre>";
        // exit();
        
        $service_request['additional_charges']=$additional_charge;

        $custAddressData = \App\Customer::where('id',$service_request['customer_id'])
                                        ->where('status','Active')
                                        ->get()->first();

        $supported_service_centers = \App\ServiceCenter::where('status','Active')->Where('supported_zipcode', 'like', '%' . $custAddressData->zipcode . '%')->get();

        $service_center_supported = true;
        if($service_request['service_center_id'] != "")
        {
            // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            $technicians = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                                    ->where('status','Active')
                                    ->where('service_center_id',$service_request['service_center_id'])
                                    ->orderBy('name')
                                    ->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

            // check service center supported to apply transportation charges
            $supported_center_detail=\App\ServiceCenter::Where('supported_zipcode', 'like', '%' . $custAddressData->zipcode . '%')->where('id',$service_request['service_center_id'])->get();


            if(count($supported_center_detail) <= 0)
            {
                $service_center_supported = false;
            }

        }
        else
        {
            $technicians=array(''=>trans('quickadmin.qa_please_select'));
        }
          
        $parts=array();
        $products=array(''=>trans('quickadmin.qa_please_select'));                             
        if($service_request['company_id'] != "")
        {
            $customers = \App\Customer::select("*", DB::raw('CONCAT(CONCAT(UCASE(LEFT(customers.firstname, 1)),SUBSTRING(customers.firstname, 2))," ",CONCAT(UCASE(LEFT(customers.lastname, 1)),SUBSTRING(customers.lastname, 2))) as firstname'))->where('company_id',$service_request['company_id'])
                                        ->where('status','Active')
                                        ->orderBy('firstname')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

            // $product_parts = \App\AssignPart::where('company_id',$service_request['company_id'])
            //                     ->with('product_parts')->get();
            $product_parts = \App\AssignPart::where('company_id',$service_request['company_id'])
                                ->whereHas('product_parts', function ($query) {
                                        $query->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product_parts->name;
                                });

            // $company_products = \App\AssignProduct::where('company_id',$service_request['company_id'])
            //                     ->with('product')->get();
            $company_products = \App\AssignProduct::where('company_id',$service_request['company_id'])
                                ->whereHas('product', function ($query) {
                                        $query->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product->name;
                                });


            // echo "<pre>"; print_r ($company_products); echo "</pre>"; exit();
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
                    $products[$value->product->id]=$value->product->name;
                    // foreach($value->product_id as $details)
                    // {
                    //     $products[$details->id]=$details->name;
                    // }
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

        $enum_company_status = \App\Company::$enum_status;
        $enum_customer_status = \App\Customer::$enum_status;
        $enum_service_center_status = \App\ServiceCenter::$enum_status;
        $enum_technician_status = \App\User::$enum_status;
        return view('admin.service_requests.edit', compact('service_request', 'enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName', 'service_request_logs', 'custAddressData','additional_charge_title','service_center_supported', 'supported_service_centers', 'enum_company_status', 'enum_customer_status', 'enum_service_center_status', 'enum_technician_status','enum_status_color','additional_charge_array','pre_additional_charge_array','userDetail'))->with('no', 1);
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
        
        if($request['service_center_id'] != "")
        {
            
            if($service_request->status == "New")
            {
                if($request['technician_id'] != "")
                {
                    $request['status']="Technician assigned";
                }
                else{
                    $request['status']="Service center assigned";
                }
                // service request log for assigned status
                // $insertServiceRequestLogArr = array(
                //                             'action_made'     =>   "Status is changed from New to ".$request['status'].".",
                //                             'action_made_company'     =>   "Status is changed from New to ".$request['status'].".",
                //                             'action_made_service_center'     =>   "Status is changed from New to ".$request['status'].".", 
                //                             'service_request_id'   =>   $id,
                //                             'user_id'   =>   auth()->user()->id
                //                         );
                // ServiceRequestLog::create($insertServiceRequestLogArr);
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
        else{
            $request['status']="New";
        }  
        if($request['technician_id'] != "")
        {
            // insert service request log on technician change 
            if($service_request->technician_id != $request['technician_id']){
                $request['is_accepted'] = 0;
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
            if($request['status'] == "Closed" && $service_request->is_accepted == 0)
            {
                // if techician is assigned, request is not accepted and any user close the request, is_accepted will be set to 1
                $request['is_accepted'] = 1;
            }
            else if ($request['status'] =="Service center assigned")
            {
                $request['status']="Technician assigned";
            }
            // if($request['status'] =="Service center assigned"){
            //         $request['status']="Technician assigned";
                
            // // service request log for assigned status
            //     $insertServiceRequestLogArr = array(
            //                                 'action_made'     =>   "Status is changed from Service center assigned to ".$request['status'].".",
            //                                 'action_made_company'     =>   "Status is changed from Service center assigned to ".$request['status'].".",
            //                                 'action_made_service_center'     =>   "Status is changed from Service center assigned to ".$request['status'].".", 
            //                                 'service_request_id'   =>   $id,
            //                                 'user_id'   =>   auth()->user()->id
            //                             );
            //     ServiceRequestLog::create($insertServiceRequestLogArr);
            // } 
        }
        else
        {
            $request['is_accepted'] = 0;
            if($request['status'] == "Technician assigned")
            {
                if($request['service_center_id'] != "")
                {
                    $request['status']="Service center assigned";
                }
                else
                {
                    $request['status']="New";
                }
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
        if($request['status'] == "Closed")
        {
            // calculate invoice number
            $max_invoice_number= ServiceRequest::max('invoice_number');
            $last_invoice_number=0;
            if(!empty($max_invoice_number))
            {
                $last_invoice_number = $max_invoice_number;
            }
            $request['invoice_number'] = str_pad(($last_invoice_number + 1), 4, '0', STR_PAD_LEFT);  

            $request['closed_at'] = date('Y-m-d H:i:s');
        }
        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+(($request['additional_charges'] == "")?0:number_format((float)$request['additional_charges'], 2, '.', ''));

        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');
        
        // $predefine_additional_charge_array = [];
        $actual_value = '';
        
        $predefine_additional_charge_array['option'] = [];
        if(isset($request['existingAdditional_charge_for']) && $request['existingAdditional_charge_for'] != ''){
        
            foreach ($request['existingAdditional_charge_for'] as $key => $existingAdditional_charge_for_Vlaue) {
               
                if($existingAdditional_charge_for_Vlaue != 0){
                   
                    $actual_value = ($pre_additional_charge_array[$existingAdditional_charge_for_Vlaue] == "")?0:number_format((float)$request['existingAdditional_charge'][$key], 2, '.', '');

                    $total_amount+=$actual_value;

                    $predefine_additional_charge_array['option'][$key] = array($pre_additional_charge_array[$existingAdditional_charge_for_Vlaue] => number_format((float)$actual_value, 2, '.', ''));
                  
                    
                }
            }
            $predefine_additional_charge_array['option'] = array_values($predefine_additional_charge_array['option']);
        }

        $predefine_additional_charge_array['other'] =(object)array();
        if(!empty($request['additional_charges_title']) && $request['additional_charges_title'] != ''){
            $predefine_additional_charge_array['other'] = array($request['additional_charges_title'] => number_format((float)$request['additional_charges'], 2, '.', ''));
        }

        $request['additional_charges']= json_encode($predefine_additional_charge_array);
        // convert to json
        // $request['additional_charges']= json_encode(array($request['additional_charges_title'] => number_format((float)$request['additional_charges'], 2, '.', '')));


        $distance_charge=\App\ManageCharge::get()->where('status','Active')->first();
        $km_charge = 0;
        if(!empty($distance_charge))
        {
            $km_charge =$distance_charge->km_charge;
        }
        $request['km_distance'] = ($request['km_distance'] == "") ? 0 : number_format((float)$request['km_distance'], 2, '.', '');
        $request['km_charge'] = ($request['km_charge'] == "") ? number_format((float)$km_charge, 2, '.', '') : number_format((float)$request['km_charge'], 2, '.', '');

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
        $existing_technician_id=$service_request->technician_id;

        if($request['call_type'] == "Warranty"){

            if($request['warranty_card_number'] != '' && $request['online_serial_number'] != ''){
                $request['warranty_card_number'] = $request['warranty_card_number'];
                $request['online_serial_number'] = $request['online_serial_number'];
            }
        }else{
            $request['warranty_card_number'] = '';
            $request['online_serial_number'] = '';
        }  
        
        $service_request->update($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));

        if($request_status != $request['status'])
        {
            //send mail on every status change
            $msg='Status is changed from '.$request_status.' to '.$request['status'].'.';
            // echo $id;exit;
            SendMailHelper::sendRequestUpdateMail($id,$msg);
        }
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')){

            if($request->technician_id != '' && $request->technician_id != 0 && !empty($request->technician_id) && $request->technician_id != NULL && $request->technician_id != $existing_technician_id){
                $this->sendPushNotificationTechnician($request->technician_id,$id);
            }

        }
        if($request['status'] == "Closed" && (auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')))
        {
            // allow only admin and super admin to generate PDF
            // return $this->createReceiptPDF($id);
            return redirect()->route('admin.service_request.invoice',[$id]);
        }
        else
        {
            return redirect()->route('admin.service_requests.index')->with('success','Service Request updated successfully!');
        }
        // return redirect()->route('admin.service_requests.index');
    }
    public function acceptServiceRequest($id)
    {
        
        $request = ServiceRequest::find($id);
        
        if($request) {
            $technician_name = $request->technician->name;
            $request->is_accepted = 1;
            $request->save();

            SendMailHelper::sendRequestAcceptRejectMail($id,$technician_name);
        }
        
        return redirect()->route('admin.service_requests.index');
    }
    public function rejectServiceRequest($id)
    {
        // SendMailHelper::sendRequestAcceptRejectMail($id);
        $request = ServiceRequest::find($id);

        if($request) {
            $technician_name = $request->technician->name;
            $request->technician_id = NULL;
            $request->status = 'Service center assigned';
            $request->save();

            SendMailHelper::sendRequestAcceptRejectMail($id,$technician_name);
        }
        return redirect()->route('admin.service_requests.index');
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
       
        $userDetail = '';
        if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){

            if($service_request->created_by != ''){
                $userDetail=\App\User::findOrFail($service_request->created_by);
            }
             
        }
        
        $additional_charge_array=json_decode($service_request['additional_charges']);

        $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');

        // $additional_charge_title = [];
        // $additional_charges = [];

        $additional_charge_title['option'] = [];
        $additional_charges['option'] = [];

        $additional_charge_title['other'] = [];
        $additional_charges['other'] = [];

        if(!empty($additional_charge_array->option)){
            foreach ($additional_charge_array->option as $OptionKey => $value) {
                
                $AdditionalChargeTitle =  key((array)$value);
                foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                    if($AdditionalChargeTitle === $arr_val){

                        $additional_charge_title['option'][$OptionKey] = $AdditionalChargeTitle;
                        $additional_charges['option'][$OptionKey] = $value->$arr_val;
                    
                    }
                }
            }
        }  
        if(!empty($additional_charge_array->other)){
            foreach ($additional_charge_array->other as $key => $value) {
               
                $additional_charge_title['other'] = str_replace('_empty_', '', $key);
                $additional_charges['other'] = $value;
                
            }                                      
        }
        // echo "<pre>";
        // print_r($additional_charge_title);
        // echo "</pre>";
        // exit();
        
        // $additional_charge_title=[];
        // $additional_charges=[];
        // if(!empty($additional_charge_array))
        // {
        //     foreach ($additional_charge_array->option as $additionalChargeKey => $value) {

        //         $ActualAdditionalChargeFor =  key((array)$value);
              
        //         foreach($pre_additional_charge_array as $key => $arr_val){
                    
        //             if($arr_val === $ActualAdditionalChargeFor){
        //                 $additional_charge_title[] = $arr_val;
        //                 if(count($value) > 0){
        //                     $additional_charges[] = $value->$ActualAdditionalChargeFor;
        //                 }
        //             }
        //         }
        //     }
        //     if(!empty($additional_charge_array->other)){
        //         foreach ($additional_charge_array->other as $key => $value) {
        //             $additional_charge_title[] = str_replace('_empty_', '', $key);
        //             $additional_charges[] = $value;
                
        //         }                                      
        //     }
           
            // $keyVlaue = [];
            // $AmountVlaue = [];
            // if(isset($additional_charge_array->option)){
            //   foreach ($additional_charge_array->option as $key => $value) {
            //     $keyVlaue[$key] = key((array)$value);
               
            //     $Vlaue = $keyVlaue[$key];
            //     if($Vlaue != 0){

            //         $AmountVlaue[] = $value->$Vlaue;
            //     }
            //     echo "<pre>";
            //     print_r($AmountVlaue);
            //     echo "</pre>";
            //     exit();
                
            //   } 
            // }

            // if(isset($additional_charge_array->other)){
            //   if (strpos(key((array)$additional_charge_array->other),'_empty_') === false) {
            //     array_push($keyVlaue,key((array)$additional_charge_array->other));
            //     $value = key((array)$additional_charge_array->other);
            //     $AmountVlaue[] = $additional_charge_array->other->$value;
            //   }
            // }
            // $additional_charge_title=$keyVlaue;
            // $additional_charges=$AmountVlaue;
            // echo "<pre>";
            // print_r($additional_charges);
            // echo "</pre>";
            // exit();

            // $keyVlaue = [];
            // foreach ($additional_charge_array as $key => $value) {
            //     $keyVlaue[$key] = key((array)$value);
            // }
            // // Worked to display json value in edit page
            // foreach ($additional_charge_array as $key => $value) {
            //     $key = $keyVlaue[$key];
            //     $additional_charge_title[]=str_replace('_empty_', '', $key);
            //     $additional_charges[]=$value->$key;
            // }
        // }

        $enum_status_color = ServiceRequest::$enum_status_color_code;
        $service_request['additional_charges']=$additional_charges;

        $service_request_logs = $service_request->servicerequestlog;
        return view('admin.service_requests.show', compact('service_request', 'service_request_logs','additional_charge_title','enum_status_color','userDetail'))->with('no', 1);
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
            // if($request['km_distance'] != "" && $request['km_distance'] != 0)
            // {
                // $km_charge=($request['km_charge'] != "" && $request['km_charge'] != 0)? "<tr><td colspan='2'>Transportation Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['km_charge'] * $request['km_distance'],2)."<br/>(".number_format($request['km_charge'],2)." rs per km)</td></tr>":"";
            // } 
            $km_charge=($request['transportation_charge'] != "" && $request['transportation_charge'] != 0)? "<tr><td colspan='2'>Transportation Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['transportation_charge'],2)."<br/>(".number_format($request['km_charge'],2)." rs per km)</td></tr>":"";   
            

            // $additional_charges=($request['additional_charges'] != "" && $request['additional_charges'] != 0)? "<tr><td colspan='2'>Additional Charge </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['additional_charges'],2)."</td></tr>":"";
            $additional_charge_array=json_decode($request['additional_charges']);

            $pre_additional_charge_array = config('constants.PRE_ADDITIONAL_CHARGES_FOR');
            $additional_charges = '';
            
            if(!empty($additional_charge_array->option)){
                foreach ($additional_charge_array->option as $OptionKey => $value) {
                    
                    $AdditionalChargeTitle =  key((array)$value);
                    foreach($pre_additional_charge_array as $PreArrayKey => $arr_val){
                        if($AdditionalChargeTitle === $arr_val){

                            $additional_charges.= "<tr><td colspan='2'>".$AdditionalChargeTitle." </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($value->$arr_val,2)."</td></tr>";

                        }
                    }
                }
            }
            if(!empty($additional_charge_array->other)){
                foreach ($additional_charge_array->other as $key => $value) {
                    if(str_replace('_empty_', '', $key) != "" && $value > 0) 
                    {
                        $additional_charges.= "<tr><td colspan='2'>".str_replace('_empty_', '', $key)." </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($value,2)."</td></tr>";
                    }

                }                                      
            }
            // $additional_charges="";
            // if(!empty($additional_charge_array))
            // {
            //     // Worked to display json value in edit page
            //     foreach ($additional_charge_array as $key => $value) {

            //         $additional_charge_title=str_replace('_empty_', '', $key);
            //         if(!empty($additional_charge_title) && !empty($value))
            //         {
            //             $additional_charges="<tr><td colspan='2'>".$additional_charge_title." </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($value,2)."</td></tr>";
            //         }
                    
            //     }
            // }

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
                                        <th class='align-text-center'>Product</th>
                                        <th class='align-text-center'>&nbsp;</th>
                                        <th class='price'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class='align-text-center'>".$productDetail->name."</td>
                                        <td class='align-text-center'></td>
                                        <td class='price'></td>
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
                            .price{
                                text-align:right;
                            }
                            .align-text-center{
                                text-align:center;
                            }
                        </style>
                    </head>";
            $html.="<body>
                    <h1 style='text-align:center;'>Bill Receipt</h1>";

                    if(!empty($request['invoice_number']))
                    {
                        $html.="<h5 style='text-align:center;'>Invoice Number : ".$request['invoice_number']."</h5>";
                    }
                    
            
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
            if($details['productId'] && $details['companyId'])
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
                                ->where('status','Active')
                                ->orderby('firstname')
                                // ->orderby('id','DESC')
                                ->get();

            $product_parts = \App\AssignPart::where('company_id',$details['companyId'])
                                ->whereHas('product_parts', function ($query) {
                                        $query->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product_parts->name;
                                });

            $products = \App\AssignProduct::where('company_id',$details['companyId'])
                                ->whereHas('product', function ($query) {
                                        $query->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product->name;
                                });

            if(count($customers) > 0)
            {
                foreach($customers as $key => $value)
                {   
                    // $selected = '';
                    // if($key == 0){
                    //     $selected = 'selected';
                    // }

                    $data['custOptions'].="<option value='".$value->id."'>". ucfirst($value->firstname).' '.ucfirst($value->lastname)."</option>";   
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
                    // if(!empty($value->product_id)){
                    //     foreach($value->product_id as $details)
                    //     {
                            $data['productOptions'].="<option value='".$value->product->id."'>".$value->product->name."</option>";   
                    //     }
                    // }
                }   
            }else{
                echo json_encode(array('no_products' => 1));
                exit;
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

            $distance_charge=\App\ManageCharge::get()->where('status','Active')->first();
            $km_charge = 0;
            if(!empty($distance_charge))
            {
                $km_charge =$distance_charge->km_charge;
            }
            $data['km_charge']=$km_charge;

            if(count($supportedCenterDetail) <= 0)// && $customerDetail->zipcode != $centerDetail->zipcode)
            {
                // calculate transportation charges for unsupported zipcode
                $customer_latitude=$customerDetail->location_latitude;
                $customer_longitude=$customerDetail->location_longitude;

                $center_latitude=$centerDetail->location_latitude;
                $center_longitude=$centerDetail->location_longitude;
                
                $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

                $data['km_distance']=$distance;

                
                $data['transportation_amount']=($distance*$km_charge);
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
            if(!empty($customer))
            {
                $data['service_centers'] = \App\ServiceCenter::where('status','Active')->Where('supported_zipcode', 'like', '%' . $customer->zipcode . '%')->get();
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
            if(!empty($customer))
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
            // $query = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                        // ->orderby('id','DESC');

            $query->where('service_center_id',$details['serviceCenterId']);
            $query->where('status','Active');
            
            $technicians = $query->get();
            if(count($technicians) > 0)
            {
                foreach($technicians as $key => $value)
                {
                    // $selected = '';
                    // if($key == 0){
                    //     $selected = 'selected';
                    // }
                    $data['options'].="<option value='".$value->id."'>".$value->name."</option>";
                    
                }
                
            }
        }
        echo json_encode($data);
        exit;
    
    }
    public function clearRequestFilterAjax(Request $request)
    {
        // clear service request list filter dropdown
        $request->session()->forget(['filter_company', 'filter_customer', 'filter_product', 'filter_service_center', 'filter_technician','filter_request_status','filter_request_type']);
        
        return redirect()->route('admin.service_requests.index');
    }
    public function getFilterCompanyDetails(Request $request)
    {
        
        // ajx function to get customers, product and parts of particular company
        
        $details=$request->all();

        $data['custOptions']="<option value=''>".trans('quickadmin.qa_show_all')."</option>";
        $data['partOptions']="";
        $data['productOptions']="<option value=''>".trans('quickadmin.qa_show_all')."</option>";

        if($details['companyId'] != "")
        {
            $customers = \App\Customer::where('company_id',$details['companyId'])
                                ->where('status','Active')->orderBy('firstname')->get();

            // $product_parts = \App\AssignPart::where('company_id',$details['companyId'])
            //                     ->whereHas('product_parts', function ($query) {
            //                             $query->orderBy('name');
            //                             $query->where('status', 'Active');
            //                     })->get()
            //                     ->sortBy(function($value, $key) {
            //                       return $value->product_parts->name;
            //                     });

            $products = \App\AssignProduct::where('company_id',$details['companyId'])
                                ->whereHas('product', function ($query) {
                                        $query->orderBy('name');
                                        $query->where('status', 'Active');
                                })->get()
                                ->sortBy(function($value, $key) {
                                  return $value->product->name;
                                });
            // echo count($product_parts);
            //             echo "<pre>"; print_r ($product_parts); echo "</pre>"; exit();        
            if(count($customers) > 0)
            {
                foreach($customers as $key => $value)
                {
                    $data['custOptions'].="<option value='".$value->id."'>". ucfirst($value->firstname).' '.ucfirst($value->lastname)."</option>";
                }   
            }
            // if(count($product_parts) > 0)
            // {
            //     foreach($product_parts as $key => $value)
            //     {
            //         $data['partOptions'].="<option value='".$value->product_parts->id."'>".$value->product_parts->name."</option>";   
                    
            //     }   
            // }
            if(count($products) > 0)
            {
                foreach($products as $key => $value)
                {
                    // echo "<pre>"; print_r ($value->product_id); echo "</pre>"; exit();
                    $data['productOptions'].="<option value='".$value->product->id."'>".ucfirst($value->product->name)."</option>";
                }   
            }
        }
        echo json_encode($data);
        exit;
    }

    public function getFilterTechnicians(Request $request)
    {
        
        // ajx function to get technicians of particular service center
        
        $details=$request->all();
        $data['options']="<option value=''>".trans('quickadmin.qa_show_all')."</option>";
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;
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
            // calculate total paid and due service request
            $data['paid_amount'] = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',$details['serviceCenterId'])->where('is_paid','1')->sum('amount');

            $data['due_amount'] = ServiceRequest::select('id','amount')->where('status','Closed')->where('service_center_id',$details['serviceCenterId'])->where('is_paid','0')->sum('amount');
        }


        echo json_encode($data);
        exit;
    
    }

    public function amountPaid( Request $request )
    {
        $requestDetail = ServiceRequest::find($request->serviceRequestId);
        $requestDetail->is_paid = 1;
        $status = $requestDetail->save();
        if($status == 1){
            return 1;
        }else{
            return 0;
        }

    }

    public function quickadd( Request $request )
    {   
        $status = 0;
        $returnHTML = '';

        if($request['type'] != ''){
            
            if($request['type'] == 'company'){

                // $enum_company_status = \App\Company::$enum_status;
                // $returnHTML = view('admin.companies.content')->with('enum_company_status', $enum_company_status)->render();
                $returnHTML = view('admin.companies.content')->render();
                $status = 1;

            }elseif ($request['type'] == 'customer') {

                $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

                // $enum_customer_status = \App\Customer::$enum_status;
                // $returnHTML = view('admin.customers.content',compact('enum_customer_status', 'companies'))->render();

                $returnHTML = view('admin.customers.content',compact('companies'))->render();
                $status = 1;

            }elseif ($request['type'] == 'service_center') {

                // $enum_service_center_status = \App\ServiceCenter::$enum_status;
                // $returnHTML = view('admin.service_centers.content')->with('enum_service_center_status', $enum_service_center_status)->render();
                $returnHTML = view('admin.service_centers.content')->render();
                $status = 1;

            }elseif ($request['type'] == 'technician') {

                // $enum_technician_status = \App\User::$enum_status;
                $service_centers = \App\ServiceCenter::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
                // $returnHTML = view('admin.technicians.content',compact('enum_technician_status', 'service_centers'))->render();
                $returnHTML = view('admin.technicians.content',compact('service_centers'))->render();
                $status = 1;

            }
            
            // $returnHTML = view('job.userjobs')->with('userjobs', $userjobs)->render();
        }
        return response()->json(array('success' => $status , 'html'=>$returnHTML));
    }

    public function ajaxAssignProducts(Request $request)
    {
        $status = 0;
        $returnHTML = '';
        $company_id = '';
        if($request['company_id'] != '' && $request['company_id'] != 0){

            $company_id = $request['company_id'];

            $companies = \App\Company::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

            $product_ids = \App\Product::where('status','Active')->orderBy('name')->get()->pluck('name', 'id');

            $returnHTML = view('admin.assign_products.content',compact('companies', 'product_ids'))->render();
            $status = 1;
        }
        return response()->json(array('success' => $status , 'html'=>$returnHTML, 'company_id' => $company_id));
    }

    public function sendPushNotificationTechnician($technicianId,$lastInsertedId)
    {
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')){

            if($technicianId != '' && $technicianId != 0 && !empty($technicianId) && $technicianId != NULL){

                $assignedRequest = ServiceRequest::select('service_requests.id','service_requests.service_type',
                    'service_requests.created_at','service_requests.customer_id','service_requests.amount','service_requests.completion_date',DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)),LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'),'service_requests.status','service_requests.is_accepted'
                )
                ->where('service_requests.technician_id',$technicianId)
                ->where('service_requests.id',$lastInsertedId)
                ->join('customers','service_requests.customer_id','=','customers.id')
                ->join('products','service_requests.product_id','=','products.id')
                ->first();

                $device_token = \App\User::select('firebase_token')
                ->where('role_id',config('constants.TECHNICIAN_ROLE_ID'))
                ->where('status','Active')
                ->where('id',$technicianId)
                ->first();

                // $device_token = "dBJg3r2jgb8:APA91bEaSNTeUdwETnXr-xjXkqNKhiLhA16xwh-5Uw0JDLPfGdQWK18HQG1aYYJ9FSpaHFQysSb4rtkMCh3WV67LoJMXPQAgAKbkFrt91fXkBg_qGAIbH-sr9_TNI-O3bSe0CWATfZLZ";
               
                
                $apiKey = "AAAAkwYtQh8:APA91bF0WWqlkV15KYPpr6zd0-d0d6CsApLji6MKGpxyzhXOtQRCJDPrukQhS_S_DHjHH0sWhsUDujUVv8aBgWL2MyCbh8TrQX4VqYTgi6PQ_0JWipAdh2w8Jni4w9C23dR7wSDVa8mD";
                // FCM API KEY

                $message = array("message" => $assignedRequest);
                // $message = array("message" => "test message from push notification");
                
                $registrationIDs = array($device_token->firebase_token);
                // $registrationIDs = array($device_token);

                $url = 'https://fcm.googleapis.com/fcm/send';

                $fields = array(
                    'registration_ids' => $registrationIDs,
                    'data' => $message,
                    "time_to_live" => 300000,
                    // "delay_while_idle" => false,
                    'priority' => 'high',
                );

                $headers = array(
                    'Authorization: key=' . $apiKey,
                   'Content-Type: application/json'
                );

                // Open connection
                $ch = curl_init();

                // Set the url, number of POST vars, POST data
                curl_setopt( $ch, CURLOPT_URL, $url );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

                // Execute post
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    Log::error('Oops! FCM Send Error: ' . curl_error($ch));
                    // die('Oops! FCM Send Error: ' . curl_error($ch));
                }

                // echo "----".$result;
                // echo "<br>".json_encode( $fields );
                // Close connection
		        Log::info("== sending notification ==".json_encode( $fields ));
		        Log::info("== sent notification ==".json_encode( $result ));
                curl_close($ch);
                // exit();
            }
        }
    }
}
