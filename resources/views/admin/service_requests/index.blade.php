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
            <table class="table table-bordered table-striped {{ count($service_requests) > 0 ? 'datatable' : '' }} @can('service_request_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
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
                
                <tbody>
                    @if (count($service_requests) > 0)
                        @foreach ($service_requests as $service_request)
                            <tr data-entry-id="{{ $service_request->id }}">
                                @can('service_request_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                                <td field-key='company'>{{ $service_request->company->name or '' }}</td>
                                @endif
                                <td field-key='customer'>{{ $service_request->customer->firstname or '' }}</td>
                                <td field-key='service_type'>{{ $service_request->service_type }}</td>

                                @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                                <td field-key='service_center'>{{ $service_request->service_center->name or '' }}
                                    @if($service_request->technician_id != "")
                                        <br/>
                                        (
                                            {{ $service_request->technician->name or '' }} 
                                        )
                                    @endif
                                </td>
                                @endif
                                <!-- <td field-key='technician'>{{ $service_request->technician->name or '' }}</td> -->
                                <td field-key='product'>{{ $service_request->product->name or '' }}</td>
                                <td field-key='amount'>{{ number_format($service_request->amount,2) }}</td>
                                <td field-key='status'>{{ $service_request->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    @can('service_request_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.restore', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('service_request_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.perma_del', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td>
                                    @can('service_request_view')
                                    <a href="{{ route('admin.service_requests.show',[$service_request->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('service_request_edit')
                                    <a href="{{ route('admin.service_requests.edit',[$service_request->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('service_request_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.destroy', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="32">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('service_request_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_requests.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection