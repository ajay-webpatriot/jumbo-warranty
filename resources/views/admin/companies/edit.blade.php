@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.company.title')</h3> -->
    
    {!! Form::model($company, ['method' => 'PUT', 'route' => ['admin.companies.update', $company->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.company.formTitle')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('name', trans('quickadmin.company.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_1', trans('quickadmin.company.fields.address-1').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('address_1', old('address_1'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address_1'))
                        <p class="help-block">
                            {{ $errors->first('address_1') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('credit', trans('quickadmin.company.fields.credit').'', ['class' => 'control-label']) !!}
                    {!! Form::text('credit', old('credit'), ['class' => 'form-control', 'placeholder' => '', 'onkeypress' => 'return checkIsDecimalNumber(this,event)']) !!}
                    @if($available_credit < 0)
                    <font color="red"><p>Available credit is {{$available_credit}}</p></font>
                    @else
                    <font color="green"><p>Available credit is {{$available_credit}}</p></font>
                    @endif
                    <p class="help-block"></p>
                    @if($errors->has('credit'))
                        <p class="help-block">
                            {{ $errors->first('credit') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_2', trans('quickadmin.company.fields.address-2').'', ['class' => 'control-label']) !!}
                    {!! Form::text('address_2', old('address_2'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address_2'))
                        <p class="help-block">
                            {{ $errors->first('address_2') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('installation_charge', trans('quickadmin.company.fields.installation-charge').'*', ['class' => 'control-label']) !!}
                    <div class="input-group">
                        <label class="input-group-addon" for="installation_charge">
                            <span class="fa fa-rupee"></span>
                        </label>
                        {!! Form::text('installation_charge', old('installation_charge'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'onkeypress' => 'return checkIsDecimalNumber(this,event)']) !!}
                    </div>
                    <p class="help-block"></p>
                    @if($errors->has('installation_charge'))
                        <p class="help-block">
                            {{ $errors->first('installation_charge') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('city', trans('quickadmin.company.fields.city').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('city'))
                        <p class="help-block">
                            {{ $errors->first('city') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('status', trans('quickadmin.company.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('state', trans('quickadmin.company.fields.state').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('state', old('state'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('state'))
                        <p class="help-block">
                            {{ $errors->first('state') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                </div>
                <div class="col-xs-6">
                    {!! Form::label('zipcode', trans('quickadmin.company.fields.zipcode').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('zipcode', old('zipcode'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','minlength' => '6','maxlength' => '6']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zipcode'))
                        <p class="help-block">
                            {{ $errors->first('zipcode') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    <a href="{{ route('admin.companies.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

