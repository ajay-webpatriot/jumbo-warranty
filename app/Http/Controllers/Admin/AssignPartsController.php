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

use DB;
use Validator;
use Illuminate\Support\Facades\Input;

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
        
        
        // if (request('show_deleted') == 1) {
        //     if (! Gate::allows('assign_part_delete')) {
        //         return abort(401);
        //     }
        //     $assign_parts = AssignPart::onlyTrashed()->get();
        // } else {
            if(auth()->user()->role_id ==  config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id ==  config('constants.COMPANY_USER_ROLE_ID'))
            {
                //get company admin's or user's own company assigned parts if logged in user is company admin or user
                $assign_parts = AssignPart::where('company_id',auth()->user()->company_id)->get();
            }
            else
            {
                $assign_parts = AssignPart::all();
            }
            
            
        // }
        
        foreach ($assign_parts as $key => $value) {
            $usedParts=$AssignPart->getRequestedServiceParts($value->product_parts_id,$value->company_id);// get quantity of used
            $value['availableQuantity']=$value->quantity-$usedParts;
        } 

        $companies = \App\Company::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_show_all'), '');
        return view('admin.assign_parts.index', compact('companies'));
    }
    /**
     * Display a listing of assigned parts ajax data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function DataTableAssignPartAjax(Request $request)
    {
        if (! Gate::allows('assign_part_access')) {
            return abort(401);
        }

        $tableFieldData = [];
        $ViewButtons = '';
        $EditButtons = '';
        $DeleteButtons = '';

        // count data with filter value
        $requestFilterCountQuery =  AssignPart::select('assign_parts.*','companies.name as company_name','product_parts.name as part_name',
            DB::raw("(assign_parts.quantity - (SELECT COUNT(distinct service_requests.id) FROM service_requests 
                join product_part_service_request on service_requests.id = product_part_service_request.service_request_id 
                                WHERE assign_parts.product_parts_id = product_part_service_request.product_part_id and service_requests.company_id = assign_parts.company_id and service_requests.deleted_at IS NULL 
                                ) )  as available_quantity")
            )
         ->join('companies','assign_parts.company_id','=','companies.id')
         ->join('product_parts','product_parts.id','=','assign_parts.product_parts_id')
         ->whereNull('product_parts.deleted_at')
         ->whereNull('companies.deleted_at')
         ->Where('product_parts.status', 'Active')
         ->Where('companies.status', 'Active');

        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            $columnArray = array(
                    1 => 'assign_parts.id',
                    2 => 'companies.name',
                    3 => 'product_parts.name',
                    4 => 'assign_parts.quantity',
                    5 => 'available_quantity'
                );

            if(!empty($request->input('company')))
            {   
                $requestFilterCountQuery->Where('assign_parts.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('product_parts.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('assign_parts.quantity', 'like', '%' . $searchVal . '%');
                    
                });
            }
        }
        else{
            $columnArray = array(
                    0 => 'assign_parts.id',
                    1 => 'product_parts.name',
                    2 => 'assign_parts.quantity',
                    3 => 'available_quantity'
                );

            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $requestFilterCountQuery->where('assign_parts.company_id',auth()->user()->company_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $requestFilterCountQuery->where(function ($query) use ($searchVal) {
                    
                    $query->orWhere('product_parts.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('assign_parts.quantity', 'like', '%' . $searchVal . '%');

                });
            } 
        }
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        
        $requestFilterCount = $requestFilterCountQuery->count('assign_parts.id');
        

        $assignPartQuery = AssignPart::select('assign_parts.*','companies.name as company_name','product_parts.name as part_name',
            DB::raw("(assign_parts.quantity - (SELECT COUNT(distinct service_requests.id) FROM service_requests 
                join product_part_service_request on service_requests.id = product_part_service_request.service_request_id 
                                WHERE assign_parts.product_parts_id = product_part_service_request.product_part_id and service_requests.company_id = assign_parts.company_id and service_requests.deleted_at IS NULL 
                                ) )  as available_quantity")
         )
         ->join('companies','assign_parts.company_id','=','companies.id')
         ->join('product_parts','product_parts.id','=','assign_parts.product_parts_id')
         ->whereNull('product_parts.deleted_at')
         ->whereNull('companies.deleted_at')
         ->Where('product_parts.status', 'Active')
         ->Where('companies.status', 'Active')
         ->offset($start)
         ->limit($limit)
         ->orderBy($order,$dir);


        // filter data from table
        if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        {
            if(!empty($request->input('company')))
            {   
                $assignPartQuery->Where('assign_parts.company_id', $request['company']);
            }

            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $assignPartQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere('companies.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('product_parts.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('assign_parts.quantity', 'like', '%' . $searchVal . '%');

                });
                // $assignPartQuery->having('available_quantity', 'like', '%' . $searchVal . '%');
            }
            // fetch total count without any filter
            $countRecord = AssignPart::select('assign_parts.*')
                            ->join('companies','assign_parts.company_id','=','companies.id')
                            ->join('product_parts','product_parts.id','=','assign_parts.product_parts_id')
                            ->whereNull('product_parts.deleted_at')
                            ->whereNull('companies.deleted_at')
                            ->Where('product_parts.status', 'Active')
                            ->Where('companies.status', 'Active')->count('assign_parts.id');
        } 
        else 
        {
            if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            {
                $assignPartQuery->where('assign_parts.company_id',auth()->user()->company_id);
            }
            //Search from table
            if(!empty($request->input('search.value')))
            { 
                $searchVal = $request['search']['value'];
                $assignPartQuery->where(function ($query) use ($searchVal) {

                    $query->orWhere('product_parts.name', 'like', '%' . $searchVal . '%');
                    $query->orWhere('assign_parts.quantity', 'like', '%' . $searchVal . '%');

                });
                // $assignPartQuery->having('available_quantity', 'like', '%' . $searchVal . '%');
            }
            // fetch total count without any filter
            $countRecord = AssignPart::select('assign_parts.*')
                                ->join('companies','assign_parts.company_id','=','companies.id')
                                ->join('product_parts','product_parts.id','=','assign_parts.product_parts_id')
                                ->whereNull('product_parts.deleted_at')
                                ->whereNull('companies.deleted_at')
                                ->Where('product_parts.status', 'Active')
                                ->Where('companies.status', 'Active')
                                ->where('company_id',auth()->user()->company_id)->count('assign_parts.id');
        } 

        
        // echo $assignPartQuery->toSql();exit;
        $assignParts = $assignPartQuery->get();
        // echo "<pre>"; print_r ($assignParts); echo "</pre>"; exit();

        if(!empty($assignParts)){

            foreach ($assignParts as $key => $assignPart) {

                $tableField['checkbox'] = '';
                $tableField['sr_no'] =  $assignPart->id;
                $tableField['company'] = ucfirst($assignPart->company_name);
                $tableField['part_name'] = ucfirst($assignPart->part_name);
                $tableField['quantity'] = $assignPart->quantity;
                $tableField['available_quantity'] = $assignPart->available_quantity;

                $EditButtons = '';
                if (Gate::allows('assign_part_edit')) {
                    $EditButtons = '<a href="'.route('admin.assign_parts.edit',$assignPart->id).'" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>';
                }
                $DeleteButtons = '';
                if (Gate::allows('assign_part_delete')) {
                    $DeleteButtons = '<form action="'.route('admin.assign_parts.destroy',$assignPart->id).'" method="post" onsubmit="return confirm(\'Are you sure ?\');" style="display: inline-block;">

                    <input name="_method" type="hidden" value="DELETE">
                    <input type="hidden"
                               name="_token"
                               value="'.csrf_token().'">
                    <button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
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
     * Show the form for creating new AssignPart.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assign_part_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_parts = \App\ProductPart::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            
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

        // check unique validation for company and part both field
        $validator = Validator::make($request->all(), []);
        $checkExistData = AssignPart::where('company_id', $request->company_id)->where('product_parts_id', $request->product_parts_id)->first();

        if(!empty($checkExistData))
        {   
            return redirect()->back()->withInput(Input::all())->with(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->add('Duplicate', trans('Same part is already assigned to this company.'))

                ));
                exit;
        }
        $assign_part = AssignPart::create($request->all());



        return redirect()->route('admin.assign_parts.index')->with('success','Assign Parts added successfully!');
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
        
        $companies = \App\Company::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $product_parts = \App\ProductPart::where('status','Active')->orderBy('name')->get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
            
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

        // check unique validation for company and part both field
        $validator = Validator::make($request->all(), []);
        $checkExistData = AssignPart::where('company_id', $request->company_id)->where('product_parts_id', $request->product_parts_id)->where('id',"!=", $id)->first();

        if(!empty($checkExistData))
        {   
            return redirect()->back()->withInput(Input::all())->with(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->add('Duplicate', trans('Same part is already assigned to this company.'))

                ));
                exit;
        }
        $assign_part = AssignPart::findOrFail($id);
        $assign_part->update($request->all());



        return redirect()->route('admin.assign_parts.index')->with('success','Assign Parts updated successfully!');
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
