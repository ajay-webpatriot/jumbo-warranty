@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center-admin.title')</h3> -->
    @can('user_create')
    <p class="text-right">
        <a href="{{ route('admin.service_center_admins.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    <style type="text/css">
    /* table th td align ment verticle center*/
    td,th{
      vertical-align: middle!important;
    }
    </style>

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
                            {!! Form::label('service_center_id', trans('quickadmin.users.fields.service-center').'', ['class' => 'control-label']) !!}

                            {!! Form::select('filter_service_center',$service_centers, null, ['class' => 'form-control select2', 'id' => 'filter_service_center','style' => 'width:100%']) !!}
                        </div>
                    </div> 
                </div>
            </div>
        </div>                  
    @endif    

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-center-admin.title')
        </div>

        <div class="panel-body table-responsive">
             <table id="service_center_admin" class="display table table-bordered table-striped dt-select dataTable no-footer datatable">
                <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        <th>@lang('quickadmin.qa_sr_no')</th>
                        <th>@lang('quickadmin.users.fields.service-center')</th>
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.phone')</th>
                        <!-- <th>@lang('quickadmin.users.fields.address-1')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.address-2')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.city')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.state')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.zipcode')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.location')</th> -->
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.users.fields.status')</th>
                        <th>@lang('quickadmin.qa_action')</th>
                    </tr>
                </thead>
                
                <tbody>
                    {{-- @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td></td>
                                @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                                || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))
                                    <td field-key='service_center'>{{ $user->service_center->name or '' }}</td>
                                @endif
                                <td field-key='name'>{{ $user->name }}</td>
                                <td field-key='phone'>{{ $user->phone }}</td>
                                <!-- <td field-key='address_1'>{{ $user->address_1 }}</td> -->
                                <!-- <td field-key='address_2'>{{ $user->address_2 }}</td> -->
                                <!-- <td field-key='city'>{{ $user->city }}</td> -->
                                <!-- <td field-key='state'>{{ $user->state }}</td> -->
                                <!-- <td field-key='zipcode'>{{ $user->zipcode }}</td> -->
                                <!-- <td field-key='location'>{{ $user->location_address }}</td> -->
                                <td field-key='email'>{{ $user->email }}</td>
                                <td field-key='status'>{{ $user->status }}</td>
                                                                <td>
                                    @can('user_edit')
                                    <a href="{{ route('admin.service_center_admins.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_center_admins.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td align="center" colspan="20">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif --}}
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
            window.route_mass_crud_entries_destroy = '{{ route('admin.service_center_admins.mass_destroy') }}';


            var tableServiceCenterAdmin = $('#service_center_admin').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
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
                // "language": {
                //     "emptyTable": "<font color='red'>Please select project and supplier name.</font>"
                //   },
                "ajax":{
                        "url": APP_URL+"/admin/DataTableServiceCenterAdminAjax",
                        "type":"POST",
                        "dataType": "json",
                        // "data":{"_token": "{{csrf_token()}}"}
                        "data":function(data) {
                            data.service_center = $('#filter_service_center').val();
                            data._token = "{{csrf_token()}}";

                        },
                    },
                "columns": [
                    
                    { "data": "checkbox" },
                    { "data": "sr_no" },
                    { "data": "service_center" },
                    { "data": "admin_name" },
                    { "data": "phone" },
                    { "data": "email" },
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
                    "targets":   7,
                    "className": 'text-center',
                 },{
                    "className": 'text-center',
                    "targets":   6
                },{
                    "className": 'text-center',
                    "targets":   4
                }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('data-entry-id', aData.sr_no);
                },
                "drawCallback": function( settings ) {
                    var api = this.api();
                    // Output the data for the visible rows to the browser's console
                    if(api.rows( {page:'current'} ).data().length > 0)
                    {
                        if($('#service_center_admin').parent().find(".actions").length == 0 )
                        {
                            // set bulk delete button after table draw
                            if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                $('#service_center_admin').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                            }
                        }
                    }
                    else
                    {
                        $('#service_center_admin').parent().find(".actions").remove();
                    }
                }   
            });
            $(document).on("change",'#filter_service_center',function(){
                tableServiceCenterAdmin.draw();
            });
    </script>
@endsection