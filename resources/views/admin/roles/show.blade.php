@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.roles.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.roles.fields.title')</th>
                            <td field-key='title'>{{ $role->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.roles.fields.status')</th>
                            <td field-key='status'>{{ $role->status }}</td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="active"><a href="#users" aria-controls="users" role="tab" data-toggle="tab">Users</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
<div role="tabpanel" class="tab-pane active" id="users">
<table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
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

            <p>&nbsp;</p>

            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop


