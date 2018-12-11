<?php

namespace App\Http\Controllers\Admin;

use App\AssignProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssignProductsRequest;
use App\Http\Requests\Admin\UpdateAssignProductsRequest;

class AssignProductsController extends Controller
{
    /**
     * Display a listing of AssignProduct.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assign_product_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('assign_product_delete')) {
                return abort(401);
            }
            $assign_products = AssignProduct::onlyTrashed()->get();
        } else {
            $assign_products = AssignProduct::all();
        }

        return view('admin.assign_products.index', compact('assign_products'));
    }

    /**
     * Show the form for creating new AssignProduct.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assign_product_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_ids = \App\Product::get()->pluck('name', 'id');

        $enum_status = AssignProduct::$enum_status;
            
        return view('admin.assign_products.create', compact('enum_status', 'companies', 'product_ids'));
    }

    /**
     * Store a newly created AssignProduct in storage.
     *
     * @param  \App\Http\Requests\StoreAssignProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssignProductsRequest $request)
    {
        if (! Gate::allows('assign_product_create')) {
            return abort(401);
        }
        $assign_product = AssignProduct::create($request->all());
        $assign_product->product_id()->sync(array_filter((array)$request->input('product_id')));



        return redirect()->route('admin.assign_products.index');
    }


    /**
     * Show the form for editing AssignProduct.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('assign_product_edit')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_ids = \App\Product::get()->pluck('name', 'id');

        $enum_status = AssignProduct::$enum_status;
            
        $assign_product = AssignProduct::findOrFail($id);

        return view('admin.assign_products.edit', compact('assign_product', 'enum_status', 'companies', 'product_ids'));
    }

    /**
     * Update AssignProduct in storage.
     *
     * @param  \App\Http\Requests\UpdateAssignProductsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssignProductsRequest $request, $id)
    {
        if (! Gate::allows('assign_product_edit')) {
            return abort(401);
        }
        $assign_product = AssignProduct::findOrFail($id);
        $assign_product->update($request->all());
        $assign_product->product_id()->sync(array_filter((array)$request->input('product_id')));



        return redirect()->route('admin.assign_products.index');
    }


    /**
     * Display AssignProduct.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('assign_product_view')) {
            return abort(401);
        }
        $assign_product = AssignProduct::findOrFail($id);

        return view('admin.assign_products.show', compact('assign_product'));
    }


    /**
     * Remove AssignProduct from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('assign_product_delete')) {
            return abort(401);
        }
        $assign_product = AssignProduct::findOrFail($id);
        $assign_product->delete();

        return redirect()->route('admin.assign_products.index');
    }

    /**
     * Delete all selected AssignProduct at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('assign_product_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = AssignProduct::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore AssignProduct from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('assign_product_delete')) {
            return abort(401);
        }
        $assign_product = AssignProduct::onlyTrashed()->findOrFail($id);
        $assign_product->restore();

        return redirect()->route('admin.assign_products.index');
    }

    /**
     * Permanently delete AssignProduct from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('assign_product_delete')) {
            return abort(401);
        }
        $assign_product = AssignProduct::onlyTrashed()->findOrFail($id);
        $assign_product->forceDelete();

        return redirect()->route('admin.assign_products.index');
    }
}
