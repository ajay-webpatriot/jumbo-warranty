<?php
namespace App\Http\Controllers\Admin;

use App\ServiceRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

class ServiceRequestLogsController extends Controller
{
    public function index()
    {
        if (! Gate::allows('service_request_log_access')) {
            return abort(401);
        }
        
            $service_request_log = ServiceRequestLog::select('service_request_logs.id', 'users.name', 'users.email', 'service_requests.service_type', 'service_request_logs.status_made', 'service_request_logs.created_at', 'service_request_logs.updated_at')
        												->Join('service_requests', 'service_request_logs.service_request_id', '=', 'service_requests.id')
    													->Join('users', 'service_request_logs.user_id', '=', 'users.id')
    													->orderby('service_request_logs.created_at','desc')
    													->get();
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
        $service_request_log = ServiceRequestLog::select()
        												->Join('service_requests', 'service_request_logs.service_request_id', '=', 'service_requests.id')
    													->Join('users', 'service_request_logs.user_id', '=', 'users.id')
    													->where('service_request_logs.id', $id)
    													->orderby('service_request_logs.created_at','desc')
    													->get();
    	// $service_request_logs = ServiceRequestLog::findOrFail($id);

        return view('admin.service_request_logs.show', compact('service_request_logs'));
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
