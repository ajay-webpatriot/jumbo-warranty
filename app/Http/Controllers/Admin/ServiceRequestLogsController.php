<?php
namespace App\Http\Controllers\Admin;

use App\ServiceRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

// models
use App\ServiceRequest;

class ServiceRequestLogsController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageServiceRequestLog')) {
                return abort(404);
            }
            return $next($request);
        });
    }
    public function index()
    {
        if (! Gate::allows('service_request_log_access')) {
            return abort(401);
        }
        
        // if (request('show_deleted') == 1) {
        //     if (! Gate::allows('service_request_log_delete')) {
        //         return abort(401);
        //     }
        //     $service_request_log = ServiceRequestLog::onlyTrashed()->orderby('created_at','desc')->get();
        // } else {
            
        //         $service_request_log = ServiceRequestLog::all()->sortByDesc('created_at');
        //     }

        
        if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
        {
            $service_request_log = ServiceRequest::where('service_center_id',auth()->user()->service_center_id)->orderBy('id', 'desc')->get();
        }
        else if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
        {
            $service_request_log = ServiceRequest::where('technician_id',auth()->user()->id)->orderBy('id', 'desc')->get();
        }
        else
        {
            $service_request_log = ServiceRequest::all()->sortByDesc('id');
        }
    	// $service_request_log = ServiceRequestLog::all();
       	return view('admin.service_request_logs.index', compact('service_request_log'));
    }

    /**
     * Display ServiceRequestLogs.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('service_request_log_view')) {
            return abort(401);
        }
        // $service_request_log = ServiceRequestLog::select()
        // 												->Join('service_requests', 'service_request_logs.service_request_id', '=', 'service_requests.id')
    				// 									->Join('users', 'service_request_logs.user_id', '=', 'users.id')
    				// 									->where('service_request_logs.id', $id)
    				// 									->orderby('service_request_logs.created_at','desc')
    				// 									->get();
    	$service_request_log = ServiceRequestLog::where('service_request_id',$id)->get();
        // echo "<pre>"; print_r ($service_request_logs); echo "</pre>"; exit();
        return view('admin.service_request_logs.show', compact('service_request_log'))->with('no', 1);
    }

    /**
     * Delete all selected ServiceRequestLogs at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {	
    	if (! Gate::allows('service_request_log_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = ServiceRequestLog::whereIn('id', $request->input('ids'))->get();
			foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
}
