@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.service-request-log.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($service_request_log) > 0 ? 'datatable' : '' }} ">
            	<thead>
                    <tr>
                    	
                        <th>@lang('quickadmin.service-request-log.fields.service-request-id')</th>
                        <th>@lang('quickadmin.service-request-log.fields.company')</th>
                        <th>@lang('quickadmin.service-request-log.fields.status')</th>
                        <th>&nbsp;</th>
                    </tr>
                 	<tbody>
                    	@if (count($service_request_log) > 0)
                        	@foreach ($service_request_log as $service_request_log)
                    	 		<tr data-entry-id="{{ $service_request_log->id }}">
	                    	 	 	<td field-key='service-request-id'>{{ $service_request_log->id or '' }}</td>
	                                <td field-key='company'>{{ $service_request_log->company->name or '' }}</td>
	                                <td field-key='status_made'>{{ $service_request_log->status or '' }}</td>
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
