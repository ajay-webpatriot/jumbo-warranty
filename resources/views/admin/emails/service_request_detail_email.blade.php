@include('admin.emails.header') 

                <table border="0" cellpadding="0" cellspacing="0" class="container" style="width:90%;">
                    <tr>
                        <td align="center" height="35"></td>
                    </tr>

                    <tr>
                        <td align="center" valign="top" class="bodyContent" bgcolor="#ffffff">
                            <div>
                                <h2>Hello {{ $user_name }}!</h2>
                                <span class="divider">―</span>

                                <!-- <h2>Service Request Details</h3> -->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bodyContent" bgcolor="#ffffff">
                        <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">    
                            <div class="panel-heading headerTitle" style="color: #333;
                                background-color: #f5f5f5;
                                border-color: #ddd;padding: 10px 15px;
                                border-bottom: 1px solid transparent;
                                border-top-left-radius: 3px;
                                border-top-right-radius: 3px;font-weight: bold;">
                                @lang('quickadmin.service-request.formTitle')
                            </div>

                            <div class="panel-body table-responsive" style="margin-bottom: 0;
                                border: 0;padding: 15px;min-height: .01%;
                                overflow-x: auto;">
                                <div class="row">
                                    <div class="col-md-12" style="width: 100%;float: left;">

                                        <div class="row">
                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                <div class="row">
                                                    <div class="col-md-6" style="width: 50%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        ">
                                                        {!! Form::label('service_type', trans('quickadmin.service-request.fields.service-type').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}

                                                        {!! Form::label('service_type', ucfirst($service_request->service_type), ['class' => 'control-label fontweight']) !!}


                                                        
                                                    </div>

                                                    <div class="col-md-6" style="float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        {!! Form::label('created_date', trans('quickadmin.service-request.fields.created_date').': ', ['class' => 'control-label lablemargin', 'style' => 'font-weight:bold;']) !!}
                                                        {!! Form::label('created_date', App\Helpers\CommonFunctions::setDateFormat($service_request->created_at), ['class' => 'control-label lablemargin fontweight','readonly' => '']) !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                <!-- Request Status -->
                                               {!! Form::label('status', trans('quickadmin.service-request.fields.status').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                {!! Form::label('status', $service_request->status, ['class' => 'control-label fontweight']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="panel-group" style="margin-bottom: 20px;margin-top:5%;">
                                    
                                    <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">
                                        <div class="panel-heading" style="color: #333;
                                            background-color: #f5f5f5;
                                            border-color: #ddd;
                                            border-bottom: 0;
                                            padding: 10px 15px;
                                            border-top-left-radius: 3px;
                                            border-top-right-radius: 3px;
                                            ">
                                            <span style="color: #3c8dbc;text-decoration: none;">Company & Customer</span>
                                        </div>

                                        <div class="panel-collapse in" role="tabpanel">
                                            <div class="panel-body" style="border-top-color: #ddd;border-top: 1px solid #ddd;padding: 15px;height:140px;">

                                                <div class="row">
                                                    <!-- Company & Customer  -->
                                                    
                                                    <div class="col-md-6" style="width: 50%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', $service_request->company->name, ['class' => 'control-label fontweight']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6" style="float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;"> 
                                                        <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                                            <div class="col-xs-12 ">
                                                                {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', $service_request->customer->firstname.' '.$service_request->customer->lastname, ['class' => 'control-label fontweight']) !!}
                                                                
                                                            </div>
                                                        </div>
                                                    </div> 
                                                </div>

                                                <div class="row">
                                                    
                                                    <div class="col-md-6" style="width: 50%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <!--  added condition to set layput when company is not visible -->
                                                    </div>
                                                    
                                                    <div class="col-md-6" style="float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <div class="row custDiv"  {{ ($service_request->company_id == "") ? 'style=display:none' : ''}}>
                                                            <div class="col-xs-12">
                                                                {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer-address').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
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
                                    <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">
                                        <div class="panel-heading" style="color: #333;
                                            background-color: #f5f5f5;
                                            border-color: #ddd;
                                            border-bottom: 0;
                                            padding: 10px 15px;
                                            border-top-left-radius: 3px;
                                            border-top-right-radius: 3px;
                                            ">
                                            <span style="color: #3c8dbc;text-decoration: none;">Service Center</span>
                                        </div>
                                        <div id="collapseServiceCenter" class="panel-collapse in">
                                            <div class="panel-body" style="border-top-color: #ddd;border-top: 1px solid #ddd;padding: 15px;height:40px;">
                                                <div class="row">

                                                    <!-- Service center -->
                                                    <div class="col-md-6" style="width: 50%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('',  (!empty($service_request->service_center->name))?$service_request->service_center->name:'', ['class' => 'control-label fontweight']) !!}
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
                                                    <div class="col-md-6" style="float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <div class="row techDiv" {{ ($service_request->service_type == "") ? 'style=display:none' : ''}}>
                                                            <div class="col-xs-12">
                                                                {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', (!empty($service_request->technician->name))?$service_request->technician->name: '', ['class' => 'control-label fontweight']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">
                                        <div class="panel-heading" style="color: #333;
                                            background-color: #f5f5f5;
                                            border-color: #ddd;
                                            border-bottom: 0;
                                            padding: 10px 15px;
                                            border-top-left-radius: 3px;
                                            border-top-right-radius: 3px;
                                            "> <span style="color: #3c8dbc;text-decoration: none;" >Call Detail</span></div>
                                        <div id="collapseCallDetail" class="panel-collapse in">
                                            <div class="panel-body" style="border-top-color: #ddd;border-top: 1px solid #ddd;padding: 15px;height:40px;">

                                                <div class="row">
                                                    <!-- Call type  -->
                                                    <div class="col-md-4" style="width: 33.33333333%;float: left;">
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                {!! Form::label('call_type', trans('quickadmin.service-request.fields.call-type').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', $service_request->call_type, ['class' => 'control-label fontweight']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Call location -->
                                                    <div class="col-md-4" style="width: 33.33333333%;float: left;">
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                {!! Form::label('call_location', trans('quickadmin.service-request.fields.call-location').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', $service_request->call_location, ['class' => 'control-label fontweight']) !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Priority -->
                                                    <div class="col-md-4" style="width: 33.33333333%;float: left;">
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                {!! Form::label('priority', trans('quickadmin.service-request.fields.priority').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                {!! Form::label('', $service_request->priority, ['class' => 'control-label fontweight']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">
                                        <div class="panel-heading" style="color: #333;
                                            background-color: #f5f5f5;
                                            border-color: #ddd;
                                            border-bottom: 0;
                                            padding: 10px 15px;
                                            border-top-left-radius: 3px;
                                            border-top-right-radius: 3px;
                                            "> <span style="color: #3c8dbc;text-decoration: none;">Product</span></div>
                                        <div id="collapseProduct" class="panel-collapse in">
                                            <div class="panel-body" style="border-top-color: #ddd;border-top: 1px solid #ddd;padding: 15px;height:140px;">
                                                <div class="row">
                                                    <div class="col-md-6" style="width: 50%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">

                                                        <div class="form-group">
                                                            {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', (!empty($service_request->product->name))?$service_request->product->name:'', ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="partsDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
                                                            <div class="form-group">
                                                                {!! Form::label('parts', trans('quickadmin.service-request.fields.parts').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                @foreach ($service_request->parts as $singleParts)
                                                                    <span class="label label-info label-many" style="
                                                                    margin-right: 5px;
                                                                    margin-bottom: 5px!important;
                                                                    display: inline-block;
                                                                    background-color: #00c0ef!important;
                                                                    border-radius: .25em;
                                                                    padding: .2em .6em .3em;
                                                                    color: #fff;
                                                                    margin-top: 5px;
                                                                    text-align: center;
                                                                    white-space: nowrap;
                                                                    vertical-align: baseline;
                                                                    line-height: 1;">{{ $singleParts->name }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('make', trans('quickadmin.service-request.fields.make').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', $service_request->make, ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('model_no', trans('quickadmin.service-request.fields.model-no').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', $service_request->model_no, ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('is_item_in_warrenty', trans('quickadmin.service-request.fields.is-item-in-warrenty').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            
                                                            {!! Form::label('', $service_request->is_item_in_warrenty, ['class' => 'control-label fontweight']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6" style="float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <div class="form-group">
                                                            {!! Form::label('bill_no', trans('quickadmin.service-request.fields.bill-no').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', $service_request->bill_no, ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('bill_date', trans('quickadmin.service-request.fields.bill-date').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', ($service_request->bill_date != "")?App\Helpers\CommonFunctions::setDateFormat($service_request->bill_date):"", ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('serial_no', trans('quickadmin.service-request.fields.serial-no').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', $service_request->serial_no, ['class' => 'control-label fontweight']) !!}
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                            {!! Form::label('', $service_request->purchase_from, ['class' => 'control-label fontweight']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}

                                                            {!! Form::label('', $service_request->mop, ['class' => 'control-label fontweight']) !!}

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default" style="margin-bottom: 20px;
                                background-color: #fff;
                                border: 1px solid transparent;
                                border-radius: 4px;
                                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                                box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #ddd;">
                                        <div class="panel-heading" style="color: #333;
                                            background-color: #f5f5f5;
                                            border-color: #ddd;
                                            border-bottom: 0;
                                            padding: 10px 15px;
                                            border-top-left-radius: 3px;
                                            border-top-right-radius: 3px;
                                            "> <span style="color: #3c8dbc;text-decoration: none;" >Other</span></div>
                                        <div id="collapseOther" class="panel-collapse in">
                                            <div class="panel-body" style="border-top-color: #ddd;border-top: 1px solid #ddd;padding: 15px;height:210px;">

                                               
                                                <div class="row">
                                                    <div class="col-md-12" style="width: 100%;float: left;">
                                                        {!! Form::label('complain_details', trans('quickadmin.service-request.fields.complain-details').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}

                                                        {!! Form::label('complain_details', $service_request->complain_details, ['class' => 'control-label fontweight']) !!}
                                                        
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6" style="width: 48%;    float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        ">
                                                        {!! Form::label('completion_date', trans('quickadmin.service-request.fields.completion-date').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                        {!! Form::label('',App\Helpers\CommonFunctions::setDateFormat( $service_request->completion_date), ['class' => 'control-label fontweight']) !!}
                                                    </div>

                                                    <div class="col-md-6" style="width:45%;float: left;    
                                                        position: relative;
                                                        min-height: 1px;
                                                        padding-right: 15px;
                                                        padding-left: 15px;">
                                                        <div class="row serviceChargeDiv" {{ ($service_request->service_type == "installation") ? 'style=display:none' : ''}}>
                                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                                {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').': ', ['class' => 'control-label lablemargin', 'style' => 'font-weight:bold;']) !!}

                                                                <!-- service charge value label -->
                                                                {!! Form::label('', number_format($service_request->service_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_service_charge', 'style' => 'float:right;']) !!}

                                                            </div>
                                                        </div>
                                                       
                                                        <div class="row installationChargeDiv" {{ ($service_request->service_type == "repair") ? 'style=display:none' : ''}}>
                                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                                {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').': ', ['class' => 'control-label lablemargin', 'style' => 'font-weight:bold;']) !!}
                                                                
                                                                <!-- installation charge value label -->
                                                                {!! Form::label('', number_format($service_request->installation_charge,2), ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_installation_charge','style' => 'float:right;']) !!}
                                                                
                                                            </div>
                                                        </div>
                                                        @if($service_request->km_distance > 0)
                                                        <div class="row">
                                                            <div class="col-md-12"  style="width: 100%;float: left;">
                                                                    {!! Form::label('transportation_charge', trans('quickadmin.service-request.fields.transportation-charge').':', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                
                                                                    <!-- total amount value label -->
                                                                    {!! Form::label('', number_format(($service_request->km_distance * $service_request->km_charge),2), ['class' => 'control-label pull-right', 'id' => 'lbl_trans_amount','style' => 'float:right;']) !!}
                                                            </div>
                                                            <div class="col-md-12"  style="width: 100%;float: left;">

                                                                {!! Form::label('', '('.number_format($service_request->km_charge,2).' rs per km)', ['class' => 'control-label pull-right fontsize', 'id' => 'lbl_trans_amount','style' => 'float:right;font-size:11px;']) !!}
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if(!empty($service_request->additional_charges))
                                                        <div class="row">
                                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        {!! Form::label('additional_charges', trans('quickadmin.service-request.fields.additional-charges').':', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="pull-left" style="width:50%;float:left;">
                                                                        {!! Form::label('', $service_request->additional_charges_title.': ', ['class' => 'control-label fontsize', ]) !!}
                                                                    
                                                                        </div>
                                                                        <div class="pull-right" style="float:right;">
                                                                            {{number_format($service_request->additional_charges,2)}}
                                                                        
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <hr style="margin-top: 10px !important;
                                                                margin-bottom: 10px !important;
                                                                height: 0;
                                                                border-top: 1px solid #eee;"/>

                                                        <div class="row">
                                                            <div class="col-md-12" style="width: 100%;float: left;">
                                                                    {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').':', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}
                                                                
                                                                    <!-- total amount value label -->
                                                                    {!! Form::label('',number_format($service_request->amount,2), ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount', 'style' => 'float:right;']) !!}
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12 form-group" style="width: 100%;float: left;height:30px;">
                                                        
                                                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12 form-group" style="width: 100%;float: left;">
                                                        {!! Form::label('note', trans('quickadmin.service-request.fields.note').': ', ['class' => 'control-label', 'style' => 'font-weight:bold;']) !!}

                                                        {!! Form::label('',$service_request->note, ['class' => 'control-label fontweight', 'id' => '']) !!}
                                                        
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                