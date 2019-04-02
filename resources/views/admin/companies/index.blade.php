@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.company.title')</h3> -->
    @can('company_create')
    <p class="text-right">
        <a href="{{ route('admin.companies.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    @can('company_delete')
    <!-- <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.companies.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.companies.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p> -->
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.company.title')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($companies) > 0 ? 'datatable' : '' }} @can('company_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('company_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('quickadmin.company.fields.name')</th>
                        <th>@lang('quickadmin.company.fields.credit')</th>
                        <th>@lang('quickadmin.company.fields.installation-charge')</th>
                        <!-- <th>@lang('quickadmin.company.fields.address-1')</th>
                        <th>@lang('quickadmin.company.fields.address-2')</th> -->
                        <th>@lang('quickadmin.company.fields.city')</th>
                        <th>@lang('quickadmin.company.fields.state')</th>
                        <!-- <th>@lang('quickadmin.company.fields.zipcode')</th> -->
                        <th>@lang('quickadmin.company.fields.status')</th>
                        {{-- @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif --}}
                        <th>@lang('quickadmin.qa_action')</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($companies) > 0)
                        @foreach ($companies as $company)
                            <tr data-entry-id="{{ $company->id }}">
                                @can('company_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                <td field-key='name'>{{ $company->name }}</td>
                                <td class="text-right" field-key='credit'>{{ number_format($company->credit,2) }}</td>
                                <td class="text-right" field-key='installation_charge'>{{ number_format($company->installation_charge,2) }}</td>
                                <!-- <td field-key='address_1'>{{ $company->address_1 }}</td>
                                <td field-key='address_2'>{{ $company->address_2 }}</td> -->
                                <td field-key='city'>{{ $company->city }}</td>
                                <td field-key='state'>{{ $company->state }}</td>
                                <!-- <td field-key='zipcode'>{{ $company->zipcode }}</td> -->
                                <td field-key='status'>{{ $company->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    @can('company_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.companies.restore', $company->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('company_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.companies.perma_del', $company->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td>
                                    @can('company_edit')
                                    <a href="{{ route('admin.companies.edit',[$company->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('company_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.companies.destroy', $company->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td align="center" colspan="15">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('company_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.companies.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection