<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
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

        if (request('show_deleted') == 1) {
            if (! Gate::allows('customer_delete')) {
                return abort(401);
            }
            $customers = Customer::onlyTrashed()->get();
        } else {

            if(auth()->user()->role_id ==  config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id ==  config('constants.COMPANY_USER_ROLE_ID'))
            {
                //get company admin's or user's own company customers if logged in user is company admin or user
                $customers = Customer::where('company_id',auth()->user()->company_id)->get();
            }
            else
            {
                $customers = Customer::all();
            }
            
        }

        return view('admin.customers.index', compact('customers'));
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
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Customer::$enum_status;
            
        return view('admin.customers.create', compact('enum_status', 'companies'));
    }

    /**
     * Store a newly created Customer in storage.
     *
     * @param  \App\Http\Requests\StoreCustomersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomersRequest $request)
    {
        if (! Gate::allows('customer_create')) {
            return abort(401);
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


        return redirect()->route('admin.customers.index');
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
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
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


        return redirect()->route('admin.customers.index');
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

            foreach ($entries as $entry) {
                $entry->delete();
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
    public function getCustomerAddress(Request $request)
    {
        
        // ajx function to get customer address
        
        $details=$request->all();
        $data['address']="";
        if($details['customerId'] != "")
        {
            $customer = Customer::where('id',$details['customerId'])
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
}
