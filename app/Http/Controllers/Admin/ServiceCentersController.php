<?php

namespace App\Http\Controllers\Admin;

use App\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCentersRequest;
use App\Http\Requests\Admin\UpdateServiceCentersRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

// get lat long 
use GoogleAPIHelper;

class ServiceCentersController extends Controller
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
     * Display a listing of ServiceCenter.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // echo GoogleAPIHelper::distance(22.8418873, 72.5559746, 23.024349, 72.5301521, "K");exit;
        if (! Gate::allows('service_center_access')) {
            return abort(401);
        }

        if (request('show_deleted') == 1) {
            if (! Gate::allows('service_center_delete')) {
                return abort(401);
            }
            $service_centers = ServiceCenter::onlyTrashed()->get();
        } else {
            $service_centers = ServiceCenter::all();
        }

        return view('admin.service_centers.index', compact('service_centers'));
    }

    /**
     * Show the form for creating new ServiceCenter.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('service_center_create')) {
            return abort(401);
        }        $enum_status = ServiceCenter::$enum_status;
            
        return view('admin.service_centers.create', compact('enum_status'));
    }

    /**
     * Store a newly created ServiceCenter in storage.
     *
     * @param  \App\Http\Requests\StoreServiceCentersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceCentersRequest $request)
    {

        if (! Gate::allows('service_center_create')) {
            return abort(401);
        }

        $resultLocation=GoogleAPIHelper::getLatLong($request['zipcode']);
            
        if($resultLocation){    
            $request['location_latitude']=$resultLocation['lat'];
            $request['location_longitude']=$resultLocation['lng'];
        }

        $service_center = ServiceCenter::create($request->all());



        return redirect()->route('admin.service_centers.index');
    }


    /**
     * Show the form for editing ServiceCenter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('service_center_edit')) {
            return abort(401);
        }        $enum_status = ServiceCenter::$enum_status;
            
        $service_center = ServiceCenter::findOrFail($id);

        return view('admin.service_centers.edit', compact('service_center', 'enum_status'));
    }

    /**
     * Update ServiceCenter in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceCentersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceCentersRequest $request, $id)
    {
        if (! Gate::allows('service_center_edit')) {
            return abort(401);
        }
        $service_center = ServiceCenter::findOrFail($id);


        if(isset($service_center->zipcode) && isset($request['zipcode']))
        {
            if($service_center->zipcode !== $request['zipcode'])
            {

                $resultLocation=GoogleAPIHelper::getLatLong($request['zipcode']);
                    
                if($resultLocation){    
                    $request['location_latitude']=$resultLocation['lat'];
                    $request['location_longitude']=$resultLocation['lng'];
                }
            }
        }     
        
        $service_center->update($request->all());



        return redirect()->route('admin.service_centers.index');
    }


    /**
     * Display ServiceCenter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('service_center_view')) {
            return abort(401);
        }
        $users = \App\User::where('service_center_id', $id)->get();$service_requests = \App\ServiceRequest::where('service_center_id', $id)->get();

        $service_center = ServiceCenter::findOrFail($id);

        return view('admin.service_centers.show', compact('service_center', 'users', 'service_requests'));
    }


    /**
     * Remove ServiceCenter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('service_center_delete')) {
            return abort(401);
        }
        $service_center = ServiceCenter::findOrFail($id);
        $service_center->delete();

        return redirect()->route('admin.service_centers.index');
    }

    /**
     * Delete all selected ServiceCenter at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('service_center_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = ServiceCenter::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore ServiceCenter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('service_center_delete')) {
            return abort(401);
        }
        $service_center = ServiceCenter::onlyTrashed()->findOrFail($id);
        $service_center->restore();

        return redirect()->route('admin.service_centers.index');
    }

    /**
     * Permanently delete ServiceCenter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('service_center_delete')) {
            return abort(401);
        }
        $service_center = ServiceCenter::onlyTrashed()->findOrFail($id);
        $service_center->forceDelete();

        return redirect()->route('admin.service_centers.index');
    }

    
}
