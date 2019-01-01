<?php

namespace App\Http\Controllers\Admin;

use App\AssignPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssignPartsRequest;
use App\Http\Requests\Admin\UpdateAssignPartsRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class AssignPartsController extends Controller
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
     * Display a listing of AssignPart.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assign_part_access')) {
            return abort(401);
        }

        $AssignPart = new AssignPart(); // model object to call custom functions
        
        
        if (request('show_deleted') == 1) {
            if (! Gate::allows('assign_part_delete')) {
                return abort(401);
            }
            $assign_parts = AssignPart::onlyTrashed()->get();
        } else {
            $assign_parts = AssignPart::all();
        }
        
        foreach ($assign_parts as $key => $value) {
            $usedParts=$AssignPart->getRequestedServiceParts($value->product_parts_id,$value->company_id);// get quantity of used parts in service requests
            $value['availableQuantity']=$value->quantity-$usedParts;
        }
        
        return view('admin.assign_parts.index', compact('assign_parts'));
    }

    /**
     * Show the form for creating new AssignPart.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assign_part_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_parts = \App\ProductPart::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            
        return view('admin.assign_parts.create', compact('companies', 'product_parts'));
    }

    /**
     * Store a newly created AssignPart in storage.
     *
     * @param  \App\Http\Requests\StoreAssignPartsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssignPartsRequest $request)
    {
        if (! Gate::allows('assign_part_create')) {
            return abort(401);
        }
        $assign_part = AssignPart::create($request->all());



        return redirect()->route('admin.assign_parts.index');
    }


    /**
     * Show the form for editing AssignPart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('assign_part_edit')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_parts = \App\ProductPart::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            
        $assign_part = AssignPart::findOrFail($id);

        return view('admin.assign_parts.edit', compact('assign_part', 'companies', 'product_parts'));
    }

    /**
     * Update AssignPart in storage.
     *
     * @param  \App\Http\Requests\UpdateAssignPartsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssignPartsRequest $request, $id)
    {
        if (! Gate::allows('assign_part_edit')) {
            return abort(401);
        }
        $assign_part = AssignPart::findOrFail($id);
        $assign_part->update($request->all());



        return redirect()->route('admin.assign_parts.index');
    }


    /**
     * Display AssignPart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('assign_part_view')) {
            return abort(401);
        }
        $assign_part = AssignPart::findOrFail($id);

        return view('admin.assign_parts.show', compact('assign_part'));
    }


    /**
     * Remove AssignPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('assign_part_delete')) {
            return abort(401);
        }
        $assign_part = AssignPart::findOrFail($id);
        $assign_part->delete();

        return redirect()->route('admin.assign_parts.index');
    }

    /**
     * Delete all selected AssignPart at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('assign_part_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = AssignPart::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore AssignPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('assign_part_delete')) {
            return abort(401);
        }
        $assign_part = AssignPart::onlyTrashed()->findOrFail($id);
        $assign_part->restore();

        return redirect()->route('admin.assign_parts.index');
    }

    /**
     * Permanently delete AssignPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('assign_part_delete')) {
            return abort(401);
        }
        $assign_part = AssignPart::onlyTrashed()->findOrFail($id);
        $assign_part->forceDelete();

        return redirect()->route('admin.assign_parts.index');
    }
}
