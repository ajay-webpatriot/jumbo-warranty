<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\ServiceRequest;
use App\Company;
use DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
         if((auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.ADMIN_ROLE_ID')) && (auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))){
            return view('home');
        }
       
        $PendingComplainCount       = 0;
        $SolvedComplainCount        = 0;
        $PendingInstallationCount   = 0;
        $SolvedInstallationCount    = 0;

        // $ServiceTypes = ServiceRequest::select('service_type','status')
        // ->whereIn('service_type',array('repair','installation'))
        // ->get();
        $serviceTypesQuery = ServiceRequest::select('service_type','status')
        ->whereIn('service_type',array('repair','installation'));
        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $serviceTypesQuery->Where('service_requests.company_id', auth()->user()->company_id);
        }
        $ServiceTypes = $serviceTypesQuery->get();

        $enum_status_color = ServiceRequest::$enum_status_color_code;
        
        foreach ($ServiceTypes as $key => $SingleServiceTypes) {

            if($SingleServiceTypes->service_type == 'repair' && $SingleServiceTypes->status != 'Closed'){

                /*Total panding complain (Repair) */
                $PendingComplainCount++;

            }else if($SingleServiceTypes->service_type == 'repair' && $SingleServiceTypes->status == 'Closed'){

                /*Total solved complain (Repair) */
                $SolvedComplainCount++;

            }else if($SingleServiceTypes->service_type == 'installation' && $SingleServiceTypes->status != 'Closed'){

                /*Total panding installation */
                $PendingInstallationCount++;

            }else if($SingleServiceTypes->service_type == 'installation' && $SingleServiceTypes->status == 'Closed'){

                /*Total solved installation */
                $SolvedInstallationCount++;

            }
        }
         
        $ServiceTypeDetailsQuery = ServiceRequest::select('service_requests.status','service_requests.amount','service_requests.service_type','service_requests.id','service_requests.created_at',DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)), 
        LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'))
        ->join('customers','service_requests.customer_id','=','customers.id')
        ->join('products','service_requests.product_id','=','products.id')
        ->whereIn('service_requests.service_type',array('repair','installation'))
        ->orderBy('service_requests.created_at','DESC')
        ->limit(10);

        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
             $ServiceTypeDetailsQuery->Where('service_requests.company_id', auth()->user()->company_id);
        }

        $ServiceTypeDetails = $ServiceTypeDetailsQuery->get();

        if($request->ajax())
        {
            $startDate          = $request->startDate;
            $endDate            = $request->endDate;
            $SelectedCompanyId  = $request->SelectedCompanyId;

            /*Total Installation count*/
            $installationToday = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"installation_today");

            /*Total Repair count*/
            $repairToday       = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"repair_today");
            
            /*Total Delayed count*/
            $delayedRequest    = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"delayed_request");

            /*Total Closed count*/
            $closededRequest   = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"closed_request");
            
            return response()->json([
                'installationToday' => $installationToday,
                'repairToday'       => $repairToday,
                'delayedRequest'    => $delayedRequest,
                'closededRequest'   => $closededRequest
            ]);
            exit();
        }

        $CompaninesName = Company::select('companies.name as CompanyName','companies.status as CompanyStatus','companies.id as CompanyId')
        ->where('deleted_at',NULL)
        ->where('status','=','Active')->get();
       
        return view('admin.admin_dashboard',compact('PendingComplainCount','SolvedComplainCount','PendingInstallationCount','SolvedInstallationCount','ServiceTypeDetails','CompaninesName','enum_status_color'));
    }

    public function getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,$type)
    {

        $todayDate = date('Y-m-d');
        $startDate = date('Y-m-d',strtotime($startDate));
        $endDate = date('Y-m-d',strtotime($endDate));
        
        $ServiceCount = ServiceRequest::select('service_requests.service_type','service_requests.status');

        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $ServiceCount->Where('service_requests.company_id', auth()->user()->company_id);
        }else{
            if($SelectedCompanyId != 'all'){
                    $ServiceCount->where('service_requests.company_id',$SelectedCompanyId);
            }
        }

        if($type == "installation_today"){
            $ServiceCount->where('service_requests.service_type','=','installation')
            ->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$startDate."' AND '".$endDate."'");

            /*Total Installation count*/
            return $ServiceCount->count();

        }elseif ($type == "repair_today") {
            $ServiceCount->where('service_requests.service_type','=','repair')
            ->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$startDate."' AND '".$endDate."'");
             
            /*Total Repair count*/
            return $ServiceCount->count();

        }elseif ($type == "delayed_request") {
            $ServiceCount->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.completion_date, '%Y-%m-%d') < '".$todayDate."'");
            
            /*Total Delayed count*/
            return $ServiceCount->count();

        }elseif ($type == "closed_request") {
            $ServiceCount->where('service_requests.status','=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.closed_at, '%Y-%m-%d') = '".$todayDate."'");
             
            /*Total Closed count*/
            return $ServiceCount->count();

        }
    }

    public function getCompanyDashboardDataByType(Request $request)
    {
        
        
        $todayDate = date('Y-m-d');
        $startDate = date('Y-m-d',strtotime($request->formData[1]['value']));
        $endDate = date('Y-m-d',strtotime($request->formData[2]['value']));
        $type = $request->formData[4]['value'];
        $companyId = $request->formData[3]['value'];
        $typeTitle = '';
        $dataByType = (object)array();
        $Status = 0;
        $color = '';
       
        $ServiceCount = ServiceRequest::select('service_requests.*',DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)), 
        LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'));

        if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        {
            $ServiceCount->Where('service_requests.company_id', auth()->user()->company_id);
        }else{
            if($companyId != 'all'){
                    $ServiceCount->where('service_requests.company_id',$companyId);
            }
        }
        
       
        if($type == "installation_today"){

            $typeTitle = 'TOTAL INSTALLATION REQUESTS';
            $color = 'info';

            $ServiceCount->where('service_requests.service_type','=','installation')
            ->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$startDate."' AND '".$endDate."'");

            /*Total Installation count*/
            // $dataByType = $ServiceCount->get();

            // return $ServiceCount->count();

        }elseif ($type == "repair_today") {

            $typeTitle = 'TOTAL SERVICE REQUESTS';
            $color = 'danger';

            $ServiceCount->where('service_requests.service_type','=','repair')
            ->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$startDate."' AND '".$endDate."'");
            
            /*Total Repair count*/
            // $dataByType = $ServiceCount->get();

        }elseif ($type == "delayed_request") {
            
            $typeTitle = 'TOTAL DELAYED REQUESTS FROM TODAY';
            $color = 'success';

            $ServiceCount->where('service_requests.status','!=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.completion_date, '%Y-%m-%d') < '".$todayDate."'");

            /*Total Delayed count*/
            // $dataByType = $ServiceCount->get();

        }elseif ($type == "closed_request") {

            $typeTitle = 'TOTAL REQUESTS CLOSED BY TODAY';
            $color = 'primary';

            $ServiceCount->where('service_requests.status','=','Closed')
            ->whereRaw("DATE_FORMAT(service_requests.closed_at, '%Y-%m-%d') = '".$todayDate."'");
             
            /*Total Closed count*/
            
        }
        $ServiceCount->join('customers','service_requests.customer_id','=','customers.id')
        ->join('products','service_requests.product_id','=','products.id')
        ->whereIn('service_requests.service_type',array('repair','installation'))
        ->orderBy('service_requests.created_at','DESC');
        
        // if($request->ajax()){

        //     $ServiceCount = ServiceRequest::select('service_requests.*',DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)),LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'));

        //     if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        //     {
        //         $ServiceCount->Where('service_requests.company_id', auth()->user()->company_id);
        //     }else{
        //         if($request->companyId != 'all'){
        //                 $ServiceCount->where('service_requests.company_id',$request->companyId);
        //         }
        //     }
            
       
        //     if($type == "installation_today"){

        //         $ServiceCount->where('service_requests.service_type','=','installation')
        //         ->where('service_requests.status','!=','Closed')
        //         ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$request->startDate."' AND '".$request->endDate."'");

        //     }elseif ($type == "repair_today") {
        //         $ServiceCount->where('service_requests.service_type','=','repair')
        //         ->where('service_requests.status','!=','Closed')
        //         ->whereRaw("DATE_FORMAT(service_requests.created_at, '%Y-%m-%d') BETWEEN '".$request->startDate."' AND '".$request->endDate."'");

        //     }elseif ($type == "delayed_request") {

        //         $ServiceCount->where('service_requests.status','!=','Closed')
        //         ->whereRaw("DATE_FORMAT(service_requests.completion_date, '%Y-%m-%d') < '".$request->todayDate."'");

        //     }elseif ($type == "closed_request") {

        //         $ServiceCount->where('service_requests.status','=','Closed')
        //         ->whereRaw("DATE_FORMAT(service_requests.closed_at, '%Y-%m-%d') = '".$request->todayDate."'");
        //     }
        //     $ServiceCount->join('customers','service_requests.customer_id','=','customers.id')
        //     ->join('products','service_requests.product_id','=','products.id')
        //     ->whereIn('service_requests.service_type',array('repair','installation'))
        //     ->orderBy('service_requests.created_at','DESC');

        //     $ServiceCount->limit(10)->offset($request->limitStart);

        // }else{
            // $ServiceCount->limit(10);
        // }
        $dataByType = $ServiceCount->get();

        $enum_status_color = ServiceRequest::$enum_status_color_code;

        // if($request->ajax()){
        //     return response()->json(['dataByType' => $dataByType,
        //     'enum_status_color' => $enum_status_color]);
        // }else{
        //     $ServiceCount->limit(10);
        // }

        
        $returnHTML = view('admin.request_list',compact('dataByType','enum_status_color','typeTitle','type','companyId','startDate','endDate','todayDate','color'))->render();

        return response()->json(array('success' => true, 'html'=>$returnHTML));

        // return view('admin.request_list',compact('dataByType','enum_status_color','typeTitle','type','companyId','startDate','endDate','todayDate','color'));
        
    }

    // public function getCompanyDashboardRequestCount()
    // {
    //     $GetFilterValue = Input::all();

    //     $startDate          = $GetFilterValue['startDate'];
    //     $endDate            = $GetFilterValue['endDate'];
    //     $SelectedCompanyId  = $GetFilterValue['SelectedCompanyId'];

    //     /*Total Installation count*/
    //     $installationToday = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"installation_today");

    //     /*Total Repair count*/
    //     $repairToday       = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"repair_today");
        
    //     /*Total Delayed count*/
    //     $delayedRequest    = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"delayed_request");

    //     /*Total Closed count*/
    //     $closededRequest   = $this->getCompanyDashboardData($startDate,$endDate,$SelectedCompanyId,"closed_request");
        
    //     return response()->json([
    //         'installationToday' => $installationToday,
    //         'repairToday'       => $repairToday,
    //         'delayedRequest'    => $delayedRequest,
    //         'closededRequest'   => $closededRequest
    //     ]);
    // }
}
?>