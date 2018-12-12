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
        

         if (request('show_deleted') == 1) {
            if (! Gate::allows('service_request_delete')) {
                return abort(401);
            }
            $service_request_logs = ServiceRequestLog::onlyTrashed()->get();
        } else {
            
                $service_request_logs = ServiceRequestLog::all();
            
            
        }
        // echo "<pre>"; print_r ($service_request_logs); echo "</pre>"; exit();
        return view('admin.service_request_logs.index', compact('service_request_logs'));
    }
}
