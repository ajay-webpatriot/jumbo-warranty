@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-request.title')</h3> -->
    @can('service_request_create')
    <p class="text-right">
        <a href="{{ route('admin.service_requests.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    @can('service_request_delete')
    <!-- <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.service_requests.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.service_requests.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p> -->
    @endcan

    <!-- Filter portion start -->
    <div class="panel panel-default">
        <div class="panel-heading headerTitle" href="#collapseAdvanceFilter" data-toggle="collapse">
            <!-- <a href="#"> -->Advance Filters<!-- </a> -->
            <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
        </div>
        <div id="collapseAdvanceFilter" class="panel-collapse collapse in" role="tabpanel">
            <div class="panel-body">
                <!-- Company & Customer -->
                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'', ['class' => 'control-label']) !!}

                               
                                    {!! Form::select('filter_company', $companies, ($request->session()->has('filter_company'))? $request->session()->get('filter_company'):'', ['class' => 'form-control select2','onchange' => 'requestCustomerFilter(this)', 'id' => 'filter_company','style' => 'width:100%']) !!}
                            </div>
                        </div>
                    </div>
                    @if($request->session()->has('filter_company'))
                    <div class="filterCompanyDetails">
                    @else
                    <div class="filterCompanyDetails" style="display: none;">
                    @endif     
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('filter_customer', $customers, ($request->session()->has('filter_customer'))? $request->session()->get('filter_customer'):'', ['class' => 'form-control select2', 'id' => 'filter_customer','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('customer_id'))
                                    <p class="help-block">
                                        {{ $errors->first('customer_id') }}
                                    </p>
                                    @endif
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('filter_product', $products,($request->session()->has('filter_product'))? $request->session()->get('filter_product'):'', ['class' => 'form-control select2', 'id' => 'filter_product','style' => 'width:100%']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').'', ['class' => 'control-label']) !!}
                                   
                                    {!! Form::select('filter_service_center', $service_centers, ($request->session()->has('filter_service_center'))? $request->session()->get('filter_service_center'):'', ['class' => 'form-control select2','onchange' => 'requestTechnicianFilter(this)', 'id' => 'filter_service_center','style' => 'width:100%']) !!}
                                </div>
                            </div> 
                        </div>
                        @if($request->session()->has('filter_service_center'))
                        <div class="col-md-4 filterTechnicianDiv">
                        @else
                        <div class="col-md-4 filterTechnicianDiv" style="display: none;">
                        @endif
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}

                                    @if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                                        {!! Form::text('technician_name', auth()->user()->name, ['class' => 'form-control', 'placeholder' => 'Service Center Name','disabled' => '']) !!}
                                        {!! Form::hidden('filter_technician', auth()->user()->id, ['class' => 'form-control', 'id' => 'filter_technician']) !!}
                                    @else
                                        {!! Form::select('filter_technician', $technicians, ($request->session()->has('filter_technician'))? $request->session()->get('filter_technician'):'', ['class' => 'form-control select2', 'id' => 'filter_technician','style' => 'width: 100%;']) !!}
                                    @endif
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-4 service_center_balance" <?=(!empty(session('filter_service_center')))? 'style="display: block;"':'style="display: none;"'?>>
                            <div class="row"> 
                                <div class="col-lg-6 pull-right balance_details">
                                    {!! Form::label('', '', ['class' => 'control-label']) !!}
                                    {!! Form::label('', trans('quickadmin.service-request.fields.total-paid').'  : ', ['class' => 'control-label']) !!}

                                    {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format(($total_paid_amount),2), ['class' => 'control-label pull-right fontweight', 'id' => 'total_paid_amount'])) !!}

                                    <br/>
                                    {!! Form::label('', trans('quickadmin.service-request.fields.total-due').' : ', ['class' => 'control-label']) !!}

                                    {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format(($total_due_amount),2), ['class' => 'control-label pull-right fontweight', 'id' => 'total_due_amount'])) !!}
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                {!! Form::label('request_status', trans('quickadmin.service-request.fields.status').'', ['class' => 'control-label']) !!}

                                {!! Form::select('request_status', $request_stauts, ($request->session()->has('filter_request_status'))? $request->session()->get('filter_request_status'):'', ['class' => 'form-control select2','onchange' => 'requestStatusFilter(this)', 'id' => 'filter_request_status','style' => 'width:100%']) !!}

                            </div>

                            <div class="col-md-4 col-xs-12">
                                {!! Form::label('request_type', trans('quickadmin.service-request.fields.service-type').'', ['class' => 'control-label']) !!}

                                {!! Form::select('request_type', $request_type, ($request->session()->has('filter_request_type'))? $request->session()->get('filter_request_type'):'', ['class' => 'form-control select2','onchange' => 'requestStatusFilter(this)', 'id' => 'filter_request_type','style' => 'width:100%']) !!}
                            </div>

                            <div class="col-md-4 col-xs-12 pull-right">
                                <!-- <div class="row"> -->
                                    <!-- <div class="col-xs-12"> -->
                                        <label class="control-label"></label>
                                        <p class="paddingFormele text-right">
                                            <a href="{{url('/admin/clearRequestFilterAjax')}}"  id="clearRequestFilter" class="btn btn-danger">@lang('quickadmin.service-request.clear-filter')</a>
                                        </p>
                                    <!-- </div> -->
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                <div class="row">
                    <div class="filterCompanyDetails">   
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('filter_customer', $customers, ($request->session()->has('filter_customer'))? $request->session()->get('filter_customer'):'', ['class' => 'form-control select2', 'id' => 'filter_customer','style' => 'width:100%']) !!}
                                    <p class="help-block"></p>
                                    @if($errors->has('customer_id'))
                                    <p class="help-block">
                                        {{ $errors->first('customer_id') }}
                                    </p>
                                    @endif
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('filter_product', $products,($request->session()->has('filter_product'))? $request->session()->get('filter_product'):'', ['class' => 'form-control select2', 'id' => 'filter_product','style' => 'width:100%']) !!}
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label class="control-label"></label>
                                        <p class="paddingFormele text-right">
                                            <a href="{{url('/admin/clearRequestFilterAjax')}}"  id="clearRequestFilter" class="btn btn-danger">@lang('quickadmin.service-request.clear-filter')</a>
                                            
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                {!! Form::label('request_status', trans('quickadmin.service-request.fields.status').'', ['class' => 'control-label']) !!}

                                {!! Form::select('request_status', $request_stauts, ($request->session()->has('filter_request_status'))? $request->session()->get('filter_request_status'):'', ['class' => 'form-control select2','onchange' => 'requestStatusFilter(this)', 'id' => 'filter_request_status','style' => 'width:100%']) !!}

                            </div>

                            <div class="col-md-4 col-xs-12">
                                {!! Form::label('request_type', trans('quickadmin.service-request.fields.service-type').'', ['class' => 'control-label']) !!}

                                {!! Form::select('request_type', $request_type, ($request->session()->has('filter_request_type'))? $request->session()->get('filter_request_type'):'', ['class' => 'form-control select2','onchange' => 'requestStatusFilter(this)', 'id' => 'filter_request_type','style' => 'width:100%']) !!}
                            </div>

                            @if((auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')))
                            <div class="col-md-4 service_center_balance" style="display: block;">
                                <div class="row"> 
                                    <div class="col-lg-6 pull-right balance_details">
                                        {!! Form::label('', '', ['class' => 'control-label']) !!}
                                        {!! Form::label('', trans('quickadmin.service-request.fields.total-paid').'  : ', ['class' => 'control-label']) !!}

                                        {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format(($total_paid_amount),2), ['class' => 'control-label pull-right fontweight', 'id' => 'total_paid_amount'])) !!}

                                        <br/>
                                        {!! Form::label('', trans('quickadmin.service-request.fields.total-due').' : ', ['class' => 'control-label']) !!}

                                        {!! Html::decode(Form::label('', '<i class="fa fa-rupee"></i> '.number_format(($total_due_amount),2), ['class' => 'control-label pull-right fontweight', 'id' => 'total_due_amount'])) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if((auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')))
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-4 filterTechnicianDiv">
                            
                                    <div class="row"> 
                                        <div class="col-xs-12">
                                            {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}

                                            {!! Form::select('filter_technician', $technicians, ($request->session()->has('filter_technician'))? $request->session()->get('filter_technician'):'', ['class' => 'form-control select2', 'id' => 'filter_technician','style' => 'width:100%']) !!}
                                            
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label class="control-label"></label>
                                            <p class="paddingFormele text-right">
                                                <a href="{{url('/admin/clearRequestFilterAjax')}}"  id="clearRequestFilter" class="btn btn-danger">@lang('quickadmin.service-request.clear-filter')</a>
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-4 col-sm-12" style="margin-top:10px">

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="dateRangeFilter">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter portion end -->
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.title')
        </div>
        <div class="panel-body table-responsive">
            @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            
                <table  id="technician" class="display" width="100%">
                {{--<!-- <table  id="technician" class="display {{ count($service_requests) > 0 ? 'datatable' : '' }} @can('service_request_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan"> -->--}}
                    <thead>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.request-id')</th>
                            {{--@can('service_request_delete')
                                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" class="dt-body-center" id="select-all" /></th>@endif
                            @endcan--}}
                            
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.service-center')</th> -->
                            <!-- <th>@lang('quickadmin.service-request.fields.technician')</th> -->
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <th>@lang('quickadmin.service-request.fields.created_date')</th>
                            <th>@lang('quickadmin.service-request.fields.created_by')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
                            <!-- paid column will be visiblke to only service center admin) -->
                            <th>@lang('quickadmin.qa_paid')</th>
                            @endif
                            
                            <th>@lang('quickadmin.qa_action')</th>
                            {{--@if( request('show_deleted') == 1 )
                            <th>Action</th>
                            @else
                            <th>&nbsp;</th>
                            @endif--}}
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            @elseif(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            
                <table id="company" class="display table table-bordered table-striped dt-select dataTable no-footer datatable" width="100%">
                    <thead>
                        <tr>
                        
                            @can('service_request_delete')
                                <th style="text-align:center;"><input type="checkbox" class="dt-body-center" id="select-all" /></th>
                            @endcan
                            <th>@lang('quickadmin.service-request.fields.request-id')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.company')</th> -->
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.technician')</th> -->
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.amount')</th> -->
                            <th>@lang('quickadmin.service-request.fields.created_date')</th>
                            <th>@lang('quickadmin.service-request.fields.created_by')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <th>@lang('quickadmin.qa_action')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            @else
                <table id="serviceRequest" class="display table table-bordered table-striped dt-select dataTable no-footer datatable" width="100%">
                    <thead>
                        <tr>
                            
                            @can('service_request_delete')
                                <th style="text-align:center;"><input type="checkbox" class="dt-body-center select-checkbox" id="select-all" /></th>
                            @endcan
                            <th>@lang('quickadmin.service-request.fields.request-id')</th>
                            <th>@lang('quickadmin.service-request.fields.company')</th>
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <th>@lang('quickadmin.service-request.fields.service-center')</th>
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <th>@lang('quickadmin.service-request.fields.created_date')</th>
                            <th>@lang('quickadmin.service-request.fields.created_by')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <th>@lang('quickadmin.qa_paid')</th>
                            <th>@lang('quickadmin.qa_action')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop

@section('javascript') 
    <script src="{{ url('adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        var daterangeStartValue = "";
        var daterangeEndValue = "";
        // var startdate = "{{ $request->session()->get('filter_start_date') }}";
        // var enddate = "{{ $request->session()->get('filter_end_date') }}";
        // startdate = new date(startdate);
        $(function(){
            // daterangeStartValue = "{{ $request->session()->get('filter_start_date') }}";
            // daterangeEndValue = "{{ $request->session()->get('filter_end_date') }}";

            $('#dateRangeFilter').daterangepicker({
                opens: 'right',
                locale: {
                    format: "{{ config('app.date_format_moment') }}"
                }
            }, function(start, end, label) {
                // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));

                daterangeStartValue = start.format('YYYY-MM-DD');
                daterangeEndValue=end.format('YYYY-MM-DD');
                tableServiceRequest.draw();
           });

            //    // set date during initialization
            //    daterangeStartValue=moment($('#dateRangeFilter').val().split(" - ")[0],'DD/MM/YYYY').format('YYYY-MM-DD');
            //    daterangeEndValue=moment($('#dateRangeFilter').val().split(" - ")[1],'DD/MM/YYYY').format('YYYY-MM-DD');
            // if("{{ $request->session()->has('filter_start_date') }}" == 1 && "{{ $request->session()->has('filter_end_date') }}" == 1){
            //     console.log(startdate);
            //     daterangeStartValue=moment(startdate.split(" - ")[0],'DD/MM/YYYY').format('YYYY-MM-DD');
            //     daterangeEndValue=moment(enddate.split(" - ")[1],'DD/MM/YYYY').format('YYYY-MM-DD');

            // }else{
                daterangeStartValue=moment($('#dateRangeFilter').val().split(" - ")[0],'DD/MM/YYYY').format('YYYY-MM-DD');
                daterangeEndValue=moment($('#dateRangeFilter').val().split(" - ")[1],'DD/MM/YYYY').format('YYYY-MM-DD');
            // }
        });

        @can('service_request_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}'; @endif
        @endcan
        window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}';
        
        @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            // service center admin and technician

            @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
                var tableColumns =  [
                    { "data": "sr_no" },
                    // { "data": "checkbox" },
                    { "data": "customer","name": "customer"},
                    { "data": "service_type","name": "service_type" },
                    /*{ "data": "service_center","name": "service_center" },*/
                    { "data": "product","name": "product" },
                    { "data": "amount","name": "amount" },
                    { "data": "created_at","name": "created_at" },
                    { "data": "request_status","name": "request_status" },
                    { "data": "amount_paid" },
                    { "data": "action" }
                ];

                var tableColumnDefs = [{
                    "orderable": true,
                    // "className": 'dt-body-center select-checkbox',
                    "className": 'dt-body-center',
                    "targets":   0,
                    "visible": true,
                    "searchable": true
                },{
                    "orderable": true,
                    "targets":   [1,2,3,4,5]
                },
                {
                    "class": "text-right",
                    "targets":   4
                },
                {
                    "class": "text-center",
                    "targets":   5
                },
                {
                    "class": "text-center",
                    "targets":   6
                },
                {
                    "orderable": false,
                    "targets":   8
                }];
            @else
                var tableColumns =  [
                    { "data": "sr_no" },
                    // { "data": "checkbox" },
                    { "data": "customer","name": "customer"},
                    { "data": "service_type","name": "service_type" },
                    /*{ "data": "service_center","name": "service_center" },*/
                    { "data": "product","name": "product" },
                    { "data": "amount","name": "amount" },
                    { "data": "created_at","name": "created_at" },
                    { "data": "request_status","name": "request_status" },
                    { "data": "action" }
                ];

                var tableColumnDefs = [{
                    "orderable": true,
                    // "className": 'dt-body-center select-checkbox',
                    "className": 'dt-body-center',
                    "targets":   0,
                    "visible": true,
                    "searchable": true
                },{
                    "orderable": true,
                    "targets":   [1,2,3,4,5]
                },
                {
                    "class": "text-right",
                    "targets":   4
                },
                {
                    "class": "text-center",
                    "targets":   5
                },
                {
                    "class": "text-center",
                    "targets":   6
                },
                {
                    "orderable": false,
                    "targets":   7
                }];
            @endif
            var tableServiceRequest = $('#technician').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                retrieve: true,
                dom: 'lBfrtip<"actions">',
                columnDefs: [],
                "iDisplayLength": 10,
                "aaSorting": [],
                buttons: [
                    {
                        extend: 'pdf',
                        text: window.pdfButtonTrans,
                        orientation: 'landscape',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function (doc) {
                            // var iColumns = $('#technician thead th').length;
                            // set 100% width fot table in pdf
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                            // set alignment of amount and request status column for PDF screen
                            var rowCount = document.getElementById("technician").rows.length;
                            
                            for (i = 0; i < rowCount; i++) {
                                    doc.content[1].table.body[i][0].alignment = 'center';
                                    doc.content[1].table.body[i][4].alignment = 'right';
                                    doc.content[1].table.body[i][5].alignment = 'center';
                                 
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function (win) {
                            // set alignment of amount and request status column for print screen
                            $(win.document.body).find('table tbody td:nth-child(1)').css('text-align', 'center');
                            $(win.document.body).find('table tbody td:nth-child(5)').css('text-align', 'right');
                            $(win.document.body).find('table tbody td:nth-child(6)').css('text-align', 'center');
                        }
                    }
                ],
                "ajax":{
                        "url": APP_URL+"/admin/DataTableServiceRequestAjax",
                        "type":"POST",
                        "dataType": "json",
                        // "data":{"_token": "{{csrf_token()}}"}
                        "data":function(data) {
                            data.company = $('#filter_company').val();
                            data.customer = $('#filter_customer').val();
                            data.product = $('#filter_product').val();
                            data.serviceCenter = $('#filter_service_center').val();
                            data.technician = $('#filter_technician').val();
                            data.status = $('#filter_request_status').val();
                            data.type = $('#filter_request_type').val();
                            data.startdate =daterangeStartValue;
                            data.enddate =daterangeEndValue;
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": tableColumns,
                "columnDefs": tableColumnDefs
            });
            
        @elseif(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
            // company admin and company user
            var tableServiceRequest = $('#company').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[ 1, "desc" ]],
                retrieve: true,
                dom: 'lBfrtip<"actions">',
                columnDefs: [],
                "iDisplayLength": 10,
                "aaSorting": [],
                buttons: [
                    {
                        extend: 'pdf',
                        text: window.pdfButtonTrans,
                        orientation: 'landscape',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [1, 2, 3, 4,5]
                        },
                        customize: function (doc) {
                            // var iColumns = $('#company thead th').length;

                            // set 100% width fot table in pdf
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                            // set alignment of amount and request status column for PDF screen
                            var rowCount = document.getElementById("company").rows.length;
                            
                            for (i = 0; i < rowCount; i++) {
                                    doc.content[1].table.body[i][0].alignment = 'center';
                                    doc.content[1].table.body[i][4].alignment = 'center';
                                    // doc.content[1].table.body[i][5].alignment = 'center';
                                 
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            // columns: ':visible'
                            columns: [1, 2, 3, 4, 5]
                        },
                        customize: function (win) {
                            // set alignment of amount and request status column for print screen
                            $(win.document.body).find('table tbody td:nth-child(1)').css('text-align', 'center');
                            $(win.document.body).find('table tbody td:nth-child(5)').css('text-align', 'center');
                            // $(win.document.body).find('table tbody td:nth-child(6)').css('text-align', 'center');
                        }
                    }
                ],
                "ajax":{
                        "url": APP_URL+"/admin/DataTableServiceRequestAjax",
                        "type":"POST",
                        "dataType": "json",
                        // "data":{"_token": "{{csrf_token()}}"}
                        "data":function(data) {
                            // data.company = $('#filter_company').val();
                            data.customer = $('#filter_customer').val();
                            data.product = $('#filter_product').val();
                            data.status = $('#filter_request_status').val();
                            data.type = $('#filter_request_type').val();
                            data.startdate =daterangeStartValue;
                            data.enddate =daterangeEndValue;
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": [
                    
                    { "data": "checkbox" },
                    { "data": "sr_no" },
                    // { "data": "company_name" },
                    { "data": "customer" },
                    { "data": "service_type" },
                    { "data": "product" },
                    // { "data": "amount" },
                    { "data": "created_at" },
                    { "data": "created_by","name": "created_by" },
                    { "data": "request_status" },
                    { "data": "action" }
                ],
                "columnDefs": [{
                    "orderable": false,
                    "className": 'select-checkbox',
                    "targets":   0,
                    "searchable": false
                },{
                    "orderable": true,
                    "className": 'dt-body-center',
                    "targets":   1,
                    "visible": true,
                    "searchable": true
                },
                {
                    "class": "text-center",
                    "targets":   5
                },
                {
                    "class": "text-center",
                    "targets":   6
                },
                {
                    "class": "text-center",
                    "targets":   7
                },
                {
                    "orderable": false,
                    "targets":   8
                }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('data-entry-id', aData.sr_no);
                },
                "drawCallback": function( settings ) {
                    var api = this.api();
                    // Output the data for the visible rows to the browser's console
                    
                    if(api.rows( {page:'current'} ).data().length > 0)
                    {
                        if($('#company').parent().find(".actions").length == 0 )
                        {
                            // set bulk delete button after table draw
                            if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                $('#company').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                            }
                        }
                    }
                    else
                    {
                        $('#serviceRequest').parent().find(".actions").remove();
                    }
                }	
            });

        @else
            // admin and super admin
            var tableServiceRequest = $('#serviceRequest').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[ 1, "desc" ]],
                retrieve: true,
                dom: 'lBfrtip<"actions">',
                columnDefs: [],
                "iDisplayLength": 10,
                "aaSorting": [],
                buttons: [
                    {
                        extend: 'pdf',
                        text: window.pdfButtonTrans,
                        orientation: 'landscape',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                        },
                        customize: function (doc) {
                            // var iColumns = $('#company thead th').length;

                            // set 100% width fot table in pdf
                            // doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            
                            // set alignment of amount and request status column for PDF screen
                            var rowCount = document.getElementById("serviceRequest").rows.length;
                            
                            for (i = 0; i < rowCount; i++) {
                                    doc.content[1].table.body[i][0].alignment = 'center';
                                    doc.content[1].table.body[i][6].alignment = 'right';
                                    doc.content[1].table.body[i][7].alignment = 'center';
                                    doc.content[1].table.body[i][8].alignment = 'center';
                                 
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            // columns: ':visible'
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                        },
                        customize: function (win) {
                            // set alignment of amount and request status column for print screen
                            $(win.document.body).find('table tbody td:nth-child(1)').css('text-align', 'center');
                            $(win.document.body).find('table tbody td:nth-child(7)').css('text-align', 'right');
                            $(win.document.body).find('table tbody td:nth-child(8)').css('text-align', 'center');
                            $(win.document.body).find('table tbody td:nth-child(9)').css('text-align', 'center');
                        }
                    }
                ],
                "ajax":{
                        "url": APP_URL+"/admin/DataTableServiceRequestAjax",
                        "type":"POST",
                        "dataType": "json",
                        // "data":{"_token": "{{csrf_token()}}"}
                        "data":function(data) {
                            data.company = $('#filter_company').val();
                            data.status = $('#filter_request_status').val();
                            data.type = $('#filter_request_type').val();
                            data.customer = $('#filter_customer').val();
                            data.product = $('#filter_product').val();
                            data.serviceCenter = $('#filter_service_center').val();
                            data.technician = $('#filter_technician').val();
                            data.startdate =daterangeStartValue;
                            data.enddate =daterangeEndValue;
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": [
                    
                    { "data": "checkbox" },
                    { "data": "sr_no" },
                    { "data": "company_name" },
                    { "data": "customer" },
                    { "data": "service_type" },
                    { "data": "service_center" },
                    { "data": "product" },
                    { "data": "amount" },
                    { "data": "created_at" },
                    { "data": "created_by","name": "created_by" },
                    { "data": "request_status" },
                    { "data": "amount_paid" },
                    { "data": "action" }
                ],
                // columnDefs: [ {
                //     orderable: false,
                //     className: 'dt-body-center',
                //     targets:   0
                // } ]
                "columnDefs": [{
                    "orderable": false,
                    "className": ' select-checkbox',
                    "targets":   0,
                    "searchable": false
                },{
                    "orderable": true,
                    "className": 'dt-body-center',
                    "targets":   1,
                    "visible": true,
                    "searchable": true
                },{
                    "class": "text-right",
                    "targets":   7
                },{
                    "class": "text-center",
                    "targets":   8
                },{
                    "orderable": true,
                    "class": "text-center",
                    "targets":   9
                },{
                    "orderable": true,
                    "class": "text-center",
                    "targets":   10
                },{
                    "orderable": true,
                    "targets":   11,
                    "searchable": false
                },{
                    "orderable": false,
                    "targets":   12,
                    "searchable": false
                }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('data-entry-id', aData.sr_no);
                },
                "drawCallback": function( settings ) {
                    var api = this.api();
                    // Output the data for the visible rows to the browser's console
                    
                    if(api.rows( {page:'current'} ).data().length > 0)
                    {
                        if($('#serviceRequest').parent().find(".actions").length == 0 )
                        {
                            // set bulk delete button after table draw
                            if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                $('#serviceRequest').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                            }
                        }

                    }
                    else
                    {
                        $('#serviceRequest').parent().find(".actions").remove();
                    }
                }
            });

        @endif

        // $('#serviceRequest').on( 'draw.dt', function () {
        //     if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
        //             // $('.datatable, .ajaxTable').siblings('.actions').html('<a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a>');
        //             $('#serviceRequest').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
        //         }
        // } );
        function requestCustomerFilter(ele) {

            // get customers and product in company filter change from service request list page
            var companyId = $(ele).val();
            $.ajax({
                type:'GET',
                url:APP_URL+'/admin/getFilterCompanyDetails',
                data:{
                    'companyId':companyId
                },
                dataType: "json",
                success:function(data) {
                    if(companyId != "")
                    {
                        $(".filterCompanyDetails").show();
                    }
                    $(".filterCompanyDetails").find(".select2").select2();
                    $("#filter_customer").html(data.custOptions);
                    $("#filter_product").html(data.productOptions);

                    tableServiceRequest.draw();
                }
            });
            
        }

        function requestStatusFilter(ele) {
            tableServiceRequest.draw();
        }

        function requestTechnicianFilter(ele) {

            var serviceCenterId = $(ele).val();
            $.ajax({
                type:'GET',
                url:APP_URL+'/admin/getFilterTechnicians',
                data:{
                    'serviceCenterId':serviceCenterId
                },
                dataType: "json",
                success:function(data) {
                    if(serviceCenterId != ""){
                        $(".filterTechnicianDiv").show();

                        // display paid and due amount 
                        $(".service_center_balance").show();
                        $("#total_paid_amount").html('<i class="fa fa-rupee"></i>'+(parseFloat(data.paid_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                        $("#total_due_amount").html('<i class="fa fa-rupee"></i>'+(parseFloat(data.due_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') );

                        '<i class="fa fa-rupee"></i>'+(parseFloat(data.paid_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') 
                    }
                    else
                    {
                        $(".service_center_balance").hide();
                    }
                    $(".filterTechnicianDiv").find(".select2").select2();
                    $("#filter_technician").html(data.options);
                    
                    tableServiceRequest.draw();
                }
            });
        }
        function clearRequestFilter() {
            // clear service request list filter and set dropdown value to null
            $.ajax({
                type:'POST',
                url:APP_URL+'/admin/clearRequestFilterAjax',
                data:{
                    "_token": "{{csrf_token()}}"
                },
                dataType: "json",
                success:function(data) {
                    $("#filter_company").val('').select2().trigger("change");
                    $("#filter_customer").val('').select2();
                    $("#filter_product").val('').select2();

                    $("#filter_service_center").val('').select2().trigger("change");
                    $("#filter_technician").val('').select2();
                }
            });
        }

        $(document).on("change","#filter_customer, #filter_product, #filter_technician",function(evt){
            tableServiceRequest.draw();
        });
    </script>
@endsection