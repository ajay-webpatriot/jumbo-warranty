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
    <!-- <h3 class="page-title">@lang('quickadmin.service-request.title')</h3> -->
    
    {!! Form::model($service_request, ['method' => 'PUT', 'route' => ['admin.service_requests.update', $service_request->id], 'id' => 'formServiceRequest']) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.formTitle')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::hidden('loggedUser_role_id',auth()->user()->role_id, ['class' => 'form-control', 'placeholder' => '','id' => 'loggedUser_role_id']) !!}
                    
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
                                    {!! Form::label('created_date', App\Helpers\CommonFunctions::setDateFormat($service_request->created_at), ['class' => 'control-label lablemargin fontweight','readonly' => '']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Request Status -->
                            @if($service_request->status == "New")
                                
                                {!! Form::hidden('status', old('status'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'status']) !!}
                            @else
                                {!! Form::label('status', trans('quickadmin.service-request.fields.status').'*', ['class' => 'control-label']) !!}
                                {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','id' => 'status']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('status'))
                                        <p class="help-block">
                                            {{ $errors->first('status') }}
                                        </p>
                                @endif
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
                                <!-- Company & Customer  -->
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                    <!-- company will not visible to service center admin and technician -->
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
                                @else
                                    {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control']) !!}
                                @endif
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
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                <div class="col-md-6">
                                    <!--  added condition to set layput when company is not visible -->
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer-address').'', ['class' => 'control-label']) !!}
                                            <div class="custAddress">
                                                @if(!empty($custAddressData))
                                                {{$custAddressData->address_1}}
                                                <br/>
                                                @if(!empty($service_request->customer->address_2))
                                                {{$service_request->customer->address_2}}
                                                <br/>
                                                @endif
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
                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))

                <!-- Service center and technician will not be visible to company user and admin -->
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
                            <div class="row suggestedServiceCenterDiv" style="display: none;">
                                <!-- suggested service center -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.suggested-service-center').'', ['class' => 'control-label']) !!}
                                            <div id="suggestedHTML"></div>
                                            <p class="help-block"></p>
                                            @if($errors->has('service_center_id'))
                                            <p class="help-block">
                                                {{ $errors->first('service_center_id') }}
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

                                    <div class="partsDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
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
                                        
                                        <div class="input-group">
                                        {!! Form::text('bill_date', old('bill_date'), ['class' => 'form-control date', 'placeholder' => '']) !!}
                                        <label class="input-group-addon btn" for="completion_date">
                                            <span class="fa fa-calendar"></span>
                                        </label>
                                    </div>
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
                                    <div class="row serviceChargeDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').':', ['class' => 'control-label lablemargin']) !!}

                                            <!-- service charge value label -->
                                            {!! Form::label('', number_format($service_request->service_charge,2), ['class' => 'control-label lablemargin pull-right','readonly' => '','id' => 'lbl_service_charge']) !!}

                                            <!-- service charge hidden field -->
                                            {!! Form::hidden('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '','id' => 'service_charge', 'readonly' => '']) !!}
                                            <!-- <p class="help-block"></p>
                                            @if($errors->has('service_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('service_charge') }}
                                                </p>
                                            @endif -->
                                        </div>
                                    </div>
                                   
                                    <div class="row installationChargeDiv" {{ ($service_request->service_type == "repair") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').':', ['class' => 'control-label lablemargin']) !!}
                                            
                                            <!-- installation charge value label -->
                                            {!! Form::label('', number_format($service_request->installation_charge,2), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge']) !!}
                                            
                                            <!-- installation charge hidden field -->
                                            {!! Form::hidden('installation_charge', $service_request->installation_charge, ['class' => 'form-control', 'placeholder' => '', 'readonly' => '']) !!}
                                            <!-- <p class="help-block"></p>
                                            @if($errors->has('installation_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('installation_charge') }}
                                                </p>
                                            @endif -->
                                        </div>
                                    </div>
                                    <!-- @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                {!! Form::label('lbltransportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                                </div>
                                                <!-- transportation amount value label -->
                                                <!--<div class="col-sm-4">
                                                {!! Form::text('transportation_charge',number_format(($service_request->km_charge),2,'.',''), ['class' => 'form-control pull-right', 'placeholder' => 'Charges for', 'id' => 'transportation_charge', 'onkeypress' => 'return checkIsDecimalNumber(this,event)']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                            {!! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_trans_amount']) !!}
                                        </div>
                                    </div>
                                    @elseif($service_request->km_distance > 0)
                                    <div class="row serviceChargeDiv">
                                        <div class="col-md-12">
                                                {!! Form::label('lbltransportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                            
                                                <!-- transportation amount value label -->
                                               <!-- {!! Form::label('',number_format(($service_request->km_distance * $service_request->km_charge),2), ['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount']) !!}

                                                {!! Form::hidden('transportation_charge',($service_request->km_distance * $service_request->km_charge), ['class' => 'form-control', 'placeholder' => '','id' => 'transportation_charge']) !!}

                                        </div>
                                        <div class="col-md-12">

                                            {!! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_trans_amount']) !!}
                                        </div>
                                    </div>
                                    @endif -->

                                    <div class="row transportationDiv" {{ ($service_request->transportation_charge <= 0) ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                {!! Form::label('lbltransportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                                </div>
                                                <!-- transportation amount value label -->
                                                <div class="col-sm-4 transportationField">
                                                    @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))

                                                    {!! Form::text('transportation_charge',number_format($service_request->transportation_charge,2,'.',''), ['class' => 'form-control pull-right', 'placeholder' => 'Charges for', 'id' => 'transportation_charge', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}

                                                    @else

                                                    {!! Form::label('',number_format($service_request->transportation_charge,2), ['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount']) !!}
                                                
                                                    {!! Form::hidden('transportation_charge',($service_request->transportation_charge), ['class' => 'form-control', 'placeholder' => '','id' => 'transportation_charge']) !!}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                            {!! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge']) !!}
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
                                                
                                                    {!! Form::text('additional_charges_title',$additional_charge_title, ['class' => 'form-control', 'placeholder' => 'Charges for', 'id' => 'additional_charges_title']) !!}
                                                        <p class="help-block"></p>
                                                        @if($errors->has('additional_charges_title'))
                                                            <p class="help-block">
                                                                {{ $errors->first('additional_charges_title') }}
                                                            </p>
                                                        @endif
                                                </div>

                                                <div class="col-sm-4">
                                                    {!! Form::label('amount', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !!}

                                                    {!! Form::text('additional_charges', ($service_request->additional_charges > 0)?$service_request->additional_charges:'', ['class' => 'form-control', 'placeholder' => 'Amount', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()', 'id' => 'additional_charges']) !!}

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
                                            {!! Form::label('totalamount',number_format($service_request->amount,2), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !!}

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
    @if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID') && !$service_request->is_accepted)
        <div class="row">
            <div class="col-md-12 form-group">
                <a href="{{ route('admin.service_request.accept',[$service_request->id]) }}" 
                     class="btn btn-danger">
                @lang('quickadmin.qa_accept')</a>
                <a href="{{ route('admin.service_request.reject',[$service_request->id]) }}" class="btn btn-default">@lang('quickadmin.qa_reject')</a>
                {!! Form::close() !!}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12 form-group">
                {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger', 'id' => 'btnUpdate']) !!}
                <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
                {!! Form::close() !!}
            </div>
        </div>
    @endif
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
                                    <td field-key='created_at'>{{ (!empty($service_request_log->created_at))?App\Helpers\CommonFunctions::setDateTimeFormat($service_request_log->created_at) : '' }}</td>
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

                // $("#adavance_amount").prop("readonly", false);
                // $("#service_charge").prop("readonly", false);
                // $("#service_tag").prop("readonly", false);
                $("#additional_charges_title").prop("readonly", false);
                $("#additional_charges").prop("readonly", false);
                // $("#amount").prop("readonly", false);

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