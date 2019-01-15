@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-request.title')</h3> -->
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
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('service_type', trans('quickadmin.service-request.fields.service-type').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('service_type', $service_request->service_type, ['class' => 'control-label fontweight']) !!}


                                    
                                </div>

                                <div class="col-md-6">
                                    {!! Form::label('created_date', trans('quickadmin.service-request.fields.created_date').': ', ['class' => 'control-label lablemargin','readonly' => '']) !!}
                                    {!! Form::label('created_date', '08-01-2018', ['class' => 'control-label lablemargin fontweight','readonly' => '']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Request Status -->
                           {!! Form::label('status', trans('quickadmin.service-request.fields.status').': ', ['class' => 'control-label']) !!}
                            {!! Form::label('status', $service_request->status, ['class' => 'control-label fontweight']) !!}
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
                                @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
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
                                @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
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
                                                {{$service_request->customer->address_2}}
                                                <br/>
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
                                            {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').': ', ['class' => 'control-label']) !!}
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
                                <div class="col-md-6">
                                    <div class="row techDiv" {{ ($service_request->service_type == "") ? 'style=display:none' : ''}}>
                                        <div class="col-xs-12">
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').': ', ['class' => 'control-label']) !!}
                                            {!! Form::label('', (!empty($service_request->technician->name))?$service_request->technician->name: '', ['class' => 'control-label fontweight']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
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
                    <div class="panel-heading"> <a data-toggle="collapse" href="#collapseProduct">Product</a></div>
                    <div id="collapseProduct" class="panel-collapse in">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group">
                                        {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', (!empty($service_request->product->name))?$service_request->product->name:'', ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="partsDiv" {{ ($service_request->service_type != "repair") ? 'style=display:none' : ''}}>
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
                                        {!! Form::label('is_item_in_warrenty', trans('quickadmin.service-request.fields.is-item-in-warrenty').': ', ['class' => 'control-label']) !!}
                                        
                                        {!! Form::label('', $service_request->is_item_in_warrenty, ['class' => 'control-label fontweight']) !!}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('bill_no', trans('quickadmin.service-request.fields.bill-no').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->bill_no, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('bill_date', trans('quickadmin.service-request.fields.bill-date').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->bill_date, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('serial_no', trans('quickadmin.service-request.fields.serial-no').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->serial_no, ['class' => 'control-label fontweight']) !!}
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('purchase_from', trans('quickadmin.service-request.fields.purchase-from').': ', ['class' => 'control-label']) !!}
                                        {!! Form::label('', $service_request->purchase_from, ['class' => 'control-label fontweight']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('mop', trans('quickadmin.service-request.fields.mop').': ', ['class' => 'control-label']) !!}

                                        {!! Form::label('', $service_request->mop, ['class' => 'control-label fontweight']) !!}

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

                           
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::label('complain_details', trans('quickadmin.service-request.fields.complain-details').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('complain_details', $service_request->complain_details, ['class' => 'control-label fontweight']) !!}
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('completion_date', trans('quickadmin.service-request.fields.completion-date').': ', ['class' => 'control-label']) !!}
                                    {!! Form::label('', $service_request->completion_date, ['class' => 'control-label fontweight']) !!}
                                </div>

                                <div class="col-md-6">
                                    <div class="row serviceChargeDiv" {{ ($service_request->service_type != "repair") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('service_charge', trans('quickadmin.service-request.fields.service-charge').': ', ['class' => 'control-label lablemargin']) !!}

                                            <!-- service charge value label -->
                                            {!! Form::label('', $service_request->service_charge, ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_service_charge']) !!}

                                        </div>
                                    </div>
                                   
                                    <div class="row installationChargeDiv" {{ ($service_request->service_type != "installation") ? 'style=display:none' : ''}}>
                                        <div class="col-md-12">
                                            {!! Form::label('installation_charge', trans('quickadmin.service-request.fields.installation-charge').': ', ['class' => 'control-label lablemargin']) !!}
                                            
                                            <!-- installation charge value label -->
                                            {!! Form::label('', $service_request->installation_charge, ['class' => 'control-label lablemargin pull-right fontweight','id' => 'lbl_installation_charge']) !!}
                                            
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
                                                    {!! Form::label('charges_for', trans('quickadmin.service-request.fields.charges_for').': ', ['class' => 'control-label fontsize']) !!}
                                                
                                                    {!! Form::label('', $additional_charge_title, ['class' => 'control-label lablemargin fontweight','id' => 'lbl_installation_charge']) !!}
                                                    
                                                </div>

                                                <div class="col-sm-4 lablemargin">
                                                    <div class="pull-left">
                                                    {!! Form::label('amount', trans('quickadmin.service-request.fields.amount').': ', ['class' => 'control-label fontsize']) !!}

                                                    </div>
                                                    <div class="pull-right">
                                                        {{$service_request->additional_charges}}
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr/>

                                    <div class="row">
                                        <div class="col-md-12">
                                                {!! Form::label('totalamount', trans('quickadmin.service-request.fields.totalamount').':', ['class' => 'control-label']) !!}
                                            
                                                <!-- total amount value label -->
                                                {!! Form::label('',$service_request->amount, ['class' => 'control-label pull-right', 'id' => 'lbl_total_amount']) !!}
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!! Form::label('note', trans('quickadmin.service-request.fields.note').': ', ['class' => 'control-label']) !!}

                                    {!! Form::label('',$service_request->note, ['class' => 'control-label fontweight', 'id' => '']) !!}
                                    
                                </div>
                            </div>
                            
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
