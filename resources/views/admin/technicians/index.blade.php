@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.users.technicianTitle')</h3> -->
    @can('user_create')
    <p class="text-right">
        <a href="{{ route('admin.technicians.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.users.technicianTitle')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} dt-select">
                <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))
                            <th>@lang('quickadmin.users.fields.service-center')</th>
                        @endif
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.phone')</th>
                        <!-- <th>@lang('quickadmin.users.fields.address-1')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.address-2')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.city')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.state')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.zipcode')</th> -->
                        <!-- <th>@lang('quickadmin.users.fields.location')</th> -->
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.users.fields.status')</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td></td>
                                @if(auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                                || auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID'))
                                    <td field-key='service_center'>{{ $user->service_center->name or '' }}</td>
                                @endif
                                <td field-key='name'>{{ $user->name }}</td>
                                <td field-key='phone'>{{ $user->phone }}</td>
                                <!-- <td field-key='address_1'>{{ $user->address_1 }}</td> -->
                                <!-- <td field-key='address_2'>{{ $user->address_2 }}</td> -->
                                <!-- <td field-key='city'>{{ $user->city }}</td> -->
                                <!-- <td field-key='state'>{{ $user->state }}</td> -->
                                <!-- <td field-key='zipcode'>{{ $user->zipcode }}</td> -->
                                <!-- <td field-key='location'>{{ $user->location_address }}</td> -->
                                <td field-key='email'>{{ $user->email }}</td>
                                <td field-key='status'>{{ $user->status }}</td>
                                                                <td>
                                    @can('user_edit')
                                    <a href="{{ route('admin.technicians.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.technicians.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
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
            window.route_mass_crud_entries_destroy = '{{ route('admin.technicians.mass_destroy') }}';

    </script>
@endsection