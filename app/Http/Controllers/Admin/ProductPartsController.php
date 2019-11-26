<?php

namespace App\Http\Controllers\Admin;

use App\ProductPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductPartsRequest;
use App\Http\Requests\Admin\UpdateProductPartsRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class ProductPartsController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageParts')) {
                return abort(404);
            }
            return $next($request);
        });
    } 
    /**
     * Display a listing of ProductPart.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('product_part_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('product_part_delete')) {
                return abort(401);
            }
            $product_parts = ProductPart::onlyTrashed()->get();
        } else {
            $product_parts = ProductPart::all()->sortByDesc('id');
        }

        return view('admin.product_parts.index', compact('product_parts'));
    }

    /**
     * Show the form for creating new ProductPart.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('product_part_create')) {
            return abort(401);
        }        $enum_status = ProductPart::$enum_status;
            
        return view('admin.product_parts.create', compact('enum_status'));
    }

    /**
     * Store a newly created ProductPart in storage.
     *
     * @param  \App\Http\Requests\StoreProductPartsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductPartsRequest $request)
    {
        if (! Gate::allows('product_part_create')) {
            return abort(401);
        }
        $product_part = ProductPart::create($request->all());



        return redirect()->route('admin.product_parts.index')->with('success','Product Parts added successfully!');
    }


    /**
     * Show the form for editing ProductPart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('product_part_edit')) {
            return abort(401);
        }        $enum_status = ProductPart::$enum_status;
            
        $product_part = ProductPart::findOrFail($id);

        return view('admin.product_parts.edit', compact('product_part', 'enum_status'));
    }

    /**
     * Update ProductPart in storage.
     *
     * @param  \App\Http\Requests\UpdateProductPartsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductPartsRequest $request, $id)
    {
        if (! Gate::allows('product_part_edit')) {
            return abort(401);
        }
        $product_part = ProductPart::findOrFail($id);
        $product_part->update($request->all());



        return redirect()->route('admin.product_parts.index')->with('success','Product Parts updated successfully!');
    }


    /**
     * Display ProductPart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('product_part_view')) {
            return abort(401);
        }
        $assign_parts = \App\AssignPart::where('product_parts_id', $id)->get();$service_requests = \App\ServiceRequest::whereHas('parts',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();

        $product_part = ProductPart::findOrFail($id);

        return view('admin.product_parts.show', compact('product_part', 'assign_parts', 'service_requests'));
    }


    /**
     * Remove ProductPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('product_part_delete')) {
            return abort(401);
        }
        $product_part = ProductPart::findOrFail($id);
        $product_part->delete();

        return redirect()->route('admin.product_parts.index');
    }

    /**
     * Delete all selected ProductPart at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('product_part_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = ProductPart::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore ProductPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('product_part_delete')) {
            return abort(401);
        }
        $product_part = ProductPart::onlyTrashed()->findOrFail($id);
        $product_part->restore();

        return redirect()->route('admin.product_parts.index');
    }

    /**
     * Permanently delete ProductPart from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('product_part_delete')) {
            return abort(401);
        }
        $product_part = ProductPart::onlyTrashed()->findOrFail($id);
        $product_part->forceDelete();

        return redirect()->route('admin.product_parts.index');
    }
}
