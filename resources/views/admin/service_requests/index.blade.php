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
        <div class="panel-heading headerTitle">
            <a data-toggle="collapse" href="#collapseAdvanceFilter">Advance Filters</a>
        </div>
        <div id="collapseAdvanceFilter" class="panel-collapse in" role="tabpanel">
            <div class="panel-body">
                <div class="row">
                    <!-- Company & Customer -->
                    @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'', ['class' => 'control-label']) !!}

                                @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                                    {!! Form::text('company_name', $companyName[0], ['class' => 'form-control', 'placeholder' => 'Company Name','disabled' => '']) !!}
                                    {!! Form::hidden('filter_company', auth()->user()->company_id, ['class' => 'form-control', 'id' => 'filter_company']) !!}
                                @else
                                    {!! Form::select('filter_company', $companies, ($request->session()->has('filter_company'))? $request->session()->get('filter_company'):'', ['class' => 'form-control select2','onchange' => 'requestCustomerFilter(this)', 'id' => 'filter_company']) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                    @if((auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID')) || $request->session()->has('filter_company'))
                    <div class="filterCompanyDetails">
                    @else
                    <div class="filterCompanyDetails" style="display: none;">
                    @endif    
                        <div class="col-md-4">
                            <div class="row"> 
                                <div class="col-xs-12">
                                    {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'', ['class' => 'control-label']) !!}
                                    {!! Form::select('filter_customer', $customers, ($request->session()->has('filter_customer'))? $request->session()->get('filter_customer'):'', ['class' => 'form-control select2', 'id' => 'filter_customer']) !!}
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
                            <div class="col-xs-12">
                                {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'', ['class' => 'control-label']) !!}
                                {!! Form::select('filter_product', $products,($request->session()->has('filter_product'))? $request->session()->get('filter_product'):'', ['class' => 'form-control select2', 'id' => 'filter_product']) !!}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-6">
                        <div class="row"> 
                            <div class="col-xs-12">
                                {!! Form::label('customer_id', trans('quickadmin.service-request.fields.customer').'', ['class' => 'control-label']) !!}
                                {!! Form::select('filter_customer', $customers, ($request->session()->has('filter_customer'))? $request->session()->get('filter_customer'):'', ['class' => 'form-control select2', 'id' => 'filter_customer']) !!}
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Form::label('product_id', trans('quickadmin.service-request.fields.product').'', ['class' => 'control-label']) !!}
                                {!! Form::select('filter_product', $products, ($request->session()->has('filter_product'))? $request->session()->get('filter_product'):'', ['class' => 'form-control select2', 'id' => 'filter_product']) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>

                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                <div class="row form-group">
                    <!-- service center and technician filter -->
                    <div class="col-md-6">
                        <div class="row"> 
                            <div class="col-xs-12">
                                {!! Form::label('service_center_id', trans('quickadmin.service-request.fields.service-center').'', ['class' => 'control-label']) !!}
                                @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                                    {!! Form::text('service_center_name', $serviceCenterName[0], ['class' => 'form-control', 'placeholder' => 'Service Center Name','disabled' => '']) !!}
                                    {!! Form::hidden('servicew_center_id', auth()->user()->service_center_id, ['class' => 'form-control', 'id' => 'filter_service_center']) !!}
                                @else
                                    {!! Form::select('filter_service_center', $service_centers, ($request->session()->has('filter_service_center'))? $request->session()->get('filter_service_center'):'', ['class' => 'form-control select2','onchange' => 'requestTechnicianFilter(this)', 'id' => 'filter_service_center']) !!}
                                @endif
                            </div>
                        </div> 
                    </div>
                    @if((auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID')) || $request->session()->has('filter_service_center'))
                        <div class="col-md-6 filterTechnicianDiv">
                    @else
                        <div class="col-md-6 filterTechnicianDiv" style="display: none;">
                    @endif
                        <div class="row"> 
                            <div class="col-xs-12">
                                {!! Form::label('technician_id', trans('quickadmin.service-request.fields.technician').'', ['class' => 'control-label']) !!}

                                @if(auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                                    {!! Form::text('technician_name', auth()->user()->name, ['class' => 'form-control', 'placeholder' => 'Service Center Name','disabled' => '']) !!}
                                    {!! Form::hidden('filter_technician', auth()->user()->id, ['class' => 'form-control', 'id' => 'filter_technician']) !!}
                                @else
                                    {!! Form::select('filter_technician', $technicians, ($request->session()->has('filter_technician'))? $request->session()->get('filter_technician'):'', ['class' => 'form-control select2', 'id' => 'filter_technician']) !!}
                                @endif
                            </div>
                        </div> 
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-xs-12">
                        <p class="text-right">
                            <a href="{{url('/admin/clearRequestFilterAjax')}}"  id="clearRequestFilter" class="btn btn-danger">@lang('quickadmin.service-request.clear-filter')</a>
                            
                        </p>
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
                            <th>Sr No.</th>
                            {{--@can('service_request_delete')
                                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" class="dt-body-center" id="select-all" /></th>@endif
                            @endcan--}}
                            
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <th>@lang('quickadmin.service-request.fields.service-center')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.technician')</th> -->
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <th>Action</th>
                            
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
                            <th>Sr No.</th>
                            <th>@lang('quickadmin.service-request.fields.company')</th>
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <!-- <th>@lang('quickadmin.service-request.fields.technician')</th> -->
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <th>Action</th>
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
                            <th>Sr No.</th>
                            <th>@lang('quickadmin.service-request.fields.company')</th>
                            <th>@lang('quickadmin.service-request.fields.customer')</th>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <th>@lang('quickadmin.service-request.fields.service-center')</th>
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <th>Action</th>
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
    <script>
        @can('service_request_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}'; @endif
        @endcan
        window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}';
        
        @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
            // service center admin and technician
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
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
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
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": [
                    { "data": "sr_no" },
                    // { "data": "checkbox" },
                    { "data": "customer","name": "customer"},
                    { "data": "service_type","name": "service_type" },
                    { "data": "service_center","name": "service_center" },
                    { "data": "product","name": "product" },
                    { "data": "amount","name": "amount" },
                    { "data": "request_status","name": "request_status" },
                    { "data": "action" }
                ],
                "columnDefs": [{
                    "orderable": false,
                    "className": 'dt-body-center select-checkbox',
                    "targets":   0,
                    "visible": false,
                    "searchable": false
                },{
                    "orderable": true,
                    "targets":   [1,2,3,4,5,6]
                },
                // {
                //     "orderable": true,
                //     "targets":   1
                // },{
                //     "orderable": true,
                //     "targets":   2
                // },{
                //     "orderable": true,
                //     "targets":   3
                // },{
                //     "orderable": true,
                //     "targets":   4
                // },{
                //     "orderable": true,
                //     "targets":   5
                // },
                {
                    "orderable": false,
                    "targets":   7
                }]
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
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
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
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": [
                    
                    { "data": "checkbox" },
                    { "data": "sr_no" },
                    { "data": "company_name" },
                    { "data": "customer" },
                    { "data": "service_type" },
                    { "data": "product" },
                    { "data": "amount" },
                    { "data": "request_status" },
                    { "data": "action" }
                ],
                "columnDefs": [{
                    "orderable": false,
                    "className": 'select-checkbox',
                    "targets":   0,
                    "searchable": false
                },{
                    "orderable": false,
                    "className": 'dt-body-center',
                    "targets":   1,
                    "visible": false,
                    "searchable": false
                },{
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
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: window.printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
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
                    { "data": "request_status" },
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
                    "orderable": false,
                    "className": 'dt-body-center',
                    "targets":   1,
                    "visible": false,
                    "searchable": false
                },{
                    "orderable": false,
                    "targets":   9
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
                url:APP_URL+'/admin/getCompanyDetails',
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

        function requestTechnicianFilter(ele) {

            var serviceCenterId = $(ele).val();
            $.ajax({
                type:'GET',
                url:APP_URL+'/admin/getTechnicians',
                data:{
                    'serviceCenterId':serviceCenterId
                },
                dataType: "json",
                success:function(data) {
                    if(serviceCenterId != ""){
                        $(".filterTechnicianDiv").show();
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