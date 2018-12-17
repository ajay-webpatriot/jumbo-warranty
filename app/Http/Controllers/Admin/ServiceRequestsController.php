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
            if(auth()->user()->role_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
            {
                $service_requests = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->get();
            }
            else if(auth()->user()->role_id == $_ENV['TECHNICIAN_ROLE_ID'])
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
        $technicians = \App\User::where('role_id',$_ENV['TECHNICIAN_ROLE_ID'])->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
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
        // $customers = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $customers = \App\Customer::get()->pluck('firstname', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $service_centers = \App\ServiceCenter::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        // $technicians = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $technicians = \App\User::where('role_id',$_ENV['TECHNICIAN_ROLE_ID'])->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
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
        $companyName = \App\Company::where('id',auth()->user()->company_id)->get()->pluck('name');
        return view('admin.service_requests.edit', compact('service_request', 'enum_service_type', 'enum_call_type', 'enum_call_location', 'enum_priority', 'enum_is_item_in_warrenty', 'enum_mop', 'enum_status', 'companies', 'customers', 'service_centers', 'technicians', 'products', 'parts','companyName'));
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
        if(isset($request['technician_id']))
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
        if(isset($request['service_center_id']))
        {
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
        $service_request->update($request->all());
        $service_request->parts()->sync(array_filter((array)$request->input('parts')));



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

        return view('admin.service_requests.show', compact('service_request'));
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
}
