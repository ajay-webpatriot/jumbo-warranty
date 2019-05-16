<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompaniesRequest;
use App\Http\Requests\Admin\UpdateCompaniesRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

use Validator;
use Illuminate\Support\Facades\Input;
class CompaniesController extends Controller
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
     * Display a listing of Company.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('company_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('company_delete')) {
                return abort(401);
            }
            $companies = Company::onlyTrashed()->get();
        } else {
            $companies = Company::all();
        }

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating new Company.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('company_create')) {
            return abort(401);
        }        $enum_company_status = Company::$enum_status;
            
        return view('admin.companies.create', compact('enum_company_status'));
    }

    /**
     * Store a newly created Company in storage.
     *
     * @param  \App\Http\Requests\StoreCompaniesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('company_create')) {
            return abort(401);
        }
        $validator = Validator::make($request->all(), [

            'name' => 'required|unique:companies,name,"",id,deleted_at,NULL',
            'installation_charge' => 'required|numeric',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            // 'status' => 'required',

        ]);
        if ($validator->fails()) {

            if($request->ajax())
            {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
                ));
            }
            else
            {
                return redirect()->back()->withInput(Input::all())->with(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()

                ));
                exit;
            }
        
        }

        
        $company = Company::create($request->all());

        if($request->ajax())
        {
            // get company details and return in ajax response

            $companyOptions="<option value=''>".trans('quickadmin.qa_please_select')."</option>";

            // $companies = \App\Company::where('status','Active')->orderBy('name')->get();
            $companies = \App\Company::where('status','Active')->orderBy('id','DESC')->get();
            if(count($companies) > 0)
            {
                foreach($companies as $key => $value)
                {     
                    $selected = ''; 
                    if($key == 0){
                        $selected = 'selected';
                    }              
                    $companyOptions.="<option value='".$value->id."' $selected>".$value->name."</option>";   
                }   
            }
            return response()->json(array(
                    'success' => true,
                    'message' => 'Company created successfully!',
                    'companyOptions' => $companyOptions,
                    'selectedCompany' => $company->id
                ));
        }
        else{
            return redirect()->route('admin.companies.index')->with('success','Company created successfully!');
        }    
        
    }


    /**
     * Show the form for editing Company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('company_edit')) {
            return abort(401);
        }        $enum_status = Company::$enum_status;
            
        $company = Company::findOrFail($id);

        // $companyCredit=\App\ServiceRequest::select('sum(amount)')->where('company_id',$id)->groupBy('company_id')->get();

        $companyCredit=\App\ServiceRequest::groupBy('company_id')
       ->selectRaw('sum(amount) as used_credit')
       ->where('company_id',$id)
       ->get()->first();

       // echo $companyCredit->used_credit;exit;
        $available_credit=($companyCredit) ? ($company->credit - $companyCredit->used_credit) : ($company->credit)?$company->credit:0;
        return view('admin.companies.edit', compact('company', 'available_credit', 'enum_status'));
    }

    /**
     * Update Company in storage.
     *
     * @param  \App\Http\Requests\UpdateCompaniesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompaniesRequest $request, $id)
    {
        if (! Gate::allows('company_edit')) {
            return abort(401);
        }
        $company = Company::findOrFail($id);

        // check company is assigned in service request
        if($request['status'] == "Inactive"){
            $validator = Validator::make($request->all(), []);
            $checkExistData = ServiceRequest::where('company_id', $id)->get();

            if(count($checkExistData) > 0)
            {   
                return redirect()->back()->withInput(Input::all())->with(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->add('Assigned', trans('This company is already assigned in service request.'))

                    ));
                    exit;
            }
        }

        $company->update($request->all());



        return redirect()->route('admin.companies.index')->with('success','Company updated successfully!');
    }


    /**
     * Display Company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('company_view')) {
            return abort(401);
        }
        $service_requests = \App\ServiceRequest::where('company_id', $id)->get();$invoices = \App\Invoice::where('company_id', $id)->get();$assign_products = \App\AssignProduct::where('company_id', $id)->get();$assign_parts = \App\AssignPart::where('company_id', $id)->get();$users = \App\User::where('company_id', $id)->get();$customers = \App\Customer::where('company_id', $id)->get();

        $company = Company::findOrFail($id);

        $AssignPart = new \App\AssignPart();
        foreach ($assign_parts as $key => $value) {
            $usedParts=$AssignPart->getRequestedServiceParts($value->product_parts_id,$value->company_id);// get quantity of used parts in service requests
            $value['availableQuantity']=$value->quantity-$usedParts;
        }
        return view('admin.companies.show', compact('company', 'service_requests', 'invoices', 'assign_products', 'assign_parts', 'users', 'customers'));
    }


    /**
     * Remove Company from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('company_delete')) {
            return abort(401);
        }
        $company = Company::findOrFail($id);

        // check company is assigned in service request
        $checkExistData = ServiceRequest::where('company_id', $id)->get();
        if(count($checkExistData) > 0)
        {   
            return  redirect()->route('admin.companies.index')->withErrors('This company is already assigned in service request.');
            exit;
        }

        $company->delete();

        return redirect()->route('admin.companies.index');
    }

    /**
     * Delete all selected Company at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('company_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Company::whereIn('id', $request->input('ids'))->get();

            $not_deleted=0;
            foreach ($entries as $entry) {
                // check company is assigned in service request
                $checkExistData = ServiceRequest::where('company_id', $entry->id)->get();
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
                redirect()->route('admin.companies.index')->withErrors('Some company is already assigned in service request, so it is not deleted.');
            }
        }
    }


    /**
     * Restore Company from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('company_delete')) {
            return abort(401);
        }
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->restore();

        return redirect()->route('admin.companies.index');
    }

    /**
     * Permanently delete Company from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('company_delete')) {
            return abort(401);
        }
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->forceDelete();

        return redirect()->route('admin.companies.index');
    }
}
