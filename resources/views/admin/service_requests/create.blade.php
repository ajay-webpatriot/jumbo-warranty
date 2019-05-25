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
    @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
        @if($ProductAssignMessage != '')
            <div class="alert alert-danger alert-dismissable">
                {{ $ProductAssignMessage }}
            </div>
        @endif
    @endif
    <!-- <h3 class="page-title">@lang('quickadmin.service-request.title')</h3> -->
    {!! Form::open(['method' => 'POST', 'route' => ['admin.service_requests.store'], 'id' => 'formServiceRequest']) !!}
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.formTitle')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    {!! Form::hidden('loggedUser_role_id',auth()->user()->role_id, ['class' => 'form-control', 'placeholder' => '','id' => 'loggedUser_role_id']) !!}

                    {!! Form::label('service_type', trans('quickadmin.service-request.fields.service-type').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('service_type', $enum_service_type, old('service_type'), ['class' => 'form-control select2', 'required' => '','onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('service_type'))
                    <p class="help-block">
                        {{ $errors->first('service_type') }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="panel-group">

                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseCompany">
                        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                            Company & Customer
                        @else
                            Customer
                        @endif
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseCompany" class="panel-collapse collapse in" role="tabpanel">
                        <div class="panel-body">
                            <div class="row">
                                <!-- Company & Customer -->
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                                    <!-- company will not visible to company admin,user, service center admin and technician -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-10 col-xs-9">
                                                {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'*', ['class' => 'control-label']) !!}
                                                {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'required' => '','onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
                                                <p class="help-block"></p>
                                                @if($errors->has('company_id'))
                                                <p class="help-block">
                                                    {{ $errors->first('company_id') }}
                                                </p>
                                                @endif
                                                <span class="text-danger" id="product_error"></span>
                                            </div>
                                            <div class="col-sm-2 col-xs-3">
                                                <!-- <button class="btn btn-success btn-quick-add" data-toggle="modal" data-target="#company-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                                <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('company')" title="Add Company"><i class="fa fa-plus"></i></button>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-1">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <button class="btn btn-success" data-toggle="modal" data-target="#company-modal" type="button" style="margin-top: 23px;">+</button>
                                            </div>
                                        </div>
                                    </div> -->

                                @else
                                    {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control', 'id' => 'company_id']) !!}
                                @endif
                                <div class="col-md-6">
                                    @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                    <div class="row custDiv">
                                        <div class="col-sm-10 col-xs-9">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('customer_id'))
                                            <p class="help-block">
                                                {{ $errors->first('customer_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <!-- <button class="btn btn-success btn-quick-add" data-toggle="modal" id="quick_add_customer" data-target="#customer-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                            <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('customer')" title="Add Customer"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    @else
                                    <div class="row custDiv" style="display: none;"> 
                                        <div class="col-sm-10 col-xs-9">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('customer_id', array('' => trans('quickadmin.qa_please_select')), old('customer_id'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('customer_id'))
                                            <p class="help-block">
                                                {{ $errors->first('customer_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <!-- <button class="btn btn-success btn-quick-add" data-toggle="modal" data-target="#customer-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                            <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('customer')" title="Add Customer"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div> 
                                    @endif  
                                        
                                </div>
                            </div>
                            <div class="row">
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                                <div class="col-md-6">
                                    <!--  added condition to set layout when company is not visible -->
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="row custDiv" style="display: none;">
                                        <div class="col-xs-12">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer-address').'', ['class' => 'control-label']) !!}
                                            <div class="custAddress">
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
                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseServiceCenter"> Service Center
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseServiceCenter" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">

                                <!-- Service center -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-10 col-xs-9">
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').'', ['class' => 'control-label']) !!}
                                            {!! Form::select('service_center_id', $service_centers, old('service_center_id'), ['class' => 'form-control select2','style' => 'width:100%']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('service_center_id'))
                                            <p class="help-block">
                                                {{ $errors->first('service_center_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <!-- <button class="btn btn-success btn-quick-add" data-toggle="modal" data-target="#service-center-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                            <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('service_center')" title="Add Service Center"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Technician -->
                                <div class="col-md-6">
                                    <div class="row techDiv"  style="display: none;">
                                        <div class="col-sm-10 col-xs-9">
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}
                                            {!! Form::select('technician_id', array('' => trans('quickadmin.qa_please_select')), old('technician_id'), ['class' => 'form-control select2','style' => 'width:100%']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('technician_id'))
                                            <p class="help-block">
                                                {{ $errors->first('technician_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <!-- <button class="btn btn-success btn-quick-add" data-toggle="modal" data-target="#technician-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                            <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('technician')" title="Add Technician"><i class="fa fa-plus"></i></button>
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
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseCallDetail">Call Detail
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseCallDetail" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">
                                <!-- Call type  -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('call_type', trans('quickadmin.service-request.fields.call-type').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('call_type', $enum_call_type, old('call_type'), ['class' => 'form-control select2', 'required' => '','id' => 'call_type','style' => 'width:100%']) !!}
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
                                            {!! Form::select('call_location', $enum_call_location, old('call_location'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
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
                                            {!! Form::select('priority', $enum_priority, old('priority'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
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
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseProduct">
                        product
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                  <div id="collapseProduct" class="panel-collapse collapse in">
                      <div class="panel-body">  
                        <div class="row">
                            <div class="col-md-6">

                                @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                <div class="form-group">
                                    {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'*', ['class' => 'control-label']) !!}
                                    {!! Form::select('product_id', $products, old('product_id'), ['class' => 'form-control select2', 'required' => '','onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('product_id'))
                                    <p class="help-block">
                                        {{ $errors->first('product_id') }}
                                    </p>
                                    @endif
                                </div>
                                @else
                                <div class="form-group">
                                    {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'*', ['class' => 'control-label']) !!}
                                    {!! Form::select('product_id', array('' => trans('quickadmin.qa_please_select')), old('product_id'), ['class' => 'form-control select2', 'required' => '','onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('product_id'))
                                    <p class="help-block">
                                        {{ $errors->first('product_id') }}
                                    </p>
                                    @endif
                                </div>
                                @endif

                            
                                <div class="partsDiv" style="display: none;">

                                    @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                    <div class="form-group">
                                        {!! Form::label('parts', trans('quickadmin.service-request.fields.parts').'', ['class' => 'control-label']) !!}
                                        <button type="button" class="btn btn-primary btn-xs" id="selectbtn-parts">
                                            {{ trans('quickadmin.qa_select_all') }}
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-parts">
                                            {{ trans('quickadmin.qa_deselect_all') }}
                                        </button>
                                        {!! Form::select('parts[]', $parts, old('parts'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-parts','style' => 'width:100%']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('parts'))
                                        <p class="help-block">
                                            {{ $errors->first('parts') }}
                                        </p>
                                        @endif
                                    </div>
                                    @else
                                    <div class="form-group">
                                        {!! Form::label('parts', trans('quickadmin.service-request.fields.parts').'', ['class' => 'control-label']) !!}
                                        <button type="button" class="btn btn-primary btn-xs" id="selectbtn-parts">
                                            {{ trans('quickadmin.qa_select_all') }}
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-parts">
                                            {{ trans('quickadmin.qa_deselect_all') }}
                                        </button>
                                        {!! Form::select('parts[]', array(), old('parts'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-parts','style' => 'width:100%']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('parts'))
                                        <p class="help-block">
                                            {{ $errors->first('parts') }}
                                        </p>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('make', trans('quickadmin.service-request.fields.make').'', ['class' => 'control-label']) !!}
                                    {!! Form::text('make', old('make'), ['class' => 'form-control', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('make'))
                                    <p class="help-block">
                                        {{ $errors->first('make') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('model_no', trans('quickadmin.service-request.fields.model-no').'', ['class' => 'control-label']) !!}
                                    {!! Form::text('model_no', old('model_no'), ['class' => 'form-control', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('model_no'))
                                    <p class="help-block">
                                        {{ $errors->first('model_no') }}
                                    </p>
                                    @endif
                                
                                </div>
                                <div class="form-group">
                                    {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').'', ['class' => 'control-label']) !!}
                                    {!! Form::text('purchase_from', old('purchase_from'), ['class' => 'form-control', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('purchase_from'))
                                    <p class="help-block">
                                        {{ $errors->first('purchase_from') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group warrantycardnumber" style="display: none;">
                                    {!! Form::label('warranty_card_number', trans('quickadmin.service-request.fields.warranty-card-number').'*', ['class' => 'control-label']) !!}
                                    {!! Form::text('warranty_card_number','', ['class' => 'form-control', 'placeholder' => '','id' => 'warrantyCardNumber']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('warranty_card_number'))
                                    <p class="help-block">
                                        {{ $errors->first('warranty_card_number') }}
                                    </p>
                                    @endif
                                </div>
                                <!-- <div class="form-group"> -->
                                    {{-- !! Form::label('is_item_in_warrenty', trans('quickadmin.service-request.fields.is-item-in-warrenty').'*', ['class' => 'control-label']) !! --}}
                                    {{-- !! Form::select('is_item_in_warrenty', $enum_is_item_in_warrenty, old('is_item_in_warrenty'), ['class' => 'form-control select2', 'required' => '']) !! --}}
                                    <!-- <p class="help-block"></p> -->
                                    {{-- @if($errors->has('is_item_in_warrenty')) --}}
                                    <!-- <p class="help-block"> -->
                                        {{-- $errors->first('is_item_in_warrenty') --}}
                                    <!-- </p> -->
                                    {{-- @endif --}}
                                <!-- </div> -->
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bill_no', trans('quickadmin.service-request.fields.bill-no').'', ['class' => 'control-label']) !!}
                                    {!! Form::text('bill_no', old('bill_no'), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                                        <label class="input-group-addon btn" for="bill_date">
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
                                    {!! Form::text('serial_no', old('serial_no'), ['class' => 'form-control', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('serial_no'))
                                    <p class="help-block">
                                        {{ $errors->first('serial_no') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('mop', $enum_mop, old('mop'), ['class' => 'form-control select2','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('mop'))
                                    <p class="help-block">
                                        {{ $errors->first('mop') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group onlineserialnumber" style="display: none;">
                                    {!! Form::label('online_serial_number', trans('quickadmin.service-request.fields.online-serial-number').'*', ['class' => 'control-label']) !!}
                                    {!! Form::text('online_serial_number','', ['class' => 'form-control', 'placeholder' => '','id' => 'onlineSerialNumber']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('online_serial_number'))
                                    <p class="help-block">
                                        {{ $errors->first('online_serial_number') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseOther">
                        Other
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseOther" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <!-- <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('adavance_amount', trans('quickadmin.service-request.fields.adavance-amount').'', ['class' => 'control-label']) !!}
                                    {!! Form::text('adavance_amount', old('adavance_amount'), ['class' => 'form-control', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('adavance_amount'))
                                    <p class="help-block">
                                        {{ $errors->first('adavance_amount') }}
                                    </p>
                                    @endif
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-xs-12">
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
                            @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
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
                                    <div class="row serviceChargeDiv" style="display: none;">

                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').':', ['class' => 'control-label lablemargin']) !!}
                                                </div> 
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {{-- !! Form::label('', old('service_charge'), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_service_charge']) !! --}}

                                                            {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> 0.00', ['class' => 'control-label lablemargin pull-right','id' => 'lbl_service_charge'])) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-xs-12"> -->
                                            {{-- !! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').'', ['class' => 'control-label lablemargin']) !! --}}

                                            <!-- service charge value label -->
                                            {{-- !! Form::label('', old('service_charge'), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_service_charge']) !! --}}

                                            <!-- service charge hidden field -->
                                            {{-- !! Form::hidden('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '','readonly' => '']) !! --}}
                                            <!-- <p class="help-block"></p>
                                            @if($errors->has('service_charge'))
                                            <p class="help-block">
                                                {{ $errors->first('service_charge') }}
                                            </p>
                                            @endif -->
                                        <!-- </div> -->
                                    </div>
                                   
                                    <div class="row installationChargeDiv">

                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').':', ['class' => 'control-label lablemargin']) !!}
                                                </div> 
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {{-- !! Form::label('0.00', old('installation_charge'), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge']) !! --}}

                                                            {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> 0.00',['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge'])) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-xs-12"> -->
                                            {{-- !! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').'', ['class' => 'control-label lablemargin']) !! --}}
                                            
                                            <!-- installation charge value label -->
                                            {{-- !! Form::label('', old('installation_charge'), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge']) !! --}}
                                            
                                            <!-- installation charge hidden field -->
                                            {{-- !! Form::hidden('installation_charge', old('installation_charge'), ['class' => 'form-control', 'placeholder' => '', 'readonly' => '']) !! --}}

<!-- 
                                            <p class="help-block"></p>
                                            @if($errors->has('installation_charge'))
                                            <p class="help-block">
                                                {{ $errors->first('installation_charge') }}
                                            </p>
                                            @endif -->
                                        <!-- </div> -->
                                    </div>
                                    <div class="row transportationDiv" style="display: none;">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('lbltransportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                                </div>
                                                <!-- transportation amount value label -->
                                                <div class="col-md-4 col-xs-5 transportationField">
                                                    @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))

                                                    <div class="input-group">
                                                        <label class="input-group-addon" for="transportation_charge">
                                                            <span class="fa fa-rupee"></span>
                                                        </label>
                                                        {!! Form::text('transportation_charge','', ['class' => 'form-control text-right', 'placeholder' => 'Charges for', 'id' => 'transportation_charge', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}
                                                    </div>

                                                    @else

                                                    {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> ',['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount'])) !!}

                                                    {{-- !! Form::label('','', ['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount']) !! --}}
                                                
                                                    {{-- !! Form::hidden('transportation_charge','', ['class' => 'form-control', 'placeholder' => '','id' => 'transportation_charge']) !! --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                        {!! Html::decode(Form::label('', '( <i class="fa fa-rupee"></i> '.number_format($km_charge,2).' per km )',['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge'])) !!}
                                        
                                        {{-- !! Form::label('', '('.number_format($km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge']) !! --}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    {!! Form::label('additional_charges', trans('quickadmin.service-request.fields.additional-charges').':', ['class' => 'control-label']) !!}
                                                </div>
                                            </div>

                                            <div class="row  existingAdditional_charge_for_1">
                                                <div class="col-md-8 col-xs-7">
                                                    <div class="form-group">
                                                        <select class="form-control multiple_Additional_charge_for" id="Additional_charge_for_existing-1" style="width:100%" name="existingAdditional_charge_for[]">
                                                        <?php
                                                            foreach ($pre_additional_charge_array as $key => $value) {
                                                                if($key == 0){
                                                                    $key = '';
                                                                }
                                                        ?>  
                                                        <option value="{{$key}}">{{ $value }} </option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                        {{-- !! Form::select('existingAdditional_charge_for[]', $pre_additional_charge_array, old('existingAdditional_charge_for'), ['class' => 'form-control','id' => 'Additional_charge_for_existing-1', 'required' => '','min' => '1','style' => 'width:100%']) !! --}}
                                                        <p class="error-block_1 text-danger"></p>
                                                    </div>
                                                  
                                                </div>

                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {{-- !! Form::label('additional_charges', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !! --}}

                                                            <div class="input-group">
                                                                <label class="input-group-addon" for="existingAdditional_charge">
                                                                    <span class="fa fa-rupee"></span>
                                                                </label>
                                                                {!! Form::text('existingAdditional_charge[]', old('existingAdditional_charge[]'), ['class' => 'form-control text-right existingAdditional_charge', 'placeholder' => 'Amount','id' => 'existingAdditional_charge_1','onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}
                                                            </div>
                                                            <p class="error-amount-block_1 text-danger"></p>

                                                            <a href="javascript:void(0);" class="text-danger pull-right removelink" onclick='removeAdditionalChargeFor(1);'>Remove</a>
                                                        </div>

                                                        <!-- <div class="col-md-3 col-xs-2 row"> -->
                                                            <!-- <a class="text-info" onclick='addExistingAdditional_charge();'>Add more</a> -->
                                                        <!-- </div> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="addnewDiv"></div>
                                            
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <a href="javascript:void(0);" class="text-info pull-right addlink" onclick='addExistingAdditional_charge();'>Add more</a>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('charges_for', trans('quickadmin.service-request.fields.charges_for').'', ['class' => 'control-label fontweight fontsize']) !!}
                                                
                                                    {!! Form::text('additional_charges_title','' , ['class' => 'form-control', 'placeholder' => 'Charges for', 'id' => 'additional_charges_title']) !!}
                                                        <p class="help-block"></p>
                                                        @if($errors->has('additional_charges_title'))
                                                            <p class="help-block">
                                                                {{ $errors->first('additional_charges_title') }}
                                                            </p>
                                                        @endif
                                                </div>

                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12" style="padding-top: 4px;">
                                                            {{-- !! Form::label('amount', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !! --}}

                                                            {!! Form::label('', trans('').'', ['class' => 'control-label fontweight fontsize']) !!}

                                                            <div class="input-group">
                                                                <label class="input-group-addon" for="additional_charges">
                                                                    <span class="fa fa-rupee"></span>
                                                                </label>
                                                                {!! Form::text('additional_charges', old('additional_charges'), ['class' => 'form-control text-right', 'placeholder' => 'Amount', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()', 'id' => 'additional_charges']) !!}
                                                            </div>

                                                            <p class="help-block addamountError"></p>
                                                            @if($errors->has('additional_charges'))
                                                            <p class="help-block">
                                                                {{ $errors->first('additional_charges') }}
                                                            </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                            
                                                    <!--  {!! Form::label('amount', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label']) !!}
                                                    {!! Form::text('amount', old('amount'), ['class' => 'form-control', 'placeholder' => '','id' => 'amount', 'readonly' => '']) !!} -->
                                                    {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').':', ['class' => 'control-label']) !!}

                                                    <!-- total amount value label -->
                                                    {{-- !! Form::label('',old('amount'), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !! --}}
                                                
                                                    <!-- total amount hidden field -->
                                                    {{-- !! Form::hidden('amount', old('amount'), ['class' => 'form-control', 'placeholder' => '','id' => 'amount', 'readonly' => '']) !! --}}


                                                    {{-- !! Form::hidden('km_distance', old('km_distance'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_distance']) !! --}}
                                                    {{-- !! Form::hidden('km_charge', old('km_charge'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_charge']) !! --}}
                                                    <p class="help-block"></p>
                                                    @if($errors->has('amount'))
                                                    <p class="help-block">
                                                        {{ $errors->first('amount') }}
                                                    </p>
                                                    @endif
                                                </div> 
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {!! Html::decode(Form::label('totalamount', '<i class="fa fa-rupee"></i> '.number_format(0,2),['class' => 'control-label pull-right', 'id' => 'lbl_total_amount'])) !!}
                                                    
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            @endif

                            {!! Form::hidden('service_charge', old('service_charge'), ['id' => 'service_charge']) !!}
                            
                            {!! Form::hidden('installation_charge', old('installation_charge'), ['id' => 'installation_charge']) !!}

                            @if(auth()->user()->role_id != config('constants.ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID'))

                                {!! Form::hidden('transportation_charge','', ['id' => 'transportation_charge']) !!}

                            @endif

                            {!! Form::hidden('amount', old('amount'), ['id' => 'amount']) !!}


                            {!! Form::hidden('km_distance', old('km_distance'), ['id' => 'km_distance']) !!}
                            {!! Form::hidden('km_charge', old('km_charge'), ['id' => 'km_charge']) !!}

                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('note', trans('quickadmin.service-request.fields.note').'', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('note', old('note'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('note'))
                                    <p class="help-block">
                                        {{ $errors->first('note') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                            
                            
                            
                            
                            
                                    <!-- <div class="row">
                                        <div class="col-xs-12 form-group">
                                            {!! Form::label('status', trans('quickadmin.service-request.fields.status').'*', ['class' => 'control-label']) !!}
                                            {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('status'))
                                                <p class="help-block">
                                                    {{ $errors->first('status') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div> -->
                                    {!! Form::hidden('status', 'New', ['class' => 'form-control', 'placeholder' => '', 'id' => 'hiddenStatus']) !!}
                                </div>
                            </div>
                        </div>  
                    </div>    


                </div>
            

                {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
                <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
                {!! Form::close() !!}
                <!-- Quick add company modal -->
                <div class="modal fade in" id="company-modal">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add Company</h4>
                          </div>
                          {!! Form::open(['method' => 'POST', 'route' => ['admin.companies.store']]) !!}
                          <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>
                            <div id="renderCompanyHtml"></div>
                            {{-- @include('admin.companies.content') --}}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" id="btnAddCompany" class="btn btn-primary">Save</button>
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                </div>
                <!-- Quick add customer modal -->
                <div class="modal fade in" id="customer-modal">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add Customer</h4>
                          </div>
                          {!! Form::open(['method' => 'POST', 'route' => ['admin.customers.store']]) !!}
                          <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>
                            <div id="renderCustomerHtml"></div>
                            {{-- @include('admin.customers.content') --}}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" id="btnAddCustomer" class="btn btn-primary">Save</button>
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                </div>
                <!-- Quick add service center modal -->
                <div class="modal fade in" id="service-center-modal">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add Service center</h4>
                          </div>
                          {!! Form::open(['method' => 'POST', 'route' => ['admin.service_centers.store']]) !!}
                          <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>
                            <div id="renderServiceCenterHtml"></div>
                            {{-- @include('admin.service_centers.content') --}}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" id="btnAddServiceCenter" class="btn btn-primary">Save</button>
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                </div>
                <!-- Quick add technician modal -->
                <div class="modal fade in" id="technician-modal">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add Technician</h4>
                          </div>
                          {!! Form::open(['method' => 'POST', 'route' => ['admin.technicians.store']]) !!}
                          <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>
                            <div id="renderTechnicianHtml"></div>
                           {{-- @include('admin.technicians.content') --}}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" id="btnAddTechnician" class="btn btn-primary">Save</button>
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                </div>

                <div class="modal fade in" id="assign-products-modal">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Assign Products</h4>
                          </div>
                          {!! Form::open(['method' => 'POST', 'route' => ['admin.assign_products.store']]) !!}
                          <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>
                            <div id="renderAssignProductsHtml"></div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" id="btnAssignProducts" class="btn btn-primary">Save</button>
                          </div>
                          {!! Form::close() !!}
                        </div>
                        <!-- /.modal-content -->
                      </div>
                      <!-- /.modal-dialog -->
                </div>

                @stop

                @section('javascript')
                @parent
                
<script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<script>
    $(function(){
        moment.updateLocale('{{ App::getLocale() }}', {
            week: { dow: 1 } // Monday is the first day of the week
        });

        $('.date').datetimepicker({
            useCurrent:false,
            format: "{{ config('app.date_format_moment') }}",
            locale: "{{ App::getLocale() }}",
        });
    });

    $(document).on('click',"#selectbtn-product_id",function(){
        $("#selectall-product_id > option").prop("selected","selected");
        $("#selectall-product_id").trigger("change");
    });

    $(document).on('click',"#deselectbtn-product_id",function(){
        $("#selectall-product_id > option").prop("selected","");
        $("#selectall-product_id").trigger("change");
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
        var counter = 2;
        function addExistingAdditional_charge() {

            var arrayFromPHP = <?php echo json_encode($pre_additional_charge_array); ?>;
            
            var combo = ''
            $.each(arrayFromPHP, function (i, el) {
                if(i == 0){
                    i = '';
                }
                combo+= "<option value='"+i+"'>" + el + "</option>";
            });
            
            var append = '<div class="row existingAdditional_charge_for_'+counter+'"><div class="col-md-8 col-xs-7"><div class="form-group"><select class="form-control multiple_Additional_charge_for"  name="existingAdditional_charge_for[]" id="Additional_charge_for_existing-'+counter+'"  style="width:100%">'+ combo +'</select></div><p class="error-block_'+counter+' text-danger"></p></div><div class="col-md-4 col-xs-5"><div class="row"><div class="col-md-12 col-xs-12"><div class="input-group"><label class="input-group-addon" for="existingAdditional_charge_'+counter+'"><span class="fa fa-rupee"></span></label><input type="text" class="form-control text-right existingAdditional_charge" id="existingAdditional_charge_'+counter+'" name="existingAdditional_charge[]" onkeypress="return checkIsDecimalNumber(this,event)" onkeyup="totalServiceAmount()" placeholder="Amount"></div><a href="javascript:void(0);" class="text-danger  pull-right removelink" onclick="removeAdditionalChargeFor('+counter+');">Remove</a></div><div><p class="error-amount-block_'+counter+' text-danger"></p></div></div></div></div>';
            
            $(".addnewDiv").append(append);
            counter++;
        }

        // $(document).on('submit','form#FormnameId',function(event){
        //     event.preventDefault();
        //     var sel = $("#location-form").val();
        //     if(sel == ''){
        //         alert('Please select value first');
        //         return false;
        //     }
        // });

        function removeAdditionalChargeFor(counterId) {
            // alert(counterId);
            if(counterId != ''){
                $('.existingAdditional_charge_for_'+counterId).remove();
                totalServiceAmount();
            }else{
                return false;
            }
        }
    
</script>
@stop