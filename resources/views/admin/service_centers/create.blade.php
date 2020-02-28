@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center.title')</h3> -->
    {!! Form::open(['method' => 'POST', 'route' => ['admin.service_centers.store'],'id' => 'formServiceCenter','onsubmit' => "return saveButton()"]) !!}

    @include('admin.service_centers.content')

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formServiceCenterButton']) !!}
    <a href="{{ route('admin.service_centers.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
   <script src="/adminlte/js/mapInput.js"></script>

@stop