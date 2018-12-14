@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.service-request-log.title')</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($service_request_log) > 0 ? 'datatable' : '' }} @can('service_request_log_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
            	<thead>
                    <tr>
                    	@can('service_request_log_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.service-request.fields.status')</th>
                        <th>Created</th>
                        <th>Updated</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                 	<tbody>
                    	@if (count($service_request_log) > 0)
                        	@foreach ($service_request_log as $service_request_log)
                    	 		<tr data-entry-id="{{ $service_request_log->id }}">
	                    	 	 	@can('service_request_log_delete')
	                                    @if ( request('show_deleted') != 1 )<td></td>@endif
	                                @endcan
	                                <td field-key='name'>{{ $service_request_log->name or '' }}</td>
	                                <td field-key='email'>{{ $service_request_log->email or '' }}</td>
	                                <td field-key='status_made'>{{ $service_request_log->status_made or '' }}</td>
	                                <td field-key='created_at'>{{ $service_request_log->created_at or '' }}</td>
	                                <td field-key='updated_at'>{{ $service_request_log->updated_at or '' }}</td>
	                                <td>
	                                	@can('service_request_view')
	                                    <a href="{{ route('admin.service_request_logs.show',[$service_request_log->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
	                                    @endcan
                                	</td>
								</tr>
                    	  	@endforeach
	                    @else
	                        <tr>
	                            <td colspan="32">@lang('quickadmin.qa_no_entries_in_table')</td>
	                        </tr>
	                    @endif
                    </tbody>
                </thead>
			</table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('service_request_log_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests_logs.mass_destroy') }}'; @endif
        @endcan
	</script>
@endsection