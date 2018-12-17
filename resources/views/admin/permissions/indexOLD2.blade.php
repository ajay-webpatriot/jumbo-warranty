@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    <h3 class="page-title">@lang('quickadmin.permissions.title')</h3>
    
    {!! Form::open(['method' => 'POST', 'route' => ['admin.permissions.store']]) !!}
    <!-- @foreach ($roles as $role) -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="{{ isset($role->title) ? str_slug($role->title) :  'permissionHeading' }}">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#dd-{{ isset($role->title) ? str_slug($role->title) :  'permissionHeading' }}" aria-expanded="{{ $closed or 'true' }}" aria-controls="dd-{{ isset($role->title) ? str_slug($role->title) :  'permissionHeading' }}">
                    {{ $role->title or 'Permissions' }}
                </a>
            </h4>
        </div>
        <div id="dd-{{ isset($role->title) ? str_slug($role->title) :  'permissionHeading' }}" class="panel-collapse collapse {{ $closed or 'in' }}" role="tabpanel" aria-labelledby="dd-{{ isset($role->title) ? str_slug($role->title) :  'permissionHeading' }}">
            <div class="panel-body">
                <div class="row">
                    <table class="table table-bordered table-striped" id="tblPermission">
                        <thead>
                            <tr>
                                <th>Permissions</th>
                                <th>Access</th>
                            </tr>
                        </thead>
                    <!-- @foreach($modules as $module) -->
                        <?php
                        $cnt=0;
                        ?>
                        @foreach($permissions as $perm)
                        <?php

                            $per_found = null;
                            if( isset($role) ) {
                                $per_found = $role->hasPermissionTo($perm->name);
                                // $role = RolePermission::findById(auth()->user()->role_id);
                                // $res= $role->hasPermissionTo('User Management');
                                // echo "........... ".$res.".............. ";
                                // echo "<pre>";
                                // print_r($role->syncPermissions());exit;
                                $cnt+=1;
                            }

                        ?>
                        <tr>
                        <td>{{ $perm->name }}{{$cnt}}</td>
                        
                        
                        <td>{!! Form::checkbox("permissions_role[".$role->title."][]", $perm->name, $per_found, ['class' => 'chk']) !!}</td>

                        
                        </tr>
                        @endforeach
                    <!-- @endforeach -->
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- @endforeach -->
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