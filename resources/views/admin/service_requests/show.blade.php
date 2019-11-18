@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-request.title')</h3> -->
    <style>
        .lablemargin{
            margin-top: 20px;
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
        .rowmargin13{
            margin-bottom: 13px !important;
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.formTitle') ( {{ 'JW'.sprintf("%04d", $service_request->id)}} )
        </div>

        <div class="panel-body table-responsive">
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
            @endif
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <?php
                                    $createdByName = '-';
                                    if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID')){
                                        if($userDetail != ''){
                                            $createdByName = $userDetail->name;
                                        }
                                    }
                                ?>
                                <div class="col-md-6">
                                    {!! Form::label('service_type', trans('quickadmin.service-request.fields.service-type').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('service_type', ucfirst($service_request->service_type), ['class' => 'control-label fontweight']) !!}
                                </div>

                                <div class="col-md-6">
                                    {!! Form::label('created_date', trans('quickadmin.service-request.fields.created_date').': ', ['class' => 'control-label','readonly' => '']) !!}
                                    {!! Form::label('created_date', App\Helpers\CommonFunctions::setDateFormat($service_request->created_at), ['class' => 'control-label fontweight','readonly' => '']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row rowmargin13">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                <!-- Request Status -->
                                    {!! Form::label('status', trans('quickadmin.service-request.fields.status').': ', ['class' => 'control-label lablemargin']) !!}
                                    {!! Form::label('status', $service_request->status, ['class' => 'control-label lablemargin','style' => 'color:'.$enum_status_color[$service_request->status]]) !!}

                                    @if($service_request->is_reopen == 1)
                                       <!-- <span class="fontsize">( Re-opend )</span> -->
                                       <span class="label label-primary paddingMarginLeftLabel">Reopened Request</span>
                                    @endif
                                </div>
                                @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                            
                                        {!! Form::label('created_by', trans('quickadmin.service-request.fields.created_by').': ', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                        {!! Form::label('created_by',$createdByName, ['class' => 'control-label fontweight lablemargin','readonly' => '']) !!}
                                       
                                    </div>
                                @endif
                            </div>
                        </div>
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
                                <div class="col-md-6">
                                    
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', $service_request->company->name, ['class' => 'control-label fontweight']) !!}
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6"> 
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12 ">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', $service_request->customer->firstname.' '.$service_request->customer->lastname, ['class' => 'control-label fontweight']) !!}
                                            
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <div class="row">
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                                <div class="col-md-6">
                                    <!--  added condition to set layput when company is not visible -->
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer-address').': ', ['class' => 'control-label']) !!}
                                            <div class="custAddress">
                                                {{$service_request->customer->address_1}}
                                                <br/>
                                                @if(!empty($service_request->customer->address_2))
                                                {{$service_request->customer->address_2}}
                                                <br/>
                                                @endif
                                                {{$service_request->customer->city}}
                                                <br/>
                                                {{$service_request->customer->state." - ".$service_request->customer->zipcode}}
                                            </div>
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
                    <div class="panel-heading headerTitle" data-toggle="collapse" href="#collapseServiceCenter">
                        Service Center
                        <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
                    </div>
                    <div id="collapseServiceCenter" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">

                                <!-- Service center -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('',  (!empty($service_request->service_center->name))?$service_request->service_center->name:'Not Assigned', ['class' => 'control-label fontweight']) !!}
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
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', (!empty($service_request->technician->name))?$service_request->technician->name: 'Not Assigned', ['class' => 'control-label fontweight']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
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
                                            {!! Form::label('call_type', trans('quickadmin.service-request.fields.call-type').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', $service_request->call_type, ['class' => 'control-label fontweight']) !!}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Call location -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('call_location', trans('quickadmin.service-request.fields.call-location').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', $service_request->call_location, ['class' => 'control-label fontweight']) !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            {!! Form::label('priority', trans('quickadmin.service-request.fields.priority').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', $service_request->priority, ['class' => 'control-label fontweight']) !!}
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
                                        {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', (!empty($service_request->product->name))?$service_request->product->name:'', ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="partsDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
                                        <div class="form-group">
                                            {!! Form::label('parts', trans('quickadmin.service-request.fields.parts').': ', ['class' => 'control-label']) !!}
                                            @foreach ($service_request->parts as $singleParts)
                                                <span class="label label-info label-many">{{ $singleParts->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('make', trans('quickadmin.service-request.fields.make').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->make, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('model_no', trans('quickadmin.service-request.fields.model-no').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->model_no, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->purchase_from, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group" {{ ($service_request->call_type != "Warranty") ? 'style=display:none' : ''}}>
                                        {!! Form::label('warranty_card_number', trans('quickadmin.service-request.fields.warranty-card-number').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->warranty_card_number, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <!-- <div class="form-group"> -->
                                        {{-- !! Form::label('is_item_in_warrenty', trans('quickadmin.service-request.fields.is-item-in-warrenty').': ', ['class' => 'control-label']) !! --}}
                                        
                                        {{-- !! Form::label('', $service_request->is_item_in_warrenty, ['class' => 'control-label fontweight']) !! --}}
                                    <!-- </div> -->
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('bill_no', trans('quickadmin.service-request.fields.bill-no').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->bill_no, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('bill_date', trans('quickadmin.service-request.fields.bill-date').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', ($service_request->bill_date != "")?App\Helpers\CommonFunctions::setDateFormat($service_request->bill_date):"", ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('serial_no', trans('quickadmin.service-request.fields.serial-no').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->serial_no, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').': ', ['class' => 'control-label']) !!}

                                        {!! Form::label('', $service_request->mop, ['class' => 'control-label fontweight']) !!}

                                    </div>

                                    <div class="form-group" {{ ($service_request->call_type != "Warranty") ? 'style=display:none' : ''}}>
                                        {!! Form::label('online_serial_number', trans('quickadmin.service-request.fields.online-serial-number').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->online_serial_number, ['class' => 'control-label fontweight']) !!}
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

                           
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::label('complain_details', trans('quickadmin.service-request.fields.complain-details').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('complain_details', $service_request->complain_details, ['class' => 'control-label fontweight text-justify']) !!}
                                    
                                </div>
                            </div>
                            @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                            <div class="row">
                                <div class="col-md-6">
                                        {!! Form::label('completion_date', trans('quickadmin.service-request.fields.completion-date').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', App\Helpers\CommonFunctions::setDateFormat($service_request->completion_date), ['class' => 'control-label fontweight']) !!}
                                    
                                </div>
                               
                                <div class="col-md-6">
                                    
                                        <div class="row serviceChargeDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
                                            <div class="col-md-12">
                                                {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').': ', ['class' => 'control-label lablemargin']) !!}

                                                <!-- service charge value label -->
                                                {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->service_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_service_charge'])) !!}

                                                {{-- !! Form::label('', number_format($service_request->service_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_service_charge']) !! --}}

                                            </div>
                                        </div>
                                   
                                        <div class="row installationChargeDiv" {{ ($service_request->service_type == "repair") ? 'style=display:none' : ''}}>
                                            <div class="col-md-12">
                                                {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').': ', ['class' => 'control-label lablemargin']) !!}
                                                
                                                <!-- installation charge value label -->
                                                {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->installation_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_installation_charge'])) !!}

                                                {{-- !! Form::label('', number_format($service_request->installation_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_installation_charge']) !! --}}
                                                
                                            </div>
                                        </div>

                                    @if($service_request->transportation_charge > 0)
                                    <div class="row">
                                        <div class="col-md-12">
                                                {!! Form::label('transportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label']) !!}
                                            
                                                <!-- transportation amount value label -->
                                                {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format(($service_request->transportation_charge),2), ['class' => 'control-label pull-right fontweight', 'id' => 'lbl_trans_amount'])) !!}

                                                {{-- !! Form::label('', number_format(($service_request->transportation_charge),2), ['class' => 'control-label pull-right fontweight', 'id' => 'lbl_trans_amount']) !! --}}
                                        </div>
                                        <div class="col-md-12">

                                            {!! Html::decode(Form::label('', '( <i class="fa fa-rupee"></i> '.number_format($service_request->km_charge,2).' per km)', ['class' => 'control-label pull-right fontsize fontweight', 'id' => 'lbl_trans_amount'])) !!}

                                            {{-- !! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize fontweight', 'id' => 'lbl_trans_amount']) !! --}}
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if(!empty($additional_charge_title) && !empty($service_request->additional_charges))

                                        @if((isset($additional_charge_title['option']) && !empty($additional_charge_title['option']) ) || (isset($additional_charge_title['other']) && !empty( $additional_charge_title['other'])))

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            {!! Form::label('additional_charges', trans('quickadmin.service-request.fields.additional-charges').':', ['class' => 'control-label']) !!}
                                                        </div>
                                                    </div>
                                                    @if(!empty($additional_charge_title['option']))
                                                        @foreach($additional_charge_title['option'] as $key => $single_additional_charge_title)
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="pull-left">
                                                                    {{-- !! Form::label('', $additional_charge_title.': ', ['class' => 'control-label fontsize fontweight']) !! --}}

                                                                    {!! Form::label('', $single_additional_charge_title.': ', ['class' => 'control-label fontsize fontweight']) !!}

                                                                    </div>
                                                                    <div class="pull-right">
                                                                    <i class="fa fa-rupee"></i> 
                                                                    {{-- number_format($service_request->additional_charges,2) --}}
                                                                    {{ number_format($service_request->additional_charges['option'][$key],2) }}
                                                                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                  
                                                    <?php
                                                        if(isset($additional_charge_title['other']) && !empty($additional_charge_title['other'])){
                                                    ?>
                                                    {{-- @if($additional_charge_title['other'] != '') --}}
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="pull-left">
                                                            {{-- !! Form::label('', $additional_charge_title.': ', ['class' => 'control-label fontsize fontweight']) !! --}}

                                                            {!! Form::label('',$additional_charge_title['other'].': ', ['class' => 'control-label fontsize fontweight']) !!}

                                                            </div>
                                                            <div class="pull-right">
                                                            <i class="fa fa-rupee"></i> 
                                                            {{-- number_format($service_request->additional_charges,2) --}}
                                                            {{ number_format($service_request->additional_charges['other'],2) }}
                                                            
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                        }
                                                    ?>
                                                    {{-- @endif --}}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    <hr/>

                                    <div class="row">
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
                                        @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                                            <div class="col-md-12">
                                                {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').''.$paidStatus.':', ['class' => 'control-label']) !!}
                                                
                                                <!-- total amount value label -->
                                                {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format($service_request->amount,2), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount'])) !!}

                                                {{-- !! Form::label('',number_format($service_request->amount,2), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !! --}}
                                            </div>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                            @endif
                            <!-- <div class="row">
                                <div class="col-xs-12 form-group" style="width: 100%;float: left;height:30px;">
                                    
                                    
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('note', trans('quickadmin.service-request.fields.note').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('',$service_request->note, ['class' => 'control-label fontweight text-justify', 'id' => '']) !!}
                                    
                                </div>
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
            <p>&nbsp;</p>

            <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
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
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop
