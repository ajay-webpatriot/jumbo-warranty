<?php
namespace App\Http\Controllers\Admin;

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
        return view('admin.service_request_logs.index');
    }
}
