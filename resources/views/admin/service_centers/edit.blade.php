@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center.title')</h3> -->
    
    {!! Form::model($service_center, ['method' => 'PUT', 'route' => ['admin.service_centers.update', $service_center->id],'id' => 'formServiceCenter']) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-center.formTitle')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('name', trans('quickadmin.service-center.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_1', trans('quickadmin.service-center.fields.address-1').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('address_1', old('address_1'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address_1'))
                        <p class="help-block">
                            {{ $errors->first('address_1') }}
                        </p>
                    @endif
                </div>
            </div>

            {!! Form::hidden('location_latitude', $service_center->location_latitude , ['id' => 'location-latitude']) !!}
            {!! Form::hidden('location_longitude', $service_center->location_longitude , ['id' => 'location-longitude']) !!}

            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('commission', trans('quickadmin.service-center.fields.commission'), ['class' => 'control-label']) !!}
                    {!! Form::number('commission', old('commission'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('commission'))
                        <p class="help-block">
                            {{ $errors->first('commission') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_2', trans('quickadmin.service-center.fields.address-2').'', ['class' => 'control-label']) !!}
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
                    {!! Form::label('zipcode', trans('quickadmin.service-center.fields.zipcode').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('zipcode', old('zipcode'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','minlength' => '6','maxlength' => '6']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zipcode'))
                        <p class="help-block">
                            {{ $errors->first('zipcode') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('city', trans('quickadmin.service-center.fields.city').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('supported_zipcode', trans('quickadmin.service-center.fields.supported-zipcode').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('supported_zipcode', old('supported_zipcode'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'onkeypress' => 'return allowNumberWithComma(this,event)']) !!}
                    <p>(Add multiple supported zipcode by comma separate)</p>
                    <p class="help-block"></p>
                    @if($errors->has('supported_zipcode'))
                        <p class="help-block">
                            {{ $errors->first('supported_zipcode') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('state', trans('quickadmin.service-center.fields.state').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('status', trans('quickadmin.service-center.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-xs-6">
                </div>
                <div class="col-xs-6">
                    
                </div>
            </div> -->
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger','id' => 'formServiceCenterButton','onclick' => 'saveButton()']) !!}
    <a href="{{ route('admin.service_centers.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
   <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
   <script src="/adminlte/js/mapInput.js"></script>

@stop