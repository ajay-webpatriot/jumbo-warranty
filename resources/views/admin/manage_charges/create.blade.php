@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.manage-charges.title')</h3> -->
    {!! Form::open(['method' => 'POST', 'route' => ['admin.manage_charges.store'],'id' => 'formManageCharges','onsubmit' => "return saveButton()"]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.manage-charges.formTitle')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('km_charge', trans('quickadmin.manage-charges.fields.km-charge').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('km_charge', old('km_charge'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'onkeypress' => 'return checkIsDecimalNumber(this,event)']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('km_charge'))
                        <p class="help-block">
                            {{ $errors->first('km_charge') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('status', trans('quickadmin.manage-charges.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formManageChargeButton']) !!}
    <a href="{{ route('admin.manage_charges.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

