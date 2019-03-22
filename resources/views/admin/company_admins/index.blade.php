@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.company-admins.title')</h3> -->
    @can('user_create')
    <p class="text-right">
        <a href="{{ route('admin.company_admins.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
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

                            {!! Form::select('filter_company',$companies, null, ['class' => 'form-control select2','onchange' => 'requestCustomerFilter(this)', 'id' => 'filter_company']) !!}
                        </div>
                    </div> 
                </div>
            </div>
        </div>                  
    @endif
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.company-admins.title')
        </div>

        <div class="panel-body table-responsive">
            <table id="company_admin" class="display table table-bordered table-striped dt-select dataTable no-footer datatable">
                <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        <th>@lang('quickadmin.qa_sr_no')</th>
                       <!--  @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')) -->
                            <th>@lang('quickadmin.users.fields.company')</th>
                        <!-- @endif -->
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
                                <!-- @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                                || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')) -->
                                    <td field-key='company'>{{ $user->company->name or '' }}</td>
                                <!-- @endif -->
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
                                    <a href="{{ route('admin.company_admins.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.company_admins.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="20">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif --}}
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
            window.route_mass_crud_entries_destroy = '{{ route('admin.company_admins.mass_destroy') }}';


        var tableCompanyAdmin = $('#company_admin').DataTable({
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
                        "url": APP_URL+"/admin/DataTableCompanyAdminAjax",
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
                    { "data": "company_name" },
                    { "data": "company_admin_name" },
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
                    "targets":   7
                }],"fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('data-entry-id', aData.sr_no);
                },
                "drawCallback": function( settings ) {
                    var api = this.api();
                    // Output the data for the visible rows to the browser's console
                    
                    if(api.rows( {page:'current'} ).data().length > 0)
                    {
                        if($('#company_admin').parent().find(".actions").length == 0 )
                        {
                            // set bulk delete button after table draw
                            if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
                                $('#company_admin').parent().append('<div class="actions"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a></div>');
                            }
                        }
                    }
                    else
                    {
                        $('#company_admin').parent().find(".actions").remove();
                    }
                }   
            });


        function requestCustomerFilter(ele) {
            tableCompanyAdmin.draw();
            // $.ajax({
            //     type:'GET',
            //     url:APP_URL+'/admin/getFilterCompanyDetails',
            //     data:{
            //         'companyId':companyId
            //     },
            //     dataType: "json",
            //     success:function(data) {
            //         if(companyId != "")
            //         {
            //             $(".filterCompanyDetails").show();
            //         }
            //         $(".filterCompanyDetails").find(".select2").select2();
            //         $("#filter_customer").html(data.custOptions);
            //         $("#filter_product").html(data.productOptions);

            //         tableServiceRequest.draw();
            //     }
            // });

        }



    </script>
@endsection