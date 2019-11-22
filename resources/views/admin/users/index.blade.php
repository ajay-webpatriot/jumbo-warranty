@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.users.title')</h3> -->
    @can('user_create')
    <p class="text-right">
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
    @endcan
    <style type="text/css">
    /* table th td align ment verticle center*/
    td,th{
      vertical-align: middle!important;
    }
    </style>    

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.users.title')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.phone')</th>
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.users.fields.status')</th>
                        <!-- <th>&nbsp;</th> -->
                        <th>@lang('quickadmin.qa_action')</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('user_delete')
                                    <td></td>
                                @endcan

                                
                                <td field-key='name'>{{ $user->name }}</td>
                                <td field-key='phone' align="center">{{ $user->phone }}</td>
                                <td field-key='email'>{{ $user->email }}</td>
                                <td field-key='status' align="center">{{ $user->status }}</td>
                                <td align="center" class="action_button">
                                    @can('user_edit')
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                    @endcan
                                    @can('user_delete')
                                    <form method="POST" action="<?php echo route('admin.users.destroy',$user->id);?>" accept-charset="UTF-8" style="display: inline-block;" onsubmit="return confirm('Are you sure ?');"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="<?php echo csrf_token();?>">
                                    <button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <?php
                                    /*old code
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.users.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    */ ?>
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td align="center" colspan="20">@lang('quickadmin.qa_no_entries_in_table')</td>
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