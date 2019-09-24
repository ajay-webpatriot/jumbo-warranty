@extends('layouts.app')

@section('content')
    {!! Form::open(['method' => 'POST', 'route' => ['admin.companies.store'],'id' => 'formCompany']) !!}

    @include('admin.companies.content')
    
    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formCompanyButton','onclick' => 'saveButton()']) !!}
    <a href="{{ route('admin.companies.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

