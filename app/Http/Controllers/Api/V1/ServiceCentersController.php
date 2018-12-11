<?php

namespace App\Http\Controllers\Api\V1;

use App\ServiceCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCentersRequest;
use App\Http\Requests\Admin\UpdateServiceCentersRequest;

class ServiceCentersController extends Controller
{
    public function index()
    {
        return ServiceCenter::all();
    }

    public function show($id)
    {
        return ServiceCenter::findOrFail($id);
    }

    public function update(UpdateServiceCentersRequest $request, $id)
    {
        $service_center = ServiceCenter::findOrFail($id);
        $service_center->update($request->all());
        

        return $service_center;
    }

    public function store(StoreServiceCentersRequest $request)
    {
        $service_center = ServiceCenter::create($request->all());
        

        return $service_center;
    }

    public function destroy($id)
    {
        $service_center = ServiceCenter::findOrFail($id);
        $service_center->delete();
        return '';
    }
}
