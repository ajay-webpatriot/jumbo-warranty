@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.customers.title')</h3> -->
   
    @can('customer_create')
    <p class="text-right">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    @can('customer_delete')
    <!-- <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.customers.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.customers.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p> -->
    @endcan

    @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
        <div class="panel panel-default">
            <div class="panel-heading headerTitle" href="#collapseAdvanceFilter" data-toggle="collapse">
                Filter
                <span class="btn-box-tool glyphicon glyphicon-plus pull-right"></span>
            </div>
            <div id="collapseAdvanceFilter" class="panel-collapse collapse in" role="tabpanel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::label('company_id', trans('quickadmin.service-request.fields.company').'', ['class' => 'control-label']) !!}

                            {{-- !! Form::select('filter_company',[null=>'All'], null, ['class' => 'form-control select2']) !! --}}

                            {!! Form::select('filter_company',$companies, null, ['class' => 'form-control select2', 'id' => 'filter_company']) !!}
                        </div>
                    </div> 
                </div>
            </div>
        </div>                  
    @endif
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.customers.title')
        </div>

        <div class="panel-body table-responsive">
             @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                <table id="customer" class="display table table-bordered table-striped dt-select dataTable no-footer datatable">
                    <thead>
                        <tr>
                            
                            <th style="text-align:center;"><input type="checkbox" class="dt-body-center select-checkbox" id="select-all" /></th>
                            <th>@lang('quickadmin.qa_sr_no')</th>
                            <th>@lang('quickadmin.customers.fields.firstnameandlastname')</th>
                            <th>@lang('quickadmin.customers.fields.phone')</th>
                            <th>@lang('quickadmin.customers.fields.company')</th>
                            <th>@lang('quickadmin.customers.fields.status')</th>
                            <th>@lang('quickadmin.qa_action')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            @else
            <table id="customer" class="display table table-bordered table-striped dt-select dataTable no-footer datatable">
                <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" class="dt-body-center select-checkbox" id="select-all" /></th>
                        <th>@lang('quickadmin.qa_sr_no')</th>
                        <!-- <th>@lang('quickadmin.customers.fields.firstname')</th>
                        <th>@lang('quickadmin.customers.fields.lastname')</th> -->
                        <th>@lang('quickadmin.customers.fields.firstnameandlastname')</th>
                        <th>@lang('quickadmin.customers.fields.phone')</th>
                        <!-- <th>@lang('quickadmin.customers.fields.address-1')</th>
                        <th>@lang('quickadmin.customers.fields.address-2')</th>
                        <th>@lang('quickadmin.customers.fields.city')</th>
                        <th>@lang('quickadmin.customers.fields.state')</th>
                        <th>@lang('quickadmin.customers.fields.zipcode')</th>
                        <th>@lang('quickadmin.customers.fields.location')</th> -->
                        <th>@lang('quickadmin.customers.fields.status')</th>
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
    <script>
        @can('customer_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.customers.mass_destroy') }}'; @endif
        @endcan
        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                
                var tableCustomer = $('#customer').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [[ 1, "desc" ]],
                    retrieve: true,
                    dom: 'lBfrtip<"actions">',
                    columnDefs: [],
                    "iDisplayLength": 10,
                    "aaSorting": [],
                    buttons: [
                        // {
                        //     extend: 'pdf',
                        //     text: window.pdfButtonTrans,
                        //     exportOptions: {
                        //         columns: ':visible'
                        //     }
                        // },
                        // {
                        //     extend: 'print',
                        //     text: window.printButtonTrans,
                        //     exportOptions: {
                        //         columns: ':visible'
                        //     }
                        // }
                    ],
                    "ajax":{
                            "url": APP_URL+"/admin/DataTableCustomerAjax",
                            "type":"POST",
                            "dataType": "json",
                            // "data":{"_token": "{{csrf_token()}}"}
                            "data":function(data) {
                                data.company = $('#filter_company').val();
                                data._token = "{{csrf_token()}}";

                            },
                        },
                    "columns": [
                        
                        { "data": "checkbox" },
                        { "data": "sr_no" },
                        { "data": "customer_name" },
                        { "data": "phone" },
                        { "data": "company" },
                        { "data": "status" },
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
                        "targets":   6
                    }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                        $(nRow).attr('data-entry-id', aData.sr_no);
                    },
                    "drawCallback": function( settings ) {
                        var api = this.api();
                        // Output the data for the visible rows to the browser's console
                        
                        if(api.rows( {page:'current'} ).data().length > 0)
                        {
                            if($('#customer').parent().find(".actions").length == 0 )
                            {
                                // set bulk delete button after table draw
                                if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                    $('#customer').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                                }
                            }
                        }
                        else
                        {
                            $('#customer').parent().find(".actions").remove();
                        }
                    }   
                });
            @else
                var tableCustomer = $('#customer').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [[ 1, "desc" ]],
                    retrieve: true,
                    dom: 'lBfrtip<"actions">',
                    columnDefs: [],
                    "iDisplayLength": 10,
                    "aaSorting": [],
                    buttons: [
                        // {
                        //     extend: 'pdf',
                        //     text: window.pdfButtonTrans,
                        //     exportOptions: {
                        //         columns: ':visible'
                        //     }
                        // },
                        // {
                        //     extend: 'print',
                        //     text: window.printButtonTrans,
                        //     exportOptions: {
                        //         columns: ':visible'
                        //     }
                        // }
                    ],
                    "ajax":{
                            "url": APP_URL+"/admin/DataTableCustomerAjax",
                            "type":"POST",
                            "dataType": "json",
                            // "data":{"_token": "{{csrf_token()}}"}
                            "data":function(data) {
                                data.company = $('#filter_company').val();
                                data._token = "{{csrf_token()}}";

                            },
                        },
                    "columns": [
                        
                        { "data": "checkbox" },
                        { "data": "sr_no" },
                        { "data": "customer_name" },
                        { "data": "phone" },
                        { "data": "status" },
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
                        "targets":   5
                    }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                        $(nRow).attr('data-entry-id', aData.sr_no);
                    },
                    "drawCallback": function( settings ) {
                        var api = this.api();
                        // Output the data for the visible rows to the browser's console
                        
                        if(api.rows( {page:'current'} ).data().length > 0)
                        {
                            if($('#customer').parent().find(".actions").length == 0 )
                            {
                                // set bulk delete button after table draw
                                if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                    $('#customer').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                                }
                            }
                        }
                        else
                        {
                            $('#customer').parent().find(".actions").remove();
                        }
                    }   
                });
            @endif

            $(document).on("change",'#filter_company',function(){
                tableCustomer.draw();
            });
    </script>
@endsection