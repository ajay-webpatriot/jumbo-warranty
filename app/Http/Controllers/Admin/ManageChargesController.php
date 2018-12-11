<?php

namespace App\Http\Controllers\Admin;

use App\ManageCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManageChargesRequest;
use App\Http\Requests\Admin\UpdateManageChargesRequest;

class ManageChargesController extends Controller
{
    /**
     * Display a listing of ManageCharge.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('manage_charge_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('manage_charge_delete')) {
                return abort(401);
            }
            $manage_charges = ManageCharge::onlyTrashed()->get();
        } else {
            $manage_charges = ManageCharge::all();
        }

        return view('admin.manage_charges.index', compact('manage_charges'));
    }

    /**
     * Show the form for creating new ManageCharge.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('manage_charge_create')) {
            return abort(401);
        }        $enum_status = ManageCharge::$enum_status;
            
        return view('admin.manage_charges.create', compact('enum_status'));
    }

    /**
     * Store a newly created ManageCharge in storage.
     *
     * @param  \App\Http\Requests\StoreManageChargesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManageChargesRequest $request)
    {
        if (! Gate::allows('manage_charge_create')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::create($request->all());



        return redirect()->route('admin.manage_charges.index');
    }


    /**
     * Show the form for editing ManageCharge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('manage_charge_edit')) {
            return abort(401);
        }        $enum_status = ManageCharge::$enum_status;
            
        $manage_charge = ManageCharge::findOrFail($id);

        return view('admin.manage_charges.edit', compact('manage_charge', 'enum_status'));
    }

    /**
     * Update ManageCharge in storage.
     *
     * @param  \App\Http\Requests\UpdateManageChargesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManageChargesRequest $request, $id)
    {
        if (! Gate::allows('manage_charge_edit')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::findOrFail($id);
        $manage_charge->update($request->all());



        return redirect()->route('admin.manage_charges.index');
    }


    /**
     * Display ManageCharge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('manage_charge_view')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::findOrFail($id);

        return view('admin.manage_charges.show', compact('manage_charge'));
    }


    /**
     * Remove ManageCharge from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('manage_charge_delete')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::findOrFail($id);
        $manage_charge->delete();

        return redirect()->route('admin.manage_charges.index');
    }

    /**
     * Delete all selected ManageCharge at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('manage_charge_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = ManageCharge::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore ManageCharge from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('manage_charge_delete')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::onlyTrashed()->findOrFail($id);
        $manage_charge->restore();

        return redirect()->route('admin.manage_charges.index');
    }

    /**
     * Permanently delete ManageCharge from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('manage_charge_delete')) {
            return abort(401);
        }
        $manage_charge = ManageCharge::onlyTrashed()->findOrFail($id);
        $manage_charge->forceDelete();

        return redirect()->route('admin.manage_charges.index');
    }
}
