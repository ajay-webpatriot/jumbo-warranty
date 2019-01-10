@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.users.title')</h3> -->
    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.users.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <!-- <tr>
                            <th>@lang('quickadmin.users.fields.role')</th>
                            <td field-key='role'>{{ $user->role->title or '' }}</td>
                        </tr> 
                        <tr>
                            <th>@lang('quickadmin.users.fields.company')</th>
                            <td field-key='company'>{{ $user->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.service-center')</th>
                            <td field-key='service_center'>{{ $user->service_center->name or '' }}</td>
                        </tr>-->
                        <tr>
                            <th>@lang('quickadmin.users.fields.name')</th>
                            <td field-key='name'>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.phone')</th>
                            <td field-key='phone'>{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.email')</th>
                            <td field-key='email'>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.users.fields.status')</th>
                            <td field-key='status'>{{ $user->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>


            <p>&nbsp;</p>

            <a href="{{ route('admin.users.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop

