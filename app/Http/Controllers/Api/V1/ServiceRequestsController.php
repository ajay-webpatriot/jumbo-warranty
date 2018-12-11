<?php

namespace App\Http\Controllers\Api\V1;

use App\ServiceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequestsRequest;
use App\Http\Requests\Admin\UpdateServiceRequestsRequest;

class ServiceRequestsController extends Controller
{
    public function index()
    {
        return ServiceRequest::all();
    }

    public function show($id)
    {
        return ServiceRequest::findOrFail($id);
    }

    public function update(UpdateServiceRequestsRequest $request, $id)
    {
        $service_request = ServiceRequest::findOrFail($id);
        $service_request->update($request->all());
        

        return $service_request;
    }

    public function store(StoreServiceRequestsRequest $request)
    {
        $service_request = ServiceRequest::create($request->all());
        

        return $service_request;
    }

    public function destroy($id)
    {
        $service_request = ServiceRequest::findOrFail($id);
        $service_request->delete();
        return '';
    }
}
