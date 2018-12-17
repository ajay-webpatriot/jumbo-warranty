@extends('layouts.app')

@section('content')

    <h3 class="page-title">@lang('quickadmin.service-request-log-view.title')</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($service_request_log) > 0 ? 'datatable' : '' }} ">
                <thead>
                    <tr>
                        <th>Sr no.</th>
                        <th>@lang('quickadmin.service-request-log-view.fields.action')</th>
                        <th>@lang('quickadmin.service-request-log-view.fields.action-taken-by')</th>
                        <th>@lang('quickadmin.service-request-log-view.fields.date-time')</th>
                        
                    </tr>
                    <tbody>
                        @if (count($service_request_log) > 0)
                            @foreach ($service_request_log as $service_request_log)
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
                </thead>
            </table>
            <p>&nbsp;</p>

            <a href="{{ route('admin.service_request_logs.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop

