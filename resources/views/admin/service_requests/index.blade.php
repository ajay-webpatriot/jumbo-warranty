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
            $('#technician').DataTable({
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
                        "data":{"_token": "{{csrf_token()}}"}
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
            $('#company').DataTable({
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
                        "data":{"_token": "{{csrf_token()}}"}
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
            $('#serviceRequest').DataTable({
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
                        "data":{"_token": "{{csrf_token()}}"}
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
    </script>
@endsection