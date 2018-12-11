<?php

namespace App\Http\Controllers\Api\V1;

use App\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoicesRequest;
use App\Http\Requests\Admin\UpdateInvoicesRequest;

class InvoicesController extends Controller
{
    public function index()
    {
        return Invoice::all();
    }

    public function show($id)
    {
        return Invoice::findOrFail($id);
    }

    public function update(UpdateInvoicesRequest $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());
        

        return $invoice;
    }

    public function store(StoreInvoicesRequest $request)
    {
        $invoice = Invoice::create($request->all());
        

        return $invoice;
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return '';
    }
}
