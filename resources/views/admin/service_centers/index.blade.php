@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center.title')</h3> -->
    @can('service_center_create')
    <p class="text-right">
        <a href="{{ route('admin.service_centers.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    @can('service_center_delete')
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.service_centers.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.service_centers.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p>
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-center.title')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($service_centers) > 0 ? 'datatable' : '' }} @can('service_center_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('service_center_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('quickadmin.service-center.fields.name')</th>
                        <th>@lang('quickadmin.service-center.fields.address-1')</th>
                        <th>@lang('quickadmin.service-center.fields.location')</th>
                        <th>@lang('quickadmin.service-center.fields.commission')</th>
                        <th>@lang('quickadmin.service-center.fields.address-2')</th>
                        <th>@lang('quickadmin.service-center.fields.city')</th>
                        <th>@lang('quickadmin.service-center.fields.state')</th>
                        <th>@lang('quickadmin.service-center.fields.zipcode')</th>
                        <th>@lang('quickadmin.service-center.fields.status')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($service_centers) > 0)
                        @foreach ($service_centers as $service_center)
                            <tr data-entry-id="{{ $service_center->id }}">
                                @can('service_center_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                <td field-key='name'>{{ $service_center->name }}</td>
                                <td field-key='address_1'>{{ $service_center->address_1 }}</td>
                                <td field-key='location'>{{ $service_center->location_address }}</td>
                                <td field-key='commission'>{{ $service_center->commission }}</td>
                                <td field-key='address_2'>{{ $service_center->address_2 }}</td>
                                <td field-key='city'>{{ $service_center->city }}</td>
                                <td field-key='state'>{{ $service_center->state }}</td>
                                <td field-key='zipcode'>{{ $service_center->zipcode }}</td>
                                <td field-key='status'>{{ $service_center->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    @can('service_center_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_centers.restore', $service_center->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('service_center_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_centers.perma_del', $service_center->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td>
                                    @can('service_center_view')
                                    <!-- <a href="{{ route('admin.service_centers.show',[$service_center->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a> -->
                                    @endcan
                                    @can('service_center_edit')
                                    <a href="{{ route('admin.service_centers.edit',[$service_center->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('service_center_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_centers.destroy', $service_center->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('service_center_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.service_centers.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection