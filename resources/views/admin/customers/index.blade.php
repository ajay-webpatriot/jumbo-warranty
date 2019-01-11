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
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.customers.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.customers.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p>
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.customers.title')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($customers) > 0 ? 'datatable' : '' }} @can('customer_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('customer_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('quickadmin.customers.fields.firstname')</th>
                        <th>@lang('quickadmin.customers.fields.lastname')</th>
                        <th>@lang('quickadmin.customers.fields.phone')</th>
                        <th>@lang('quickadmin.customers.fields.company')</th>
                        <!-- <th>@lang('quickadmin.customers.fields.address-1')</th>
                        <th>@lang('quickadmin.customers.fields.address-2')</th>
                        <th>@lang('quickadmin.customers.fields.city')</th>
                        <th>@lang('quickadmin.customers.fields.state')</th>
                        <th>@lang('quickadmin.customers.fields.zipcode')</th>
                        <th>@lang('quickadmin.customers.fields.location')</th> -->
                        <th>@lang('quickadmin.customers.fields.status')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($customers) > 0)
                        @foreach ($customers as $customer)
                            <tr data-entry-id="{{ $customer->id }}">
                                @can('customer_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                <td field-key='firstname'>{{ $customer->firstname }}</td>
                                <td field-key='lastname'>{{ $customer->lastname }}</td>
                                <td field-key='phone'>{{ $customer->phone }}</td>
                                <td field-key='company'>{{ $customer->company->name or '' }}</td>
                                <!-- <td field-key='address_1'>{{ $customer->address_1 }}</td>
                                <td field-key='address_2'>{{ $customer->address_2 }}</td>
                                <td field-key='city'>{{ $customer->city }}</td>
                                <td field-key='state'>{{ $customer->state }}</td>
                                <td field-key='zipcode'>{{ $customer->zipcode }}</td>
                                <td field-key='location'>{{ $customer->location }}</td> -->
                                <td field-key='status'>{{ $customer->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    @can('customer_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.customers.restore', $customer->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('customer_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.customers.perma_del', $customer->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td>
                                    @can('customer_edit')
                                    <a href="{{ route('admin.customers.edit',[$customer->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('customer_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.customers.destroy', $customer->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="16">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('customer_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.customers.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection