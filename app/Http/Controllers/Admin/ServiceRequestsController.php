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


        if (request('show_deleted') == 1) {
            if (! Gate::allows('service_request_delete')) {
                return abort(401);
            }
            $service_requests = ServiceRequest::onlyTrashed()->get();
        } else {
            if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {
                $service_requests = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->get();
            }
            else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            {
                $service_requests = ServiceRequest::where('technician_id',auth()->user()->id)->get();
            }
            else
            {
                $service_requests = ServiceRequest::all();
            }
            
        }
        // echo "<pre>"; print_r ($service_requests); echo "</pre>"; exit();
        return view('admin.service_requests.index', compact('service_requests'));
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
        $customers = \App\Customer::get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $technicians = \App\User::where('role_id',config('constants.TECHNICIAN_ROLE_ID'))->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $products = \App\Product::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $parts = \App\ProductPart::get()->pluck('name', 'id');

        $enum_service_type = ServiceRequest::$enum_service_type;
                    $enum_call_type = ServiceRequest::$enum_call_type;
                    $enum_call_location = ServiceRequest::$enum_call_location;
                    $enum_priority = ServiceRequest::$enum_priority;
                    $enum_is_item_in_warrenty = ServiceRequest::$enum_is_item_in_warrenty;
                    $enum_mop = ServiceRequest::$enum_mop;
                    $enum_status = ServiceRequest::$enum_status;
        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');
        
        return view('admin.service_requests.create', compact('enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName'));
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

        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+$request['additional_charges'];
        $request['km_distance']=0;
        $request['km_charge']=0;
        if($request['service_type'] == 'repair')
        {
            if($request['service_center_id'] != "" && $request['customer_id'] != "")
            {
                // calculate repair charges for different city

                $centerDetail=\App\ServiceCenter::findOrFail($request['service_center_id']);
                $customerDetail=\App\Customer::findOrFail($request['customer_id']);

                if($customerDetail->zipcode != $centerDetail->zipcode)
                {
                    $customer_latitude=$customerDetail->location_latitude;
                    $customer_longitude=$customerDetail->location_longitude;

                    $center_latitude=$centerDetail->location_latitude;
                    $center_longitude=$centerDetail->location_longitude;
                    
                    $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

                    $request['km_distance']=$distance;

                    $distance_charge=\App\ManageCharge::get()->first();
                    $request['km_charge']=$distance_charge->km_charge;
                    $total_amount+=($distance*$distance_charge->km_charge);
                   
                }
                $request['status'] ="Assigned";
            } 
        }
        $request['amount']=$total_amount;  
        // calculate total amount work end


        $service_request = ServiceRequest::create($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));

        // service request log
        $insertServiceRequestLogArr = array(
                                        'action_made'     =>   "Service request is created.", 
                                        'service_request_id'   =>   $service_request->id,
                                        'user_id'   =>   auth()->user()->id
                                    );
        ServiceRequestLog::create($insertServiceRequestLogArr);
            

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
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        
        $products = \App\Product::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $parts = \App\ProductPart::get()->pluck('name', 'id');

        $enum_service_type = ServiceRequest::$enum_service_type;
                    $enum_call_type = ServiceRequest::$enum_call_type;
                    $enum_call_location = ServiceRequest::$enum_call_location;
                    $enum_priority = ServiceRequest::$enum_priority;
                    $enum_is_item_in_warrenty = ServiceRequest::$enum_is_item_in_warrenty;
                    $enum_mop = ServiceRequest::$enum_mop;
                    $enum_status = ServiceRequest::$enum_status;
            
        $service_request = ServiceRequest::findOrFail($id);
        
        
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

        $customer_address="";
        $custAddressData = \App\Customer::where('id',$service_request['customer_id'])
                                        ->where('status','Active')
                                        ->get()->first();
                                       
        if($service_request['company_id'] != "")
        {
            // $customers = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            $customers = \App\Customer::where('company_id',$service_request['company_id'])
                                        ->where('status','Active')
                                        ->get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        }
        else
        {
            $customers=array(''=>trans('quickadmin.qa_please_select'));
        }
        

        

        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');

        $service_request_logs = ServiceRequestLog::where('service_request_id',$id)->get();

        return view('admin.service_requests.edit', compact('service_request', 'enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName', 'service_request_logs', 'custAddressData'))->with('no', 1);
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
        // echo "<pre>"; print_r ($request->all()); echo "</pre>"; exit();
        $service_request = ServiceRequest::findOrFail($id);

        if(isset($service_request->status) && isset($request['status'])){
           if($service_request->status !== $request['status']){
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Status is changed from ".$service_request->status." to ".$request['status'].".", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }
        if($request['technician_id'] != "")
        {
            if($service_request->technician_id != $request['technician_id']){

                $technician=\App\User::where('id',$request['technician_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Technician is assigned to ".$technician->name.".", 
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
            }
            if($service_request->service_center_id != $request['service_center_id']){

                $service_center=\App\ServiceCenter::where('id',$request['service_center_id'])->first();
                $insertServiceRequestLogArr =   array(
                                                    'action_made'     =>  "Service center is assigned to ".$service_center->name.".", 
                                                    'service_request_id'   =>   $id,
                                                    'user_id'   =>   auth()->user()->id
                                                );
                ServiceRequestLog::create($insertServiceRequestLogArr);
            }
        }  

        // calculate total amount work start
        $total_amount=$request['installation_charge']+$request['service_charge']+$request['additional_charges'];
        $request['km_distance']=0;
        $request['km_charge']=0;
        if($request['service_type'] == 'repair')
        {
            if($request['service_center_id'] != "" && $request['customer_id'] != "")
            {
                // calculate repair charges for different city

                $centerDetail=\App\ServiceCenter::findOrFail($request['service_center_id']);
                $customerDetail=\App\Customer::findOrFail($request['customer_id']);
                
                if($customerDetail->zipcode != $centerDetail->zipcode)
                {
                    $customer_latitude=$customerDetail->location_latitude;
                    $customer_longitude=$customerDetail->location_longitude;

                    $center_latitude=$centerDetail->location_latitude;
                    $center_longitude=$centerDetail->location_longitude;
                    
                    $distance=GoogleAPIHelper::distance($center_latitude,$center_longitude,$customer_latitude,$customer_longitude);

                    $request['km_distance']=$distance;

                    $distance_charge=\App\ManageCharge::get()->first();
                    $request['km_charge']=$distance_charge->km_charge;
                    $total_amount+=($distance*$distance_charge->km_charge);
                    
                   
                }
            } 
        }
        $request['amount']=$total_amount;  
        // calculate total amount work end


        $service_request->update($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));

        if($request['status'] == "Closed")
        {
            return $this->createReceiptPDF($request->all());
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
        $service_request_logs = ServiceRequestLog::where('service_request_id',$id)->get();
        return view('admin.service_requests.show', compact('service_request', 'service_request_logs'))->with('no', 1);
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

            foreach ($entries as $entry) {
                $entry->delete();
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
    public function createReceiptPDF($request)
    {
        
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
                            <div>".$centerDetail->city.",".$centerDetail->state." - ".$centerDetail->zipcode."</div>
                            ".$technician."
                        </div>";

            $installation_charge=($request['installation_charge'] != "" && $request['installation_charge'] != 0)? "<tr><td colspan='2'>Installation Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['installation_charge'],2)."</td></tr>":"";

            $service_charge=($request['service_charge'] != "" && $request['service_charge'] != 0)? "<tr><td colspan='2'>Service Charge</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['service_charge'],2)."</td></tr>":"";

            $km_distance=($request['km_distance'] != "" && $request['km_distance'] != 0)? "<tr><td colspan='2'>Distance</td><td class='price'>".number_format($request['km_distance'],2)."</td></tr>":"";

            $km_charge="";
            if($request['km_distance'] != "" && $request['km_distance'] != 0)
            {
                $km_charge=($request['km_charge'] != "" && $request['km_charge'] != 0)? "<tr><td colspan='2'>Charge per km</td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['km_charge'],2)."</td></tr>":"";
            }    
            

            $additional_charges=($request['additional_charges'] != "" && $request['additional_charges'] != 0)? "<tr><td colspan='2'>Additional Charge </td><td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['additional_charges'],2)."</td></tr>":"";

            $total_amount="<tr><td colspan='2'><b>Total amount</b></td><td class='price'><b><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".number_format($request['amount'],2)."</b></td></tr>";

            $parts_used="";
            if($request['service_type'] == "repair" && isset($request['parts']))
            {
                $obj= new ServiceRequest();
                $parts= $obj->getServiceRequestParts($request['parts']);  
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
                                        <td class='price'><span style='font-family: DejaVu Sans; sans-serif;'>&#8377;</span>".$productDetail->price."</td>
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
                                    ".$km_distance."
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
                           
                            .price{
                                color:#120CEA;
                            }
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
        if($details['serviceType'] == "installation" && $details['companyId'])
        {
            $companyDetails=\App\Company::findOrFail($details['companyId']);
            $data['installation_charge']=$companyDetails->installation_charge;
        }
        else if($details['serviceType'] == "repair" && $details['productId'])
        {
            $productDetails=\App\Product::findOrFail($details['productId']);
            $data['service_charge']=$productDetails->category->service_charge;
            
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
}
