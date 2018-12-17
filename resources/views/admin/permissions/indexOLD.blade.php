@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    <h3 class="page-title">@lang('quickadmin.permissions.title')</h3>
    
    {!! Form::open(['method' => 'POST', 'route' => ['admin.permissions.store']]) !!}
    @foreach ($modules as $module)
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="{{ isset($module->name) ? str_slug($module->name) :  'permissionHeading' }}">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#dd-{{ isset($module->name) ? str_slug($module->name) :  'permissionHeading' }}" aria-expanded="{{ $closed or 'true' }}" aria-controls="dd-{{ isset($module->name) ? str_slug($module->name) :  'permissionHeading' }}">
                    {{ $module->name or 'Permissions' }} {!! isset($user) ? '<span class="text-danger">(' . $user->getDirectPermissions()->count() . ')</span>' : '' !!}
                </a>
            </h4>
        </div>
        <div id="dd-{{ isset($module->name) ? str_slug($module->name) :  'permissionHeading' }}" class="panel-collapse collapse {{ $closed or 'in' }}" role="tabpanel" aria-labelledby="dd-{{ isset($module->name) ? str_slug($module->name) :  'permissionHeading' }}">
            <div class="panel-body">
                <div class="row">
                    <table class="table table-bordered table-striped" id="tblPermission">
                        <thead>
                            <tr>
                               
                                <th>Role</th>
                                @foreach($permissions as $perm)
                                    <?php
                                        $per_found = null;

                                        // if( isset($role) ) {
                                        //     $per_found = $role->hasPermissionTo($perm->name);
                                        // }

                                        // if( isset($user)) {
                                        //     $per_found = $user->hasDirectPermission($perm->name);
                                        // }
                                    ?>

                                    <th>
                                            <label class="{{ str_contains($perm->name, 'Delete') ? 'text-danger' : '' }}">
                                                {!! Form::checkbox("permissions_chk[]", $perm->name, $per_found, ['class' => 'allRoleCheck']) !!} {{ $perm->name }}
                                            </label>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                    @foreach($roles as $role)
                        <?php
                            $per_found = null;
                            // if( isset($role) ) {
                            //     $per_found = $role->hasPermissionTo($perm->name);
                            // }

                            // if( isset($user)) {
                            //     $per_found = $user->hasDirectPermission($perm->name);
                            // }
                        ?>
                        <tr>
                        <td>{{ $role->title }}&nbsp;{!! Form::checkbox("permissions_role[".$module->name."][".$role->title."]", $role->title, $per_found, ['class' => 'roleCheck']) !!}</td>
                        @foreach($permissions as $perm)
                        
                        <td>{!! Form::checkbox("permissions_role[".$module->name."][".$role->title."][]", $perm->name, $per_found, ['class' => 'chk']) !!}</td>

                        @endforeach
                    </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript') 
    <script>
        @can('user_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}';
        @endcan

    </script>
@endsection