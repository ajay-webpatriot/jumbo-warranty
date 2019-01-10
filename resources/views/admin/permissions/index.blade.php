@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.permissions.title')</h3> -->
    
    {!! Form::open(['method' => 'POST', 'route' => ['admin.permissions.store']]) !!}
    
    <div class="panel panel-default">
        <div class="panel-heading headerTitle"  >
            <!-- <h4 class="panel-title"> -->
                @lang('quickadmin.permissions.title')
            <!-- </h4> -->
        </div>
        <div>
            <div class="panel-body">
                <div class="row">
                    <table class="table table-bordered table-striped" id="tblPermission">
                        <thead>
                            <tr>
                                <th></th>
                            @foreach($roles as $role)
                            
                                <th>{{ $role->title }}</th>
                            @endforeach
                            </tr>
                            
                        </thead>
                        
                        
                        @foreach($permissions as $perm)
                        
                        <tr>
                        <td>{{ $perm->name }}</td>
                        
                        @foreach($roles as $role)
                        <?php

                            $per_found = null;
                            $options= null ;
                            if( isset($role) ) {
                                // $per_found = $role->hasPermissionTo($perm->name);
                                
                                if($role->title == "Technician")
                                {
                                    if($perm->name == "Company Management")
                                    {
                                        $options="disabled";
                                    }
                                    else if($perm->name == "User Management")
                                    {
                                        $options="disabled";
                                    }
                                } 
                                else if($role->title == "Service Center Admin")
                                {
                                    if($perm->name == "Company Management")
                                    {
                                        $options="disabled";
                                    }
                                }
                               
                            }

                        ?>
                        <td>{!! Form::checkbox("permissions_role[".$role->title."][]", $perm->name, $permissionCheck[$role->id][$perm->id], ['class' => 'chk',$options]) !!}</td>

                        @endforeach
                        
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    
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