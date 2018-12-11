<?php

namespace App\Http\Controllers\Api\V1;

use App\ManageCharge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManageChargesRequest;
use App\Http\Requests\Admin\UpdateManageChargesRequest;

class ManageChargesController extends Controller
{
    public function index()
    {
        return ManageCharge::all();
    }

    public function show($id)
    {
        return ManageCharge::findOrFail($id);
    }

    public function update(UpdateManageChargesRequest $request, $id)
    {
        $manage_charge = ManageCharge::findOrFail($id);
        $manage_charge->update($request->all());
        

        return $manage_charge;
    }

    public function store(StoreManageChargesRequest $request)
    {
        $manage_charge = ManageCharge::create($request->all());
        

        return $manage_charge;
    }

    public function destroy($id)
    {
        $manage_charge = ManageCharge::findOrFail($id);
        $manage_charge->delete();
        return '';
    }
}
