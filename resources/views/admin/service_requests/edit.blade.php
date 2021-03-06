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
    
    {!! Form::model($service_request, ['method' => 'PUT', 'route' => ['admin.service_requests.update', $service_request->id], 'id' => 'formServiceRequest','onsubmit' => "return saveButton()"]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.formTitle') ( {{ 'JW'.sprintf("%04d", $service_request->id)}} )
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
                                    {!! Form::select('service_type', $enum_service_type, old('service_type'), ['class' => 'form-control select2', 'required' => '', 'onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('service_type'))
                                        <p class="help-block">
                                            {{ $errors->first('service_type') }}
                                        </p>
                                    @endif
                                </div>

                                <?php
                                    $createdByName = '-';
                                    if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){
                                        if($userDetail != ''){
                                            $createdByName = $userDetail->name;
                                        }
                                       
                                    }
                                ?>
                                <div class="col-md-6 form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {!! Form::label('created_date', trans('quickadmin.service-request.fields.created_date').':', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                            {!! Form::label('created_date', App\Helpers\CommonFunctions::setDateFormat($service_request->created_at), ['class' => 'control-label lablemargin fontweight','readonly' => '']) !!}
                                        </div>
                                        @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                                            <div class="col-md-6">
                                                {!! Form::label('created_by', trans('quickadmin.service-request.fields.created_by').':', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                                {!! Form::label('created_by',$createdByName, ['class' => 'control-label lablemargin fontweight','readonly' => '']) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            $backgroundColor = '';
                        ?>
                        @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                        <?php
                            $backgroundColor = $enum_status_color[$service_request->status];
                        ?>
                             <!-- Request Status -->
                            <div class="col-md-12">
                               {!! Form::label('status', trans('quickadmin.service-request.fields.status').': ', ['class' => 'control-label']) !!}

                                {!! Form::label('', $service_request->status, ['class' => 'control-label','style' => 'color:'.$backgroundColor]) !!}
                                {!! Form::hidden('status', old('status'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'status']) !!}

                                @if($service_request->is_reopen == 1)
                                    <span class="label label-primary paddingMarginLeftLabel">Re-opened</span>
                                @endif
                                <p class="help-block"></p>
                            </div>
                        @else
                            <div class="col-md-9 col-sm-12 col-xs-12">
                                <div class="row">
                                    <?php
                                        // $divDetail = "col-md-12 col-sm-12 col-xs-12";

                                        // if($service_request->status == "Closed" && $service_request->is_paid == 0 && (auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))){
                                            $divDetail = "col-md-8 col-sm-8 col-xs-8";
                                        // }
                                    ?>
                                    <div class="{{ $divDetail }}">
                                        <!-- Request Status -->
                                        @if($service_request->status == "New")
                                            
                                            {!! Form::hidden('status', old('status'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'status']) !!}
                                        @else
                                            {!! Form::label('status', trans('quickadmin.service-request.fields.status').'*', ['class' => 'control-label']) !!}

                                            @if($service_request->is_reopen == 1)
                                                <span class="label label-primary paddingMarginLeftLabel">Re-opened</span>
                                            @endif

                                            {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','id' => 'status','style' => 'width:100%']) !!}
                                            <p class="help-block"></p>
                                            @if($errors->has('status'))
                                                    <p class="help-block">
                                                        {{ $errors->first('status') }}
                                                    </p>
                                            @endif
                                        @endif
                                    </div>
                                    @if($service_request->status == "Closed" && $service_request->is_paid == 0 && (auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')))
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <button class="btn btn-success" type="button" style="margin-top: 23px;" id="requestReopen" onclick="reopenRequest({{$service_request->id}});" title="Reopen Request">Reopen</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

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
                                <!-- Company & Customer  -->
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
                                @else
                                    {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control', 'id' => 'company_id']) !!}
                                @endif
                                <div class="col-md-6"> 
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-sm-10 col-xs-9 ">
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
                                            <!-- <button id="quick_add_customer" class="btn btn-success btn-quick-add" data-toggle="modal" data-target="#customer-modal" type="button" style="margin-top: 23px;"><i class="fa fa-plus"></i></button> -->
                                            <button id="quick_add_customer" class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;" onclick="quickadd('customer')" title="Add Customer"><i class="fa fa-plus"></i></button>
                                           
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <div class="row">
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                                <div class="col-md-6">
                                    <!--  added condition to set layout when company is not visible -->
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            <!-- show email and phone show in edit and insert -->
                                            <div class="cusEmailbl" <?php if(empty($service_request->customer->email)){ echo 'style="display:none;"';} ?>>
                                                {!! Form::label('customer_email', trans('quickadmin.service-request.fields.email').': ', ['class' => 'control-label ']) !!}
                                                {!! Form::label('', $service_request->customer->email, ['class' => 'control-label fontweight cusEmail']) !!}
                                                <br>
                                            </div>
                                            <div class="cusPhonelbl" <?php if(empty($service_request->customer->phone)){ echo 'style="display:none;"';} ?>>
                                                {!! Form::label('customer_phone', trans('quickadmin.service-request.fields.phone').': ', ['class' => 'control-label ']) !!}
                                                {!! Form::label('', $service_request->customer->phone, ['class' => 'control-label fontweight cusPhone ']) !!}  
                                                <br>
                                            </div>
                                            {!! Form::label('customer_address', trans('quickadmin.service-request.fields.customer-address').':', ['class' => 'control-label custAddresslbl']) !!}

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
                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))

                <!-- Service center and technician will not be visible to technician, company user and admin -->
                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseServiceCenter">
                        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                        <!-- <a data-toggle="collapse" href="#collapseServiceCenter">  --> Service Center <!-- </a>  -->
                        @else
                        <!-- <a data-toggle="collapse" href="#collapseServiceCenter"> -->Technician<!-- </a>  -->
                        @endif
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseServiceCenter" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
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
                                            <button class="btn btn-success btn-quick-add" type="button" style="margin-top: 23px;"  onclick="quickadd('service_center')" title="Add Service Center"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    {!! Form::hidden('service_center_id', old('service_center_id'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'service_center_id']) !!}
                                @endif
                                <!-- Technician -->
                                <div class="col-md-6">
                                    <div class="row techDiv" {{ ($service_request->service_center_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-sm-10 col-xs-9">
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}
                                            {!! Form::select('technician_id', $technicians, old('technician_id'), ['class' => 'form-control select2','id' => 'technician_id','style' => 'width:100%']) !!}
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
                            <div class="row suggestedServiceCenterDiv" {{ ((auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')) && count($supported_service_centers) > 0) ? '' : 'style=display:none'}}>
                                <!-- suggested service center -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.suggested-service-center').'', ['class' => 'control-label']) !!}
                                            <div id="suggestedHTML">
                                                @if(count($supported_service_centers) > 0)
                                                    @foreach ($supported_service_centers as $center)
                                                        <div>
                                                            <input type="radio" name="suggested_service_center" <?=($center->id == $service_request->service_center_id)?"checked='checked'":""?> value="{{ $center->id }}"><label class="control-label lblSuggestedCenter fontweight">{{ $center->name }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
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
                <!-- <div class="panel"> -->
                    {!! Form::hidden('service_center_id', old('service_center_id'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'service_center_id']) !!}
                    {!! Form::hidden('technician_id', old('technician_id'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'technician_id']) !!}
                <!-- </div> -->
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseCallDetail">
                        Call Detail
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
                        Product
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseProduct" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group">
                                        {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'*', ['class' => 'control-label']) !!}
                                        {!! Form::select('product_id', $products, old('product_id'), ['class' => 'form-control select2', 'required' => '', 'onchange' => 'requestCharge(this)','style' => 'width:100%']) !!}
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
                                            {!! Form::select('parts[]', $parts, old('parts') ? old('parts') : $service_request->parts->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-parts','style' => 'width:100%']) !!}
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
                                        {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').'', ['class' => 'control-label']) !!}
                                        {!! Form::text('purchase_from', $service_request->purchase_from, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('purchase_from'))
                                            <p class="help-block">
                                                {{ $errors->first('purchase_from') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group warrantycardnumber" {{ ($service_request->call_type != "Warranty") ? 'style=display:none' : ''}}>
                                        {!! Form::label('warranty_card_number', trans('quickadmin.service-request.fields.warranty-card-number').'*', ['class' => 'control-label']) !!}
                                        {!! Form::text('warranty_card_number',$service_request->warranty_card_number, ['class' => 'form-control', 'placeholder' => '','id' => 'warrantyCardNumber']) !!}
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
                                        {!! Form::text('serial_no',$service_request->serial_no, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('serial_no'))
                                            <p class="help-block">
                                                {{ $errors->first('serial_no') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').'', ['class' => 'control-label']) !!}
                                        {!! Form::select('mop', $enum_mop, $service_request->mop, ['class' => 'form-control select2','style' => 'width:100%']) !!}
                                        <p class="help-block"></p>
                                        @if($errors->has('mop'))
                                            <p class="help-block">
                                                {{ $errors->first('mop') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group onlineserialnumber" {{ ($service_request->call_type != "Warranty") ? 'style=display:none' : ''}}>
                                        {!! Form::label('online_serial_number', trans('quickadmin.service-request.fields.online-serial-number').'*', ['class' => 'control-label']) !!}
                                        {!! Form::text('online_serial_number',$service_request->online_serial_number, ['class' => 'form-control', 'placeholder' => '','id' => 'onlineSerialNumber']) !!}
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
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseOther"> Other
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseOther" class="panel-collapse collapse in">
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
                                    {!! Form::textarea('complain_details', old('complain_details'), ['class' => 'form-control text-justify', 'placeholder' => '']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('complain_details'))
                                        <p class="help-block">
                                            {{ $errors->first('complain_details') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <?php
                                $i = 1;
                            ?>
                            @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('completion_date', trans('quickadmin.service-request.fields.completion-date').'*', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                    {!! Form::text('completion_date', old('completion_date'), ['class' => 'form-control date', 'placeholder' => '','required' => '']) !!}
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

                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').':', ['class' => 'control-label lablemargin']) !!}
                                                </div> 
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->service_charge,2),['class' => 'control-label lablemargin pull-right','readonly' => '','id' => 'lbl_service_charge'])) !!}

                                                            {!! Form::hidden('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '','id' => 'service_charge', 'readonly' => '']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-md-6 col-xs-6"> -->
                                            {{-- !! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').':', ['class' => 'control-label lablemargin']) !! --}}

                                            <!-- service charge value label -->
                                            {{-- !! Html::decode(Form::label('', '<i class="fa fa-rupee"></i>'.number_format($service_request->service_charge,2),['class' => 'control-label lablemargin pull-right','readonly' => '','id' => 'lbl_service_charge'])) !! --}}

                                            {{-- !! Form::label('', number_format($service_request->service_charge,2), ['class' => 'control-label lablemargin pull-right','readonly' => '','id' => 'lbl_service_charge']) !! --}}

                                            <!-- service charge hidden field -->
                                            {{-- !! Form::hidden('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '','id' => 'service_charge', 'readonly' => '']) !! --}}
                                            <!-- <p class="help-block"></p>
                                            @if($errors->has('service_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('service_charge') }}
                                                </p>
                                            @endif -->
                                        <!-- </div> -->
                                    </div>
                                   
                                    <div class="row installationChargeDiv" {{ ($service_request->service_type == "repair") ? 'style=display:none' : ''}}>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').':', ['class' => 'control-label lablemargin']) !!}
                                                </div> 
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->installation_charge,2),['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge'])) !!}

                                                            {!! Form::hidden('installation_charge', $service_request->installation_charge, ['class' => 'form-control', 'placeholder' => '', 'readonly' => '','id' => 'installation_charge']) !!}

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                        </div>

                                        <!-- <div class="col-md-12"> -->
                                            {{-- !! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').':', ['class' => 'control-label lablemargin']) !! --}}
                                            
                                            <!-- installation charge value label -->
                                            {{-- !! Html::decode(Form::label('', '<i class="fa fa-rupee"></i>'.number_format($service_request->installation_charge,2),['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge'])) !! --}}

                                            {{-- !! Form::label('', number_format($service_request->installation_charge,2), ['class' => 'control-label lablemargin pull-right','id' => 'lbl_installation_charge']) !! --}}
                                            
                                            <!-- installation charge hidden field -->
                                            {{-- !! Form::hidden('installation_charge', $service_request->installation_charge, ['class' => 'form-control', 'placeholder' => '', 'readonly' => '']) !! --}}
                                            <!-- <p class="help-block"></p>
                                            @if($errors->has('installation_charge'))
                                                <p class="help-block">
                                                    {{ $errors->first('installation_charge') }}
                                                </p>
                                            @endif -->
                                        <!-- </div> -->
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
                                    @if(!$service_center_supported && (auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')))
                                    <div class="row transportationDiv">
                                    @else
                                    <div class="row transportationDiv" {{ ($service_request->transportation_charge <= 0) ? 'style=display:none' : ''}}>
                                    @endif
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('lbltransportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                                </div>
                                                <!-- transportation amount value label -->
                                                <div class="col-md-4 col-xs-5 transportationField">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))

                                                                <div class="input-group">
                                                                    <label class="input-group-addon" for="transportation_charge">
                                                                        <span class="fa fa-rupee"></span>
                                                                    </label>
                                                                    {!! Form::text('transportation_charge',number_format($service_request->transportation_charge,2,'.',''), ['class' => 'form-control pull-right text-right', 'placeholder' => 'Charges for', 'id' => 'transportation_charge', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}
                                                                </div>

                                                            @else

                                                                {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->transportation_charge,2),['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount'])) !!}

                                                                {{-- !! Form::label('',number_format($service_request->transportation_charge,2), ['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount']) !! --}}
                                                            
                                                                {!! Form::hidden('transportation_charge',($service_request->transportation_charge), ['class' => 'form-control', 'placeholder' => '','id' => 'transportation_charge']) !!}
                                                            @endif
                                                        </div>
                                                        @if($service_request->transportation_charge > 0)
                                                            <div class="col-md-12 col-xs-12">
                                                                {!! Html::decode(Form::label('', '( <i class="fa fa-rupee"></i> '.number_format($service_request->km_charge,2).' per km )',['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge'])) !!}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-12"> -->

                                            {{-- !! Html::decode(Form::label('', '(<i class="fa fa-rupee"></i>'.number_format($service_request->km_charge,2).' per km)',['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge'])) !! --}}

                                            {{-- !! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_km_charge']) !! --}}
                                        <!-- </div> -->
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {!! Form::label('additional_charges', trans('quickadmin.service-request.fields.additional-charges').':', ['class' => 'control-label']) !!}
                                                </div>
                                            </div>
                                            
                                            <?php
                                                if(isset($additional_charge_title['option']) && !empty($additional_charge_title['option'])){

                                                    $last_key = @end(array_keys($additional_charge_title['option']));
                                                  
                                                    $i = 1;
                                                    foreach ($additional_charge_title['option'] as $additional_charge_title_key => $additional_charge_title_value) {
                                                       
                                            ?>  
                                            <div class="row existingAdditional_charge_for_{{ $i }}">
                                                <div class="col-md-8 col-xs-7">
                                                    <div class="form-group">

                                                        <!-- <select class="form-control multiple_Additional_charge_for" id="Additional_charge_for_existing-1" style="width:100%" name="existingAdditional_charge_for[]"> -->

                                                        <select class="form-control multiple_Additional_charge_for" id="Additional_charge_for_existing-{{ $i }}" style="width:100%" name="existingAdditional_charge_for[]">
                                                        <?php
                                                            foreach ($pre_additional_charge_array as $key => $value) {
                                                                $selected = '';
                                                                
                                                                if($key === $additional_charge_title_value){
                                                                    $selected = 'selected';
                                                                }

                                                                if($key == 0){
                                                                    $key = '';
                                                                }
                                                                
                                                        ?>  
                                                            <option value="{{ $key }}" {{ $selected }}>{{ $value }} </option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                        {{-- !! Form::select('existingAdditional_charge_for[]', $pre_additional_charge_array, $additional_charge_title_value , ['class' => 'form-control','id' => 'Additional_charge_for_existing-1', 'required' => '','style' => 'width:100%']) !! --}}
                                                    </div>
                                                    <p class="error-block_{{ $i }} text-danger"></p>
                                                </div>
                                                
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {{-- !! Form::label('additional_charges', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !! --}}

                                                            <div class="input-group">
                                                                <label class="input-group-addon" for="existingAdditional_charge">
                                                                    <span class="fa fa-rupee"></span>
                                                                </label>
                                                                {!! Form::text('existingAdditional_charge[]', $service_request['additional_charges']['option'][$additional_charge_title_key], ['class' => 'form-control text-right existingAdditional_charge', 'placeholder' => 'Amount','id' => 'existingAdditional_charge_'.$i,'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}
                                                            </div>
                                                            <p class="error-amount-block_{{ $i }} text-danger"></p>

                                                            {{-- @if($last_key != $additional_charge_title_key) --}}
                                                            <a href="javascript:void(0);" class="text-danger pull-right removelink" onclick='removeAdditionalChargeFor({{$i}});'>Remove</a>
                                                            {{-- @endif --}}
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                        $i++;
                                                    }
                                                                      
                                                }else{
                                                    $i = 2;
                                            ?>


                                            <div class="row existingAdditional_charge_for_1">
                                                <div class="col-md-8 col-xs-7">
                                                    <div class="form-group">
                                                        {{-- !! Form::select('existingAdditional_charge_for[]', $pre_additional_charge_array,'', ['class' => 'form-control multiple_Additional_charge_for','id' => 'Additional_charge_for_existing-1', 'style' => 'width:100%']) !! --}}

                                                        <select class="form-control multiple_Additional_charge_for" id="Additional_charge_for_existing-1" style="width:100%" name="existingAdditional_charge_for[]">
                                                        <?php
                                                            foreach ($pre_additional_charge_array as $key => $value) {
                                                                if($key == 0){
                                                                    $key = '';
                                                                }
                                                                
                                                        ?>  
                                                            <option value="{{ $key }}"> {{ $value }} </option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>

                                                    </div>
                                                    <p class="error-block_1 text-danger"></p>
                                                </div>

                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            {{-- !! Form::label('additional_charges', trans('quickadmin.service-request.fields.amount').'', ['class' => 'control-label fontweight fontsize']) !! --}}

                                                            <div class="input-group">
                                                                <label class="input-group-addon" for="existingAdditional_charge">
                                                                    <span class="fa fa-rupee"></span>
                                                                </label>
                                                                {!! Form::text('existingAdditional_charge[]', '', ['class' => 'form-control text-right existingAdditional_charge', 'placeholder' => 'Amount','id' => 'existingAdditional_charge_1','onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()']) !!}
                                                                
                                                            </div>

                                                            <p class="error-amount-block_1 text-danger"></p>
                                                           
                                                            <a href="javascript:void(0);" class="text-danger pull-right removelink" onclick='removeAdditionalChargeFor(1);'>Remove</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                }
                                            ?>

                                            <div class="addnewDiv"></div>
                                                
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <a href="javascript:void(0);" class="text-info pull-right addlink" onclick='addExistingAdditional_charge();'>Add more</a>
                                                </div>
                                            </div>  
                                                
                                            <div class="row">
                                                <?php
                                                    $additional_charge_title_other = '';
                                                    $additional_charges_other = 0;
                                                    if(!empty($additional_charge_title)){

                                                        if(isset($additional_charge_title['other']) && !empty($additional_charge_title['other'])){

                                                            $additional_charge_title_other = $additional_charge_title['other'];
                                                            if(isset($service_request['additional_charges']['other']) && $service_request['additional_charges']['other'] > 0){
                                                                $additional_charges_other = $service_request['additional_charges']['other'];
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <div class="col-md-8 col-xs-7">
                                                    {!! Form::label('charges_for', trans('quickadmin.service-request.fields.charges_for').'', ['class' => 'control-label fontweight fontsize']) !!}
                                                
                                                    {!! Form::text('additional_charges_title',$additional_charge_title_other, ['class' => 'form-control', 'placeholder' => 'Charges for', 'id' => 'additional_charges_title']) !!}
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

                                                            <label for="additional_charges" class="control-label"> </label>

                                                            <div class="input-group">
                                                                <label class="input-group-addon" for="additional_charges">
                                                                    <span class="fa fa-rupee"></span>
                                                                </label>
                                                                {!! Form::text('additional_charges',($additional_charges_other > 0?$additional_charges_other:''), ['class' => 'form-control text-right', 'placeholder' => 'Amount', 'onkeypress' => 'return checkIsDecimalNumber(this,event)', 'onkeyup' => 'totalServiceAmount()', 'id' => 'additional_charges']) !!}
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
                                          
                                                    <?php 
                                                        $paidStatus = '';
                                                    ?>
                                                    @if((auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')))
                                                        <?php 
                                                            $paidStatus = '( Due ) ';
                                                        ?>
                                                        @if($service_request->status == "Closed" && $service_request->is_paid == 1)
                                                        <?php 
                                                            $paidStatus = '( Paid ) ';
                                                        ?>
                                                        @endif
                                                    @endif
                                                    {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').''.$paidStatus.':' , ['class' => 'control-label']) !!}

                                                    <!-- total amount value label -->

                                                    {{-- !! Html::decode(Form::label('totalamount', '<i class="fa fa-rupee"></i>'.number_format($service_request->amount,2),['class' => 'control-label pull-right', 'id' => 'lbl_total_amount'])) !! --}}   

                                                    {{-- !! Form::label('totalamount',number_format($service_request->amount,2), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !! --}}

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
                                                <?php
                                                
                                                ?>
                                                <div class="col-md-4 col-xs-5">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">

                                                            {!! Html::decode(Form::label('totalamount', '<i class="fa fa-rupee"></i> '.number_format($service_request->amount,2),['class' => 'control-label pull-right', 'id' => 'lbl_total_amount'])) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            @else
                                @if(isset($additional_charge_title['other']) || isset($additional_charge_title['option']) )

                                    {!! Form::hidden('additional_charges_title',isset($additional_charge_title['other'])? $additional_charge_title['other']:'', ['id' => 'additional_charges_title']) !!}

                                    @if(isset($additional_charge_title['option']))
                                        @foreach($additional_charge_title['option'] as $key => $value)
                                                        
                                            {!! Form::hidden('existingAdditional_charge_for['.$key.']',$value, ['id' => 'existingAdditional_charge_for'.$key]) !!}

                                            {!! Form::hidden('existingAdditional_charge['.$key.']',$service_request['additional_charges']['option'][$key], ['id' => 'existingAdditional_charge'.$key]) !!}
                                        

                                        @endforeach
                                    @endif
                                @endif
                                
                                <?php
                                    $othercharge = '';
                                    if(isset($service_request['additional_charges']['other']) && $service_request['additional_charges']['other'] > 0){
                                        $othercharge = $service_request['additional_charges']['other'];
                                    }
                                ?>
                                {!! Form::hidden('additional_charges', $othercharge, ['class' => 'form-control', 'placeholder' => 'Amount', 'id' => 'additional_charges']) !!}

                                {{-- !! Form::hidden('additional_charges', ($service_request['additional_charges']['other'] > 0)? $service_request['additional_charges']['other']:'', ['class' => 'form-control', 'placeholder' => 'Amount', 'id' => 'additional_charges']) !! --}}

                                {!! Form::hidden('service_charge', $service_request->service_charge, ['class' => 'form-control', 'placeholder' => '','id' => 'service_charge', 'readonly' => '']) !!}

                                <!-- new add in else -->

                                {!! Form::hidden('installation_charge', $service_request->installation_charge, ['class' => 'form-control', 'placeholder' => '', 'readonly' => '','id' => 'installation_charge']) !!}

                                @if(auth()->user()->role_id != config('constants.ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.SUPER_ADMIN_ROLE_ID'))

                                    {!! Form::hidden('transportation_charge',($service_request->transportation_charge), ['class' => 'form-control', 'placeholder' => '','id' => 'transportation_charge']) !!}

                                @endif

                                {!! Form::hidden('amount', old('amount'), ['class' => 'form-control', 'placeholder' => '','id' => 'amount', 'readonly' => '']) !!}

                                {!! Form::hidden('km_distance', old('km_distance'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_distance']) !!}

                                {!! Form::hidden('km_charge', old('km_charge'), ['class' => 'form-control', 'placeholder' => '', 'id' => 'km_charge']) !!}

                            @endif
                            
                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('note', trans('quickadmin.service-request.fields.note').'', ['class' => 'control-label']) !!}
                                    <!-- {!! Form::text('note', old('note'), ['class' => 'form-control', 'placeholder' => '']) !!} -->

                                    {!! Form::textarea('note', old('note'), ['class' => 'form-control text-justify', 'placeholder' => '']) !!}
                                    
                                    <p class="help-block"></p>
                                    @if($errors->has('note'))
                                        <p class="help-block">
                                            {{ $errors->first('note') }}
                                        </p>
                                    @endif
                                </div>
                                @if($service_request->status == "Closed" && (auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')))
                                <div class="col-xs-12 form-group pull-right">
                                     
                                    <a target="_blank" href="{{ route('admin.service_request.invoice',[$service_request->id]) }}" class="btn btn-xl btn-primary pull-right">View Invoice</a>
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
    {{-- @if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID') && !$service_request->is_accepted) --}}
        <!-- <div class="row">
            <div class="col-md-12 form-group"> -->
                <!-- <a href="{{-- route('admin.service_request.accept',[$service_request->id]) --}}" 
                     class="btn btn-danger"> -->
                {{-- @lang('quickadmin.qa_accept') --}}</a>
                <!-- <a href="{{-- route('admin.service_request.reject',[$service_request->id]) --}}" class="btn btn-default">{{-- @lang('quickadmin.qa_reject') --}}</a> -->
                {{-- !! Form::close() !! --}}
            <!-- </div> -->
        <!-- </div> -->
    {{-- @else --}}
        <div class="row">
            <div class="col-md-12 form-group">
                @if($service_request->status == "Closed" && $service_request->is_paid == 0 && (auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')))
                    <button type="button" onclick="updatePaidstatus({{$service_request->id}});"
                     class="btn btn-danger">@lang('quickadmin.qa_paid')</button>
                @endif

                
                {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger', 'id' => 'btnUpdate']) !!}
                <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
                {!! Form::close() !!}
            </div>
        </div>
    {{-- @endif --}}
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
                {{--  @include('admin.technicians.content') --}}
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
@stop

@section('javascript')
    @parent
    
    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            if("{{$service_request->status}}" == "Closed")
            {
                // $("input").prop("disabled", true);
                // $("select").prop("disabled", true);
                // $("textarea").prop("disabled", true);
                // $("#btnUpdate").hide();
                // $("#selectbtn-parts").hide();
                // $("#deselectbtn-parts").hide();
                // $(".btn-quick-add").hide();
                $("#formServiceRequest").find("input").prop("disabled", true);
                $("#formServiceRequest").find("select").prop("disabled", true);
                $("#formServiceRequest").find("textarea").prop("disabled", true);
                $("#formServiceRequest").find("#btnUpdate").hide();
                $("#formServiceRequest").find("#selectbtn-parts").hide();
                $("#formServiceRequest").find("#deselectbtn-parts").hide();
                $("#formServiceRequest").find(".btn-quick-add").hide();
                
                $(".removelink").each(function() {
                        $(this).hide();
                });
                $(".addlink").each(function(e) {
                    $(this).hide();
                });
            }
            else
            {
                if({{auth()->user()->role_id}} == SERVICE_ADMIN_ROLE_ID)
                {
                    // disabled all field except technician and status for service admin
                    // $("input[type=text]").prop("readonly", true);
                    // $("textarea").prop("readonly", true);
                    // $("select").prop("disabled", true);
                    // $("#technician_id").prop("disabled", false);
                    // $("#status").prop("disabled", false);
                    // $("#quick_add_customer").hide();
                    $("#formServiceRequest").find("input[type=text]").prop("readonly", true);
                    $("#formServiceRequest").find("textarea").prop("readonly", true);
                    $("#formServiceRequest").find("select").prop("disabled", true);
                    $("#formServiceRequest").find("#technician_id").prop("disabled", false);
                    $("#formServiceRequest").find("#status").prop("disabled", false);
                    $("#formServiceRequest").find("#selectbtn-parts").hide();
                    $("#formServiceRequest").find("#deselectbtn-parts").hide();
                    $("#formServiceRequest").find("#quick_add_customer").hide();

                    $(".removelink").each(function() {
                        $(this).hide();
                    });
                    $(".addlink").each(function(e) {
                        $(this).hide();
                    });
                }
                else if({{auth()->user()->role_id}} == TECHNICIAN_ROLE_ID)
                {
                    // disabled all field except charges related field, parts and status for technician
                    // $("input[type=text]").prop("readonly", true);
                    // $("textarea").prop("readonly", true);
                    // $("select").prop("disabled", true);
                    // $("#additional_charges_title").prop("readonly", false);
                    // $("#additional_charges").prop("readonly", false);
                    // $("#selectall-parts").prop("disabled", false);
                    // $("#status").prop("disabled", false);
                    // $("#quick_add_customer").hide();

                    $("#formServiceRequest").find("input[type=text]").prop("readonly", true);
                    $("#formServiceRequest").find("textarea").prop("readonly", true);
                    $("#formServiceRequest").find("select").prop("disabled", true);
                    $("#formServiceRequest").find("#additional_charges_title").prop("readonly", false);
                    $("#formServiceRequest").find("#additional_charges").prop("readonly", false);
                    $("#formServiceRequest").find("#selectall-parts").prop("disabled", false);
                    $("#formServiceRequest").find("#status").prop("disabled", false);
                    $("#formServiceRequest").find("#quick_add_customer").hide();
                    $("#formServiceRequest").find(".existingAdditional_charge").prop("readonly", false);
                    $("#formServiceRequest").find(".multiple_Additional_charge_for").prop("disabled", false);
                    // $(".removelink").each(function() {
                    //     $(this).hide();
                    // });
                    // $(".addlink").each(function(e) {
                    //     $(this).hide();
                    // });


                    // $("#adavance_amount").prop("readonly", false);
                    // $("#service_charge").prop("readonly", false);
                    // $("#service_tag").prop("readonly", false);
                    
                    // $("#amount").prop("readonly", false);

                    

                }
            }
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                useCurrent:false,
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}"
            });
            
            // removed disabled attr of select on form submit to store exusting value
            $('form').bind('submit', function() {
                $(this).find('select:disabled').removeAttr('disabled');
            });

            //Validate for warranty number and online number
            $('#warrantyCardNumber, #onlineSerialNumber').change(function(){
                var selectedCallType = $('#call_type').children("option:selected").val();
                // alert(selectedCallType);
                if(selectedCallType == 'Warranty'){
                    var serialNumber = $('#onlineSerialNumber').val();
                    var warrantyNumber = $('#warrantyCardNumber').val();
                    if(serialNumber == '' || warrantyNumber == ''){
                        $('#onlineSerialNumber').attr('required', true);
                        $('#warrantyCardNumber').attr('required', true);
                    }else{
                        $('#onlineSerialNumber').attr('required', false);
                        $('#warrantyCardNumber').attr('required', false);
                    }
                }
            });
        });

        function updatePaidstatus(serviceRequestId) {
            if(serviceRequestId != 0){

               $.ajax({
                type:'POST',
                url:APP_URL+"/admin/amountPaid",
                data:{
                    'serviceRequestId':serviceRequestId,
                    '_token': '{{csrf_token()}}'
                },
                dataType: "json",
                success:function(data) {
                    if(data == 1){
                        window.location.reload();
                    }
                }
               });
              
            }
        }
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

        var counter = {{$i}};
        function addExistingAdditional_charge() {
          
            var arrayFromPHP = <?php echo json_encode($pre_additional_charge_array); ?>;

            var combo = ''
            $.each(arrayFromPHP, function (i, el) {
                if(i == 0){
                    i = '';
                }
                combo+= "<option value='"+i+"'>" + el + "</option>";
            });
            
            var append = '<div class="row existingAdditional_charge_for_'+counter+'"><div class="col-md-8 col-xs-7"><div class="form-group"><select class="form-control multiple_Additional_charge_for" name="existingAdditional_charge_for[]" id="Additional_charge_for_existing-'+counter+'" style="width:100%">'+ combo +'</select><p class="error-block_'+counter+' text-danger"></p></div></div><div class="col-md-4 col-xs-5"><div class="row"><div class="col-md-12 col-xs-12"><div class="input-group"><label class="input-group-addon" for="existingAdditional_charge_'+counter+'"><span class="fa fa-rupee"></span></label><input type="text" class="form-control text-right existingAdditional_charge" id="existingAdditional_charge_'+counter+'" name="existingAdditional_charge[]" onkeypress="return checkIsDecimalNumber(this,event)" onkeyup="totalServiceAmount()" placeholder="Amount"></div><p class="error-amount-block_'+counter+' text-danger"></p><a href="javascript:void(0);" class="text-danger pull-right removelink" onclick="removeAdditionalChargeFor('+counter+');">Remove</a></div></div></div></div>';
            
            $(".addnewDiv").append(append);
            counter++;
        }

        function removeAdditionalChargeFor(counterId) {
            // alert(counterId);
            if(counterId != ''){
                $('.existingAdditional_charge_for_'+counterId).remove();
                totalServiceAmount();
            }else{
                return false;
            }
        }

        function reopenRequest(serviceRequestId) {
            $('#requestReopen').attr('disabled', true);
            $.ajax({
                type:'POST',
                url:APP_URL+"/admin/reopenRequest",
                data:{
                    'id':serviceRequestId,
                    '_token': '{{csrf_token()}}'
                },
                dataType: "json",
                success:function(data) {
                    if(data == 1){
                        var url = APP_URL+"/admin/service_requests/"+serviceRequestId+"/edit";
                        window.location.href=url;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('======Error======');
                    console.log(jqXHR);
                    console.log('==============');
                    console.log(textStatus);
                    console.log('==============');
                    console.log(errorThrown);
                    console.log('=================');
                    alert('Something went wrong');
                    $('#requestReopen').attr('disabled', false);
                }
            });
        }
    </script>
@stop