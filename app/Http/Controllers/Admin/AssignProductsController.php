<?php

namespace App\Http\Controllers\Admin;

use App\AssignProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssignProductsRequest;
use App\Http\Requests\Admin\UpdateAssignProductsRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;
use DB;
use Validator;
use Illuminate\Support\Facades\Input;

class AssignProductsController extends Controller
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
     * Display a listing of AssignProduct.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assign_product_access')) {
            return abort(401);
        }


        // if (request('show_deleted') == 1) {
        //     if (! Gate::allows('assign_product_delete')) {
        //         return abort(401);
        //     }
        //     $assign_products = AssignProduct::onlyTrashed()->get();
        // } else {

        //     if(auth()->user()->role_id ==  config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id ==  config('constants.COMPANY_USER_ROLE_ID'))
        //     {
        //         //get company admin's or user's own company assigned product if logged in user is company admin or user
        //         $assign_products = AssignProduct::where('company_id',auth()->user()->company_id)->get();
        //     }
        //     else
        //     {
        //         $assign_products = AssignProduct::all();
        //     }
        // }
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
        return view('admin.assign_products.index', compact('companies'));
    }
    /**
     * Display a listing of assigned products ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableAssignProductAdminAjax(Request $request)
    {
        if (! Gate::allows('assign_product_access')) {
            return abort(401);
        }

        

        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  AssignProduct::select('assign_products.*','companies.name as company_name',DB::raw('group_concat(products.name) as product_name'),DB::raw("count(*) as total"))
         ->join('companies','assign_products.company_id','=','companies.id')
         ->join('products','products.id','=','assign_products.product_id')
         ->Where('products.status', 'Active')
         ->Where('companies.status', 'Active')
         ->groupBy('assign_products.company_id');

        
            $columnArray = array(
                    1 => 'assign_products.id',
                    2 => 'companies.name',
                    3 => 'product_name'
                );

            if(!empty($request->input('company')))
            {   
                $requestFilterCountQuery->Where('assign_products.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('products.name', 'like', '%' . $searchVal . '%');
                    
                });
            }
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

       
        $requestFilterCount = $requestFilterCountQuery->get()->count();

        // get data with  or without filter
        $assignProductQuery = AssignProduct::select('assign_products.*','companies.name as company_name',DB::raw('group_concat(products.name) as product_name'))
         ->join('companies','assign_products.company_id','=','companies.id')
         ->join('products','products.id','=','assign_products.product_id')
         ->Where('products.status', 'Active')
         ->Where('companies.status', 'Active')
         ->groupBy('assign_products.company_id')
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        
        if(!empty($request->input('company')))
        {   
            $assignProductQuery->Where('assign_products.company_id', $request['company']);
        }

        //Search from table
        if(!empty($request->input('search.value')))
        { 
            $searchVal = $request['search']['value'];
            $assignProductQuery->where(function ($query) use ($searchVal) {

                $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                

            });
        }


        // fetch total count without any filter
        $countRecord = AssignProduct::select('assign_products.*','companies.name as company_name',DB::raw('group_concat(products.name) as product_name'))
        ->join('companies','assign_products.company_id','=','companies.id')
        ->join('products','products.id','assign_products.product_id')
        ->Where('products.status', 'Active')
        ->Where('companies.status', 'Active')
        ->groupBy('assign_products.company_id')->get()->count();
        
        // echo $assignProductQuery->toSql();exit;

        $assignProducts = $assignProductQuery->get();
        
        if(!empty($assignProducts)){

            foreach ($assignProducts as $key => $assignProduct) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] =  $assignProduct->company_id;
                $tableField['company'] = $assignProduct->company_name;

                $product_array = explode(',', $assignProduct->product_name);
                $product_string = '';
                foreach ($product_array as $key => $value) {
                    $product_string .= '<span class="label label-info label-many">'.$value.'</span>';
                }
                $tableField['product_name'] = $product_string;

                $EditButtons = '';
                if (Gate::allows('assign_product_edit')) {
                    $EditButtons = '<a href="'.route('admin.assign_products.edit',$assignProduct->company_id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                $DeleteButtons = '';
                if (Gate::allows('assign_product_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.assign_products.destroy',$assignProduct->company_id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

                    <input name="_method" type="hidden" value="DELETE">
                    <input type="hidden"
                               name="_token"
                               value="'.csrf_token().'">
                    <input type="submit" class="btn btn-xs btn-danger" value="Delete" />
                    </form>';
                }

                $tableField['action'] = $ViewButtons.' '.$EditButtons.' '.$DeleteButtons;
                $tableFieldData[] = $tableField;
            }
           
        }
               
        $json_data = array(
            "draw"            => intval($request['draw']),  
            "recordsTotal"    => intval($countRecord),  
            "recordsFiltered" => intval($requestFilterCount),
            "data"            => $tableFieldData   
            );
        // $json_data = array(
        //     "draw"            => intval($request['draw']),  
        //     "recordsTotal"    => 0,  
        //     "recordsFiltered" => 0,
        //     "data"            => array()   
        //     );

        echo json_encode($json_data);

       
    }
    public function DataTableAssignProductAjax(Request $request)
    {
        if (! Gate::allows('assign_product_access')) {
            return abort(401);
        }

        
        // list data for compoany user and admin

        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  AssignProduct::select('assign_products.*','companies.name as company_name','products.name as product_name')
         ->join('companies','assign_products.company_id','=','companies.id')
         ->join('products','products.id','=','assign_products.product_id')
         ->Where('products.status', 'Active')
         ->Where('companies.status', 'Active')
         ->where('assign_products.company_id',auth()->user()->company_id);

        
        $columnArray = array(
                0 => 'assign_products.id',
                1 => 'products.name'
            );

        //Search from table
        if(!empty($request->input('search.value')))
        { 
            $searchVal = $request['search']['value'];
            $requestFilterCountQuery->where(function ($query) use ($searchVal) {
                
                $query->orWhere('products.name', 'like', '%' . $searchVal . '%');

            });
        } 
        
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        
        $requestFilterCount = $requestFilterCountQuery->count('assign_products.id');
        

        $assignProductQuery = AssignProduct::select('assign_products.*','companies.name as company_name','products.name as product_name')
         ->join('companies','assign_products.company_id','=','companies.id')
         ->join('products','products.id','assign_products.product_id')
         ->Where('products.status', 'Active')
         ->Where('companies.status', 'Active')
         ->where('assign_products.company_id',auth()->user()->company_id)
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);

        //Search from table
        if(!empty($request->input('search.value')))
        { 
            $searchVal = $request['search']['value'];
            $assignProductQuery->where(function ($query) use ($searchVal) {

                $query->orWhere('products.name', 'like', '%' . $searchVal . '%');

            });
        }
        // fetch total count without any filter
        $countRecord = AssignProduct::select('assign_products.*','companies.name as company_name','products.name as product_name')
        ->join('companies','assign_products.company_id','=','companies.id')
        ->join('products','products.id','assign_products.product_id')
        ->where('company_id',auth()->user()->company_id)
        ->Where('products.status', 'Active')
        ->Where('companies.status', 'Active')->count('assign_products.id');
        

        
        
        $assignProducts = $assignProductQuery->get();
        // echo "<pre>"; print_r ($assignProducts); echo "</pre>"; exit();

        if(!empty($assignProducts)){

            foreach ($assignProducts as $key => $assignProduct) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] =  $assignProduct->id;
                $tableField['company'] = $assignProduct->company_name;
                $tableField['product_name'] = $assignProduct->product_name;

                $EditButtons = '';
                if (Gate::allows('assign_product_edit')) {
                    $EditButtons = '<a href="'.route('admin.assign_products.edit',$assignProduct->id).'" class="btn btn-xs btn-info">Edit</a>';
                }
                $DeleteButtons = '';
                if (Gate::allows('assign_product_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.assign_products.destroy',$assignProduct->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

                    <input name="_method" type="hidden" value="DELETE">
                    <input type="hidden"
                               name="_token"
                               value="'.csrf_token().'">
                    <input type="submit" class="btn btn-xs btn-danger" value="Delete" />
                    </form>';
                }

                $tableField['action'] = $ViewButtons.' '.$EditButtons.' '.$DeleteButtons;
                $tableFieldData[] = $tableField;
            }
           
        }
               
        $json_data = array(
            "draw"            => intval($request['draw']),  
            "recordsTotal"    => intval($countRecord),  
            "recordsFiltered" => intval($requestFilterCount),
            "data"            => $tableFieldData   
            );
        // $json_data = array(
        //     "draw"            => intval($request['draw']),  
        //     "recordsTotal"    => 0,  
        //     "recordsFiltered" => 0,
        //     "data"            => array()   
        //     );

        echo json_encode($json_data);

       
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
        
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_ids = \App\Product::where('status','Active')->get()->pluck('name', 'id');
            
        return view('admin.assign_products.create', compact('companies', 'product_ids'));
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

        $assign_product = AssignProduct::where('company_id',$request->company_id);
        if(!empty($assign_product))
        {
            $assign_product->delete();
        }
        
        $productIds = $request->product_id;
        if(!empty($productIds))
        {
            foreach ($productIds as $key => $value) {
                
                $assignProductObj = new AssignProduct([
                    'company_id' => $request->company_id,
                    'product_id' => $value,
                ]);
                $assignProductObj->save();
            }
        }
        
        // $assign_product = AssignProduct::create($request->all());
        // $assign_product->product_id()->sync(array_filter((array)$request->input('product_id')));



        return redirect()->route('admin.assign_products.index')->with('success','Assign Products added successfully!');
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
        
        $companies = \App\Company::where('status','Active')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_ids = \App\Product::where('status','Active')->get()->pluck('name', 'id');

        // $assign_product = AssignProduct::findOrFail($id);

        $assign_product = array();
        $product_id = array();
        $assign_product_data = AssignProduct::where('company_id',$id)->get();

        if(empty($assign_product_data))
        {
            return abort(404);
        }
        else
        {
            foreach ($assign_product_data as $key => $product) {
                array_push($product_id, $product->product_id);
            }
            $assign_product =  (object) array_merge($assign_product, array('product_id' => $product_id,'company_id' => $id) );
        }
        return view('admin.assign_products.edit', compact('assign_product', 'companies', 'product_ids'));
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
        // $assign_product = AssignProduct::findOrFail($id);
        // $assign_product->update($request->all());
        // $assign_product->product_id()->sync(array_filter((array)$request->input('product_id')));

        $assign_product = AssignProduct::where('company_id',$id);
        if(empty($assign_product))
        {
            return abort(404);
        }
        $assign_product->delete(); // delete existing product

        $productIds = $request->product_id;
        if(!empty($productIds))
        {
            foreach ($productIds as $key => $value) {
                
                $assignProductObj = new AssignProduct([
                    'company_id' => $request->company_id,
                    'product_id' => $value,
                ]);
                $assignProductObj->save();
            }
        }

        return redirect()->route('admin.assign_products.index')->with('success','Assign Products updated successfully!');
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
        // $assign_product = AssignProduct::findOrFail($id);
        // $assign_product->delete();

        $assign_product = AssignProduct::where('company_id',$id);
        if(!empty($assign_product))
        {
            $assign_product->delete();
        }

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
            // $entries = AssignProduct::whereIn('id', $request->input('ids'))->get();
            $entries = AssignProduct::whereIn('company_id', $request->input('ids'))->get();

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

    public function getAssignedProductsAjax(Request $request)
    {
        $details=$request->all();

        $data['selectedProductOptions']=array();
        
        if(!empty($details['companyId']))
        {
            $assign_product_data = AssignProduct::where('company_id',$details['companyId'])->get();

            if(!empty($assign_product_data))
            {
                foreach ($assign_product_data as $key => $product) {
                   array_push($data['selectedProductOptions'], $product->product_id);
                }
            }
        }    
        echo json_encode($data);
        exit;
    }
}
