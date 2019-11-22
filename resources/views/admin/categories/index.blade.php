@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.categories.title')</h3> -->
    @can('category_create')
    <p class="text-right">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    <style type="text/css">
    /* table th td align ment verticle center*/
    td,th{
      vertical-align: middle!important;
    }
    </style>

    @can('category_delete')
    <!-- <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.categories.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('quickadmin.qa_all')</a></li> |
            <li><a href="{{ route('admin.categories.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('quickadmin.qa_trash')</a></li>
        </ul>
    </p> -->
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.categories.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($categories) > 0 ? 'datatable' : '' }} @can('category_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('category_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('quickadmin.categories.fields.name')</th>
                        <th>@lang('quickadmin.categories.fields.service-charge')</th>
                        <th>@lang('quickadmin.categories.fields.status')</th>
                        {{-- @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif --}}
                        <th>@lang('quickadmin.qa_action')</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($categories) > 0)
                        @foreach ($categories as $category)
                            <tr data-entry-id="{{ $category->id }}">
                                @can('category_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                <td field-key='name'>{{ $category->name }}</td>
                                <td class="text-right" field-key='service_charge'> <i class="fa fa-rupee"></i> {{ number_format($category->service_charge,2) }}</td>
                                <td field-key='status' class="text-center">{{ $category->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td class="text-center">
                                    @can('category_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.categories.restore', $category->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('category_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.categories.perma_del', $category->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td class="text-center">
                                    @can('category_edit')
                                    <a href="{{ route('admin.categories.edit',[$category->id]) }}" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                    @endcan
                                    @can('category_delete')
                                    <form method="POST" action="<?php echo route('admin.categories.destroy',$category->id);?>" accept-charset="UTF-8" style="display: inline-block;" onsubmit="return confirm('Are you sure ?');"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="<?php echo csrf_token();?>">
                                    <button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <?php /*
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.categories.destroy', $category->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    */ ?>
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td align="center" colspan="8">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('category_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.categories.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection