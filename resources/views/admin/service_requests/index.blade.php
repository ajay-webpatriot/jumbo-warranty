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
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.service_requests.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.service_requests.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p>
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-request.title')
        </div>

        <div class="panel-body table-responsive">
            <table id="serviceRequest" class="display" width="100%">
				<thead>
                    <tr>
                        @can('service_request_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan
                        @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                        <th>@lang('quickadmin.service-request.fields.company')</th>
                        @endif
                        <th>@lang('quickadmin.service-request.fields.customer')</th>
                        <th>@lang('quickadmin.service-request.fields.service-type')</th>
                        @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                        <th>@lang('quickadmin.service-request.fields.service-center')</th>
                        @endif
                        <!-- <th>@lang('quickadmin.service-request.fields.technician')</th> -->
                        <th>@lang('quickadmin.service-request.fields.product')</th>
                        <th>@lang('quickadmin.service-request.fields.amount')</th>
                        <th>@lang('quickadmin.service-request.fields.status')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
			</table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('service_request_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}'; @endif
        @endcan

        $('#serviceRequest').DataTable({
		"processing": false,
		"serverSide": true,
	//   "initComplete":function(){onint();},
		"ajax":{
			url: APP_URL+"/admin/DataTableServiceRequestAjax",
			type:"GET",
		  data:function(dtp){
			// change the return value to what your server is expecting
			// here is the path to the search value in the textbox
			// var searchValue = dtp.search.value;
				console.log("=====Data Table=====");
				console.log(dtp);
			
			
				// return dtp;
			}
		},"columns": [
		{ "data": "company_name" },
		{ "data": "customer" },
		{ "data": "service_type" },
		{ "data": "service_center" },
		{ "data": "product" },
		{ "data": "amount" },
		{ "data": "request_status" }
		]
	});

	function onint(){
		// take off all events from the searchfield
		//$("#serviceRequest_wrapper input[type='search']").off();
		// Use return key to trigger search
		// $("#serviceRequest_wrapper input[type='search']").on("keydown", function(evt){
		// 	 if(evt.keyCode == 13){
		// 	   $("#serviceRequest").DataTable().search($("input[type='search']").val()).draw();
		// 	 }
		// });
		// $("#btnexample_search").button().on("click", function(){
		// 	  $("#example").DataTable().search($("input[type='search']").val()).draw();
   
		// });
	  }

    </script>
@endsection