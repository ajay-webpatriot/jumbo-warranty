@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.customers.title')</h3> -->
    
    {!! Form::model($customer, ['method' => 'PUT', 'route' => ['admin.customers.update', $customer->id],'id' => 'formCustomer','onsubmit' => "return saveButton()"]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.customers.formTitle')
        </div>

        <div class="panel-body">
            @if(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.COMPANY_USER_ROLE_ID'))
                {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control']) !!}
            @else
                <div class="row">
                    <div class="col-xs-6">
                            {!! Form::label('company_id', trans('quickadmin.customers.fields.company').'*', ['class' => 'control-label']) !!}
                            {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('company_id'))
                                <p class="help-block">
                                    {{ $errors->first('company_id') }}
                                </p>
                            @endif
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-xs-6">
                    {!! Form::label('firstname', trans('quickadmin.customers.fields.firstname').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('firstname', old('firstname'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('firstname'))
                        <p class="help-block">
                            {{ $errors->first('firstname') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_1', trans('quickadmin.customers.fields.address-1').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('lastname', trans('quickadmin.customers.fields.lastname').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('lastname', old('lastname'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('lastname'))
                        <p class="help-block">
                            {{ $errors->first('lastname') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('address_2', trans('quickadmin.customers.fields.address-2').'', ['class' => 'control-label']) !!}
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
                    {!! Form::label('phone', trans('quickadmin.customers.fields.phone').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','minlength' => '11','maxlength' => '11']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('city', trans('quickadmin.customers.fields.city').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('email', trans('quickadmin.customers.fields.email').'', ['class' => 'control-label']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-6">
                    {!! Form::label('state', trans('quickadmin.customers.fields.state').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('status', trans('quickadmin.customers.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6">
                    {!! Form::label('zipcode', trans('quickadmin.customers.fields.zipcode').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('zipcode', old('zipcode'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','minlength' => '6','maxlength' => '6']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zipcode'))
                        <p class="help-block">
                            {{ $errors->first('zipcode') }}
                        </p>
                    @endif
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-xs-6"> -->
                    {{-- !! Form::label('status', trans('quickadmin.customers.fields.status').'*', ['class' => 'control-label']) !! --}}
                    {{-- !! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !! --}}
                    <!-- <p class="help-block"></p> -->
                    {{-- @if($errors->has('status')) --}}
                        <!-- <p class="help-block"> -->
                            {{-- $errors->first('status') --}}
                        <!-- </p> -->
                    {{-- @endif --}}
                <!-- </div>
            </div> -->
            
        </div>
    </div>
    
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger','id' => 'formCustomerButton']) !!}
    <a href="{{ route('admin.customers.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

   <!--  <script type="text/html" id="service-request-template">
        @include('admin.customers.service_requests_row',
                [
                    'index' => '_INDEX_',
                ])
               </script > 

            <script>
        $('.add-new').click(function () {
            var tableBody = $(this).parent().find('tbody');
            var template = $('#' + tableBody.attr('id') + '-template').html();
            var lastIndex = parseInt(tableBody.find('tr').last().data('index'));
            if (isNaN(lastIndex)) {
                lastIndex = 0;
            }
            tableBody.append(template.replace(/_INDEX_/g, lastIndex + 1));
            return false;
        });
        $(document).on('click', '.remove', function () {
            var row = $(this).parentsUntil('tr').parent();
            row.remove();
            return false;
        });
        </script> -->
@stop