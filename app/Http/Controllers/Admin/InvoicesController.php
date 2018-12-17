<?php

namespace App\Http\Controllers\Admin;

use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoicesRequest;
use App\Http\Requests\Admin\UpdateInvoicesRequest;

// permission plugin
use Spatie\Permission\Models\Role as RolePermission;
use Spatie\Permission\Models\Permission as perm;

class InvoicesController extends Controller
{
    public function __construct()
    {
        // Check permission
        $this->middleware(function ($request, $next) {
            if (! Gate::allows('manageInvoices')) {
                return abort(404);
            }
            return $next($request);
        });
    }  
    /**
     * Display a listing of Invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('invoice_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('invoice_delete')) {
                return abort(401);
            }
            $invoices = Invoice::onlyTrashed()->get();
        } else {
            $invoices = Invoice::all();
        }

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating new Invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('invoice_create')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Invoice::$enum_status;
            
        return view('admin.invoices.create', compact('enum_status', 'companies'));
    }

    /**
     * Store a newly created Invoice in storage.
     *
     * @param  \App\Http\Requests\StoreInvoicesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoicesRequest $request)
    {
        if (! Gate::allows('invoice_create')) {
            return abort(401);
        }
        $invoice = Invoice::create($request->all());



        return redirect()->route('admin.invoices.index');
    }


    /**
     * Show the form for editing Invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('invoice_edit')) {
            return abort(401);
        }
        
        $companies = \App\Company::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $enum_status = Invoice::$enum_status;
            
        $invoice = Invoice::findOrFail($id);

        return view('admin.invoices.edit', compact('invoice', 'enum_status', 'companies'));
    }

    /**
     * Update Invoice in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoicesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoicesRequest $request, $id)
    {
        if (! Gate::allows('invoice_edit')) {
            return abort(401);
        }
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());



        return redirect()->route('admin.invoices.index');
    }


    /**
     * Display Invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('invoice_view')) {
            return abort(401);
        }
        $invoice = Invoice::findOrFail($id);

        return view('admin.invoices.show', compact('invoice'));
    }


    /**
     * Remove Invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('invoice_delete')) {
            return abort(401);
        }
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('admin.invoices.index');
    }

    /**
     * Delete all selected Invoice at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('invoice_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Invoice::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('invoice_delete')) {
            return abort(401);
        }
        $invoice = Invoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        return redirect()->route('admin.invoices.index');
    }

    /**
     * Permanently delete Invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('invoice_delete')) {
            return abort(401);
        }
        $invoice = Invoice::onlyTrashed()->findOrFail($id);
        $invoice->forceDelete();

        return redirect()->route('admin.invoices.index');
    }
}
