<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

use Validator;
use Illuminate\Support\Facades\Input;

class ProductsController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageProduct')) {
                return abort(404);
            }
            return $next($request);
        });
    } 
    /**
     * Display a listing of Product.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('product_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('product_delete')) {
                return abort(401);
            }
            $products = Product::onlyTrashed()->get();
        } else {
            $products = Product::with('category')->get()->sortByDesc('id');
        }

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating new Product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('product_create')) {
            return abort(401);
        }
        
        $categories = \App\Category::orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Product::$enum_status;
            
        return view('admin.products.create', compact('enum_status', 'categories'));
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param  \App\Http\Requests\StoreProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductsRequest $request)
    {
        if (! Gate::allows('product_create')) {
            return abort(401);
        }
        $product = Product::create($request->all());



        return redirect()->route('admin.products.index')->with('success','Product added successfully!');
    }


    /**
     * Show the form for editing Product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('product_edit')) {
            return abort(401);
        }
        
        $categories = \App\Category::orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Product::$enum_status;
            
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product', 'enum_status', 'categories'));
    }

    /**
     * Update Product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductsRequest $request, $id)
    {
        if (! Gate::allows('product_edit')) {
            return abort(401);
        }
        $product = Product::findOrFail($id);

        // check product is assigned in service request
        if($request['status'] == "Inactive"){
            $validator = Validator::make($request->all(), []);
            $checkExistData = ServiceRequest::where('product_id', $id)->get();

            if(count($checkExistData) > 0)
            {   
                return redirect()->back()->withInput(Input::all())->with(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->add('Assigned', trans('This product is already assigned in service request.'))

                    ));
                    exit;
            }
        }

        $product->update($request->all());



        return redirect()->route('admin.products.index')->with('success','Product updated successfully!');
    }


    /**
     * Display Product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('product_view')) {
            return abort(401);
        }
        
        $categories = \App\Category::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');$assign_products = \App\AssignProduct::whereHas('product_id',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();$service_requests = \App\ServiceRequest::where('product_id', $id)->get();

        $product = Product::findOrFail($id);

        return view('admin.products.show', compact('product', 'assign_products', 'service_requests'));
    }


    /**
     * Remove Product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('product_delete')) {
            return abort(401);
        }
        $product = Product::findOrFail($id);

        // check product is assigned in service request
        $checkExistData = ServiceRequest::where('product_id', $id)->get();
        if(count($checkExistData) > 0)
        {   
            return  redirect()->route('admin.products.index')->withErrors('This product is already assigned in service request.');
            exit;
        }

        $product->delete();

        return redirect()->route('admin.products.index');
    }

    /**
     * Delete all selected Product at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('product_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Product::whereIn('id', $request->input('ids'))->get();

            $not_deleted=0;
            foreach ($entries as $entry) {
                // check product is assigned in service request
                $checkExistData = ServiceRequest::where('product_id', $entry->id)->get();
                if(count($checkExistData) > 0)
                {   
                    $not_deleted++;
                }
                else
                {
                    $entry->delete();
                }
            }
            if($not_deleted > 0)
            {
                redirect()->route('admin.products.index')->withErrors('Some product is already assigned in service request, so it is not deleted.');
            }
        }
    }


    /**
     * Restore Product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('product_delete')) {
            return abort(401);
        }
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.index');
    }

    /**
     * Permanently delete Product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('product_delete')) {
            return abort(401);
        }
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        return redirect()->route('admin.products.index');
    }
}
