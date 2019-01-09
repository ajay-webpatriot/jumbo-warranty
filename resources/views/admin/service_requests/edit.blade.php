@extends('layouts.app')

@section('content')
    <style>
        .lablemargin{
            margin-top: 30px;
        }
        hr{
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }
        .fontweight{
            font-weight: 400!important;
        }
        .fontsize{
            font-size: 12px!important;
        }
    </style>
    <h3 class="page-title">@lang('quickadmin.service-request.title')</h3>
    
    {!! Form::model($service_request, ['method' => 'PUT', 'route' => ['admin.service_requests.update', $service_request->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('service_type', trans('quickadmin.service-request.fields.service-type').'*', ['class' => 'control-label']) !!}
                                    {!! Form::select('service_type', $enum_service_type, old('service_type'), ['class' => 'form-control select2', 'required' => '', 'onchange' => 'requestCharge(this)']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('service_type'))
                                        <p class="help-block">
                                            {{ $errors->first('service_type') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="col-md-6 form-group">
                                    {!! Form::label('created_date', trans('quickadmin.service-request.fields.created_date').':', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                    {!! Form::label('created_date', '08-01-2018', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Request Status -->
                            @if($service_request->status != "New")
                                        {!! Form::label('status', trans('quickadmin.service-request.fields.status').'*', ['class' => 'control-label']) !!}
                                        {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','id' => 'status']) !!}
                                        <p class="help-block"></p>
                                @if($errors->has('status'))
                                        <p class="help-block">
                                            {{ $errors->first('status') }}
                                        </p>
                                @endif
                            @else
                                {!! Form::hidden('status', old('status'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'status']) !!}
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel-group">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a data-toggle="collapse" href="#collapseCompany">Company & Customer</a>
                    </div>

                    <div id="collapseCompany" class="panel-collapse in" role="tabpanel">
                        <div class="panel-body">

                            <div class="row">
                                <!-- Company & Customer -->
                                <div class="col-md-6">
                                    @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'*', ['class' => 'control-label']) !!}
                                            {!! Form::text('company_name', $companyName[0], ['class' => 'form-control', 'placeholder' => 'Company Name','disabled' => '']) !!}
                                            {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('company_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('company_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'required' => '','onchange' => 'requestCharge(this)']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('company_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('company_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12 ">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('customer_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('customer_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                </div>

                                <div class="col-md-6">
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer-address').'', ['class' => 'control-label']) !!}
                                            <div class="custAddress">
                                                @if(count($custAddressData) > 0)
                                                {{$custAddressData->address_1}}
                                                <br/>
                                                {{$custAddressData->address_2}}
                                                <br/>
                                                {{$custAddressData->city}}
                                                <br/>
                                                {{$custAddressData->state." - ".$custAddressData->zipcode}}
                                                @endif
                                            </div>
                                            <p class="help-block"></p>
                                            @if($errors->has('customer_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('customer_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a data-toggle="collapse" href="#collapseServiceCenter">Service Center</a>
                    </div>
                    <div id="collapseServiceCenter" class="panel-collapse in">
                        <div class="panel-body">
                            <div class="row">

                                <!-- Service center -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').'', ['class' => 'control-label']) !!}
                                            {!! Form::select('service_center_id', $service_centers, old('service_center_id'), ['class' => 'form-control select2']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('service_center_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('service_center_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Technician -->
                                <div class="col-md-6">
                                    <div class="row techDiv" {{ ($service_request->service_type == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}
                                            {!! Form::select('technician_id', $technicians, old('technician_id'), ['class' => 'form-control select2','id' => 'technician_id']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('technician_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('technician_id') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @else
                <div class="panel">
                    {!! Form::hidden('service_center_id', old('service_center_id'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'service_center_id']) !!}
                    {!! Form::hidden('technician_id', old('technician_id'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'technician_id']) !!}
                </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading"> <a data-toggle="collapse" href="#collapseCallDetail">Call Detail</a></div>
                    <div id="collapseCallDetail" class="panel-collapse in">
                        <div class="panel-body">

                            <div class="row">
                                <!-- Call type  -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('call_type', trans('quickadmin.service-request.fields.call-type').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('call_type', $enum_call_type, old('call_type'), ['class' => 'form-control select2', 'required' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('call_type'))
                                                <p class="help-block">
                                                    {{ $errors->first('call_type') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Call location -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('call_location', trans('quickadmin.service-request.fields.call-location').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('call_location', $enum_call_location, old('call_location'), ['class' => 'form-control select2', 'required' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('call_location'))
                                                <p class="help-block">
                                                    {{ $errors->first('call_location') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('priority', trans('quickadmin.service-request.fields.priority').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('priority', $enum_priority, old('priority'), ['class' => 'form-control select2', 'required' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('priority'))
                                                <p class="help-block">
                                                    {{ $errors->first('priority') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"> <a data-toggle="collapse" href="#collapseProduct">Product</a></div>
                    <div id="collapseProduct" class="panel-collapse in">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group">
                                        {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'*', ['class' => 'control-label']) !!}
                                        {!! Form::select('product_id', $products, old('product_id'), ['class' => 'form-control select2', 'required' => '', 'onchange' => 'requestCharge(this)']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('product_id'))
                                            <p class="help-block">
                                                {{ $errors->first('product_id') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="partsDiv" {{ ($service_request->service_type != "repair") ? 'style=display:none' : ''}}>
                                        <div class="form-group">
                                            {!! Form::label('parts', trans('quickadmin.service-request.fields.parts').'', ['class' => 'control-label']) !!}
                                            <button type="button" class="btn btn-primary btn-xs" id="selectbtn-parts">
                                                {{ trans('quickadmin.qa_select_all') }}
                                            </button>
                                            <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-parts">
                                                {{ trans('quickadmin.qa_deselect_all') }}
                                            </button>
                                            {!! Form::select('parts[]', $parts, old('parts') ? old('parts') : $service_request->parts->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-parts' ]) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('parts'))
                                                <p class="help-block">
                                                    {{ $errors->first('parts') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('make', trans('quickadmin.service-request.fields.make').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('make', $service_request->make, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('make'))
                                            <p class="help-block">
                                                {{ $errors->first('make') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('model_no', trans('quickadmin.service-request.fields.model-no').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('model_no', $service_request->model_no, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('model_no'))
                                            <p class="help-block">
                                                {{ $errors->first('model_no') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('is_item_in_warrenty', trans('quickadmin.service-request.fields.is-item-in-warrenty').'*', ['class' => 'control-label']) !!}
                                        {!! Form::select('is_item_in_warrenty', $enum_is_item_in_warrenty, old('is_item_in_warrenty'), ['class' => 'form-control select2', 'required' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('is_item_in_warrenty'))
                                            <p class="help-block">
                                                {{ $errors->first('is_item_in_warrenty') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('bill_no', trans('quickadmin.service-request.fields.bill-no').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('bill_no', $service_request->bill_no, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('bill_no'))
                                            <p class="help-block">
                                                {{ $errors->first('bill_no') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('bill_date', trans('quickadmin.service-request.fields.bill-date').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('bill_date',  $service_request->bill_date, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('bill_date'))
                                            <p class="help-block">
                                                {{ $errors->first('bill_date') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('serial_no', trans('quickadmin.service-request.fields.serial-no').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('serial_no',$service_request->serial_no, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('serial_no'))
                                            <p class="help-block">
                                                {{ $errors->first('serial_no') }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('purchase_from', $service_request->purchase_from, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('purchase_from'))
                                            <p class="help-block">
                                                {{ $errors->first('purchase_from') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').'', ['class' => 'control-label']) !!}
                                        {!! Form::select('mop', $enum_mop, $service_request->mop, ['class' => 'form-control select2']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('mop'))
                                            <p class="help-block">
                                                {{ $errors->first('mop') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"> <a data-toggle="collapse" href="#collapseOther">Other</a></div>
                    <div id="collapseOther" class="panel-collapse in">
                        <div class="panel-body">

                            <!-- <div class="row"> -->
                                <!-- <div class="col-xs-12 form-group"> -->
                                    <!-- {--!! Form::label('adavance_amount', trans('quickadmin.service-request.fields.adavance-amount').'', ['class' => 'control-label']) !!--}
                                    {--!! Form::text('adavance_amount', old('adavance_amount'), ['class' => 'form-control', 'placeholder' => '','id' => 'adavance_amount']) !!--} -->
                                    <!-- <p class="help-block"></p>
                                    @if($errors->has('adavance_amount')) -->
                                        <!-- <p class="help-block">
                                            {{-- $errors->first('adavance_amount') --}}
                                        </p>
                                    @endif -->
                                <!-- </div>
                            </div> -->
                           
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::label('complain_details', trans('quickadmin.service-request.fields.complain-details').'', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('complain_details', old('complain_details'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('complain_details'))
                                        <p class="help-block">
                                            {{ $errors->first('complain_details') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('completion_date', trans('quickadmin.service-request.fields.completion-date').'*', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                    {!! Form::text('completion_date', old('completion_date'), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
                                        <label class="input-group-addon btn" for="completion_date">
                                            <span class="fa fa-calendar"></span>
                                        </label>
                                    </div>
                                    <p class="help-block"></p>
                                    @if($errors->has('completion_date'))
                                        <p class="help-block">
                                            {{ $errors->first('completion_date') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="row serviceChargeDiv" {{ ($service_request->service_type != "repair") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').'', ['class' => 'control-label lablemargin']) !!}

                                            <!-- service charge value label -->
                                            {!! Form::label('', $service_request->service_charge, ['class' => 'control-label lablemargin pull-right','readonly' => '','id' => 'lbl_service_charge']) !!}

                                            <!-- service charge hidden field -->
                                            {!! Form::hidden('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '','id' => 'service_charge', 'readonly' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('service_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('service_charge') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                   
                                    <div class="row installationChargeDiv" {{ ($service_request->service_type != "installation") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').':', ['class' => 'control-label lablemargin']) !!}
                                            
                                            <!-- installation charge value label -->
                                            {!! Form::label('', $service_request->installation_charge, ['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge']) !!}
                                            
                                            <!-- installation charge hidden field -->
                                            {!! Form::hidden('installation_charge', $service_request->installation_charge, ['class' => 'form-control', 'placeholder' => '', 'readonly' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('installation_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('installation_charge') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {!! Form::label('additional_charges', trans('quickadmin.service-request.fields.additional-charges').':', ['class' => 'control-label']) !!}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    {!! Form::label('charges_for', trans('quickadmin.service-request.fields.charges_for').'', ['class' => 'control-label fontweight fontsize']) !!}
                                                
                                                    {!! Form::text('additional_charges_title','for installation ' , ['class' => 'form-control', 'placeholder' => 'Charges for']) !!}
                                                        <p class="help-block"></p>
                                                        @if($errors->has('additional_charges'))
                                                            <p class="help-block">
                                                                {{ $errors->first('additional_charges') }}
                                                            </p>
                                                        @endif
                                                </div>

                                                <div class="col-sm-4">
                                                    {!! Form::label('amount', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !!}

                                                    {!! Form::text('additional_charges', $service_request->additional_charges, ['class' => 'form-control', 'placeholder' => 'Amount', 'onkeyup' => 'totalServiceAmount()', 'id' => 'additional_charges']) !!}

                                                    <p class="help-block"></p>
                                                    @if($errors->has('additional_charges'))
                                                        <p class="help-block">
                                                            {{ $errors->first('additional_charges') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr/>

                                    <div class="row">
                                        <div class="col-md-12">
                                            {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').':', ['class' => 'control-label']) !!}

                                            <!-- total amount value label -->
                                            {!! Form::label('totalamount',$service_request->amount, ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !!}

                                            <!-- total amount hidden field -->
                                            {!! Form::hidden('amount', old('amount'), ['class' => 'form-control', 'placeholder' => '','id' => 'amount', 'readonly' => '']) !!}

                                            {!! Form::hidden('km_distance', old('km_distance'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_distance']) !!}
                                            {!! Form::hidden('km_charge', old('km_charge'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_charge']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('amount'))
                                                <p class="help-block">
                                                    {{ $errors->first('amount') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('note', trans('quickadmin.service-request.fields.note').'', ['class' => 'control-label']) !!}
                                    <!-- {!! Form::text('note', old('note'), ['class' => 'form-control', 'placeholder' => '']) !!} -->

                                    {!! Form::textarea('note', old('note'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                                    
                                    <p class="help-block"></p>
                                    @if($errors->has('note'))
                                        <p class="help-block">
                                            {{ $errors->first('note') }}
                                        </p>
                                    @endif
                                </div>
                                @if($service_request->status == "Closed")
                                <div class="col-xs-12 form-group pull-right">
                                     
                                    <a href="{{ route('admin.service_request.invoice',[$service_request->id]) }}" class="btn btn-xl btn-primary pull-right">View Invoice</a>
                                </div> 
                                @endif
                            </div>
                            
                        </div>
                    </div>
                </div>    
            </div> 
            
        </div>
    </div>

   <!-- Form Buttons -->
    <div class="row">
        <div class="col-md-12 form-group">
            {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger', 'id' => 'btnUpdate']) !!}
            <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
            {!! Form::close() !!}
        </div>
    </div>
    
    <div class="panel panel-default">
       

        <div class="panel-body table-responsive">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                
            <li role="presentation" class="active"><a href="#service_request" aria-controls="service_request" role="tab" data-toggle="tab">Service Request Log</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                
                <div role="tabpanel" class="tab-pane active" id="service_request">
                <table class="table table-bordered table-striped {{ count($service_request_logs) > 0 ? 'datatable' : '' }}">
                    <thead>
                        <tr>
                            <th>Sr no.</th>
                            <th>@lang('quickadmin.service-request-log-view.fields.action')</th>
                            <th>@lang('quickadmin.service-request-log-view.fields.action-taken-by')</th>
                            <th>@lang('quickadmin.service-request-log-view.fields.date-time')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($service_request_logs) > 0)
                            @foreach ($service_request_logs as $service_request_log)
                                <tr data-entry-id="{{ $service_request_log->id }}">
                                    <td field-key='serial_no'>{{ $no++ }}</td>
                                    <td field-key='name'>{{ $service_request_log->action_made or '' }}</td>
                                    <td field-key='email'>{{ $service_request_log->user->name or '' }}</td>
                                    <td field-key='created_at'>{{ $service_request_log->created_at or '' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="32">@lang('quickadmin.qa_no_entries_in_table')</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            if("{{$service_request->status}}" == "Closed")
            {
                $("input").prop("disabled", true);
                $("select").prop("disabled", true);
                $("textarea").prop("disabled", true);
                $("#btnUpdate").hide();
            }
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });

            
            if({{auth()->user()->role_id}} == SERVICE_ADMIN_ROLE_ID)
            {
                // disabled all field except technician and status for service admin
                $("input[type=text]").prop("readonly", true);
                $("textarea").prop("readonly", true);
                $("select").prop("disabled", true);
                $("#technician_id").prop("disabled", false);
                $("#status").prop("disabled", false);
            }
            else if({{auth()->user()->role_id}} == TECHNICIAN_ROLE_ID)
            {
                // disabled all field except charges related field, parts and status for technician
                $("input[type=text]").prop("readonly", true);
                $("textarea").prop("readonly", true);
                $("select").prop("disabled", true);

                $("#adavance_amount").prop("readonly", false);
                $("#service_charge").prop("readonly", false);
                $("#service_tag").prop("readonly", false);
                $("#additional_charges").prop("readonly", false);
                $("#amount").prop("readonly", false);

                $("#selectall-parts").prop("disabled", false);
                $("#status").prop("disabled", false);

            }
            // removed disabled attr of select on form submit to store exusting value
            $('form').bind('submit', function() {
                $(this).find('select:disabled').removeAttr('disabled');
            });
        });
    </script>
            
    <script>
        $("#selectbtn-parts").click(function(){
            $("#selectall-parts > option").prop("selected","selected");
            $("#selectall-parts").trigger("change");
        });
        $("#deselectbtn-parts").click(function(){
            $("#selectall-parts > option").prop("selected","");
            $("#selectall-parts").trigger("change");
        });
    </script>
@stop