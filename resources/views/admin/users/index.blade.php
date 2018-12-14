@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @if(auth()->user()->role_id ==  $_ENV['COMPANY_ADMIN_ROLE_ID'])
    <h3 class="page-title">@lang('quickadmin.users.companyUserTitle')</h3>
    @elseif(auth()->user()->role_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
    <h3 class="page-title">@lang('quickadmin.users.technicianTitle')</h3>
    @else
    <h3 class="page-title">@lang('quickadmin.users.title')</h3>
    @endif
    @can('user_create')
    <p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('quickadmin.users.fields.role')</th>
                        <th>@lang('quickadmin.users.fields.company')</th>
                        <th>@lang('quickadmin.users.fields.service-center')</th>
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.phone')</th>
                        <th>@lang('quickadmin.users.fields.address-1')</th>
                        <th>@lang('quickadmin.users.fields.address-2')</th>
                        <th>@lang('quickadmin.users.fields.city')</th>
                        <th>@lang('quickadmin.users.fields.state')</th>
                        <th>@lang('quickadmin.users.fields.zipcode')</th>
                        <th>@lang('quickadmin.users.fields.location')</th>
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.users.fields.status')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('user_delete')
                                    <td></td>
                                @endcan

                                <td field-key='role'>{{ $user->role->title or '' }}</td>
                                <td field-key='company'>{{ $user->company->name or '' }}</td>
                                <td field-key='service_center'>{{ $user->service_center->name or '' }}</td>
                                <td field-key='name'>{{ $user->name }}</td>
                                <td field-key='phone'>{{ $user->phone }}</td>
                                <td field-key='address_1'>{{ $user->address_1 }}</td>
                                <td field-key='address_2'>{{ $user->address_2 }}</td>
                                <td field-key='city'>{{ $user->city }}</td>
                                <td field-key='state'>{{ $user->state }}</td>
                                <td field-key='zipcode'>{{ $user->zipcode }}</td>
                                <td field-key='location'>{{ $user->location_address }}</td>
                                <td field-key='email'>{{ $user->email }}</td>
                                <td field-key='status'>{{ $user->status }}</td>
                                                                <td>
                                    @can('user_view')
                                    <a href="{{ route('admin.users.show',[$user->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('user_edit')
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('user_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.users.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="20">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('user_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}';
        @endcan

    </script>
@endsection