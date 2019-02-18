<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;// DB library
/**
 * Class ServiceRequest
 *
 * @package App
 * @property string $company
 * @property string $customer
 * @property enum $service_type
 * @property string $service_center
 * @property string $technician
 * @property enum $call_type
 * @property enum $call_location
 * @property enum $priority
 * @property string $product
 * @property string $make
 * @property string $model_no
 * @property enum $is_item_in_warrenty
 * @property string $bill_no
 * @property string $bill_date
 * @property string $serial_no
 * @property enum $mop
 * @property string $purchase_from
 * @property string $adavance_amount
 * @property string $service_charge
 * @property string $service_tag
 * @property text $complain_details
 * @property string $note
 * @property string $completion_date
 * @property decimal $additional_charges
 * @property decimal $amount
 * @property enum $status
*/
class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = ['service_type', 'call_type', 'call_location', 'priority', 'make', 'model_no', 'is_item_in_warrenty', 'bill_no', 'bill_date', 'serial_no', 'mop', 'purchase_from', 'adavance_amount', 'service_charge', 'complain_details', 'note', 'completion_date', 'additional_charges', 'amount', 'status', 'company_id', 'customer_id', 'service_center_id', 'technician_id', 'product_id','installation_charge','km_charge','km_distance','transportation_charge'];
    protected $hidden = [];
    
    

    public static $enum_service_type = ["installation" => "Installation", "repair" => "Repair"];

    public static $enum_call_type = ["AMC" => "AMC", "Chargeable" => "Chargeable", "FOC" => "FOC", "Warranty" => "Warranty"];

    public static $enum_call_location = ["On site" => "On site", "In House" => "In House"];

    public static $enum_priority = ["HIGH" => "HIGH", "LOW" => "LOW", "MEDIUM" => "MEDIUM", "MODERATE" => "MODERATE"];

    public static $enum_is_item_in_warrenty = ["Yes" => "Yes", "No" => "No"];

    public static $enum_mop = ["Cash" => "Cash", "Bank" => "Bank", "Online" => "Online", "Credit / Debit Card" => "Credit / Debit Card"];

    public static $enum_status = ["Started" => "Started", "Pending for parts" => "Pending for parts", "Cancelled" => "Cancelled", "Transferred to inhouse" => "Transferred to inhouse", "Under testing" => "Under testing", "Issue for replacement" => "Issue for replacement", "Closed" => "Closed"];

    public static $enum_installation_status = ["Started" => "Started", "Closed" => "Closed"];

    public static $enum_repair_status = ["Started" => "Started", "Pending for parts" => "Pending for parts", "Cancelled" => "Cancelled", "Transferred to inhouse" => "Transferred to inhouse", "Under testing" => "Under testing", "Issue for replacement" => "Issue for replacement", "Closed" => "Closed"];

    public function getServiceRequestParts($partsIds)
    {
        // get parts name of service request
        return $parts = DB::table('product_parts')
            ->select(DB::raw('group_concat(product_parts.name SEPARATOR ", ") as name'))
            ->join('product_part_service_request', 'product_parts.id', '=', 'product_part_service_request.product_part_id')
            ->whereIn('product_parts.id',$partsIds)
            ->groupBy('product_part_service_request.service_request_id')
            ->get()->first();
    }
    /**
     * Set to null if empty
     * @param $input
     */
    public function setCompanyIdAttribute($input)
    {
        $this->attributes['company_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCustomerIdAttribute($input)
    {
        $this->attributes['customer_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setServiceCenterIdAttribute($input)
    {
        $this->attributes['service_center_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setTechnicianIdAttribute($input)
    {
        $this->attributes['technician_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setProductIdAttribute($input)
    {
        $this->attributes['product_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setCompletionDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['completion_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['completion_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getCompletionDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAdditionalChargesAttribute($input)
    {
        $this->attributes['additional_charges'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {  
        $this->attributes['amount'] = $input ? $input : null;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    public function service_center()
    {
        return $this->belongsTo(ServiceCenter::class, 'service_center_id')->withTrashed();
    }
    
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
    
    public function parts()
    {
        return $this->belongsToMany(ProductPart::class, 'product_part_service_request')->withTrashed();
    }

    public function servicerequestlog()
    {
        return $this->hasMany(ServiceRequestLog::class,'service_request_id');
    }
    
    public function getFilterRequestsCount($request)
    {
        // get total filter request list count
        $service_requestsQuery = ServiceRequest::select('customers.firstname','service_centers.name as sname','products.name as pname','service_requests.amount','service_requests.service_type','service_requests.status','companies.name as cname','service_requests.id')
            ->leftjoin('companies','service_requests.company_id','=','companies.id')
            ->leftjoin('roles','service_requests.technician_id','=','roles.id')
            ->leftjoin('customers','service_requests.customer_id','=','customers.id')
            ->leftjoin('products','service_requests.product_id','=','products.id')
            ->leftjoin('service_centers','service_requests.service_center_id','=','service_centers.id');
            
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

            // filter data from table
            if(!empty($request['company']))
            {   
                 $service_requestsQuery->Where('service_requests.company_id', $request['company']);
            }
            if(!empty($request['customer']))
            {   
                 $service_requestsQuery->Where('service_requests.customer_id', $request['customer']);
            }
            if(!empty($request['product']))
            {   
                 $service_requestsQuery->Where('service_requests.product_id', $request['product']);
            }
            if(!empty($request['serviceCenter']))
            {   
                 $service_requestsQuery->Where('service_requests.service_center_id', $request['serviceCenter']);
            }
            if(!empty($request['technician']))
            {   
                 $service_requestsQuery->Where('service_requests.technician_id', $request['technician']);
            }

            //Search from table
            if(!empty($request['search']['value']))
            { 
                $searchVal = $request['search']['value'];
                $service_requestsQuery->where(function ($query) use ($searchVal) {

                    if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                    {
                        $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');

                    }else if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')){

                        $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');

                    }else{

                        $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                        $query->orWhere('service_centers.name', 'like', '%' . $searchVal . '%');
                    }
                    $query->orWhere('customers.firstname', 'like', '%' . $searchVal . '%');
                    $query->orWhere('products.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('service_requests.amount', 'like', '%' . $searchVal . '%');
                    $query->orWhere('service_requests.service_type', 'like', '%' . $searchVal . '%');

                });
            }

            return $service_requestsQuery->count('service_requests.id');
    }

    public function getTechnicianAssignedRequest($technicianId,$type = NULL)
    {   
        $assignedRequest = ServiceRequest::where('service_requests.technician_id',$technicianId)
        ->where('service_requests.status','!=','Closed');
      
        if($type == 'count'){
            return $assignedRequest->count();
        }else{

            /* select parameters */
            $assignedRequest->select('service_requests.id','service_requests.service_type',
                'service_requests.created_at','service_requests.customer_id','service_requests.amount','service_requests.completion_date',
                DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),
                DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)), 
                LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'),'service_requests.status',
                'service_requests.is_accepted'
            )
            ->join('customers','service_requests.customer_id','=','customers.id')
            ->join('products','service_requests.product_id','=','products.id');
            
            return $assignedRequest->get()->toArray();
        }
    }

    public function getTechnicianDueRequest($technicianId,$duration,$type = NULL)
    {
        $todayDate = date('Y-m-d');
        
        $dueRequest = ServiceRequest::where('technician_id',$technicianId)
        ->where('service_requests.is_accepted',1)
        ->where('service_requests.status','!=','Closed');
        
        if($duration == 'todaydue'){
            $dueRequest->whereRaw("DATE_FORMAT(completion_date, '%Y-%m-%d') = '".$todayDate."'");
        }else if($duration == 'overdue'){
            $dueRequest->whereRaw("DATE_FORMAT(completion_date, '%Y-%m-%d') < '".$todayDate."'");
        }
        
        if($type == 'count'){
            return $dueRequest->count();
        }else{
            $dueRequest->select('service_requests.id','service_requests.service_type',
                'service_requests.created_at','service_requests.customer_id','service_requests.amount','service_requests.completion_date',
                DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),
                DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)), 
                LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'),'service_requests.status',
                'service_requests.is_accepted'
            )
            ->join('customers','service_requests.customer_id','=','customers.id')
            ->join('products','service_requests.product_id','=','products.id');
            return $dueRequest->get();
        }
    }

    public function getTechnicianResolvedRequest($technicianId,$type = NULL)
    {   
        $resolvedRequest = ServiceRequest::where('technician_id',$technicianId)
        ->where('service_requests.is_accepted',1)
        ->where('service_requests.status','=','Closed');

        if($type == 'count'){
            return $resolvedRequest->count();
        }
        else{
            $resolvedRequest->select('service_requests.id','service_requests.service_type',
                'service_requests.created_at','service_requests.customer_id','service_requests.amount','service_requests.completion_date',
                DB::raw('CONCAT(customers.firstname," ",customers.lastname) as customer_name'),
                DB::raw('CONCAT(CONCAT(UCASE(LEFT(service_requests.service_type, 1)), 
                LCASE(SUBSTRING(service_requests.service_type, 2)))," - ",products.name) as servicerequest_title'),'service_requests.status',
                'service_requests.is_accepted'
            )
            ->join('customers','service_requests.customer_id','=','customers.id')
            ->join('products','service_requests.product_id','=','products.id');
            return $resolvedRequest->get();
        }
    }

    public function requestStatus($requestId,$status)
    {   
        $response = 0;
        if($status == 1){
           
            $updateArray = array(
                'is_accepted'  => 1,
                'status'       =>'Started'
            );
        }else{
            $updateArray = array(
                'is_accepted'   => 0,
                'technician_id' => NULL,
                'status'        =>'Cancelled'
            );
        }
        $requestStatus = ServiceRequest::where('id',$requestId)
        ->where('status','!=','Closed')->update($updateArray);

        if($requestStatus == 1){
            $response = 1;
        }
        return $response;
    } 
}
