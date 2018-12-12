@extends('layouts.app')

@section('content')
    @if(auth()->user()->role_id ==  $_ENV['COMPANY_ADMIN_ROLE_ID'])
    <h3 class="page-title">@lang('quickadmin.users.companyUserTitle')</h3>
    @elseif(auth()->user()->role_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
    <h3 class="page-title">@lang('quickadmin.users.technicianTitle')</h3>
    @else
    <h3 class="page-title">@lang('quickadmin.users.title')</h3>
    @endif
    {!! Form::open(['method' => 'POST', 'route' => ['admin.users.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    
                    {!! Form::hidden('loggedUser_role',$logged_userRole_id, ['class' => 'form-control', 'placeholder' => '','id' => 'loggedUser_role']) !!}
                    <p class="help-block"></p>
                    
                </div>
            </div>
            @if($logged_userRole_id == $_ENV['COMPANY_ADMIN_ROLE_ID'])
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('role_id', trans('quickadmin.users.fields.role').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('role_name', $roles[$_ENV['COMPANY_USER_ROLE_ID']], ['class' => 'form-control', 'placeholder' => 'Role','disabled' => '']) !!}
                    {!! Form::hidden('role_id', $_ENV['COMPANY_USER_ROLE_ID'], ['class' => 'form-control','id' => 'userRole']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('role_id'))
                        <p class="help-block">
                            {{ $errors->first('role_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @elseif($logged_userRole_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('role_id', trans('quickadmin.users.fields.role').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('role_name', $roles[$_ENV['TECHNICIAN_ROLE_ID']], ['class' => 'form-control', 'placeholder' => 'Role','disabled' => '']) !!}
                    {!! Form::hidden('role_id', $_ENV['TECHNICIAN_ROLE_ID'], ['class' => 'form-control','id' => 'userRole']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('role_id'))
                        <p class="help-block">
                            {{ $errors->first('role_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('role_id', trans('quickadmin.users.fields.role').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('role_id', $roles, old('role_id'), ['class' => 'form-control select2', 'required' => '','id' => 'userRole']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('role_id'))
                        <p class="help-block">
                            {{ $errors->first('role_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
            @if(auth()->user()->role_id == $_ENV['COMPANY_ADMIN_ROLE_ID'])
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('company_id', trans('quickadmin.users.fields.company').'', ['class' => 'control-label']) !!}
                    {!! Form::text('company_name', $companies[auth()->user()->company_id], ['class' => 'form-control', 'placeholder' => 'Comapny Name','disabled' => '']) !!}
                    {!! Form::hidden('company_id', auth()->user()->company_id, ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_id'))
                        <p class="help-block">
                            {{ $errors->first('company_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('company_id', trans('quickadmin.users.fields.company').'', ['class' => 'control-label']) !!}
                    {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2','id' => 'userCompany']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_id'))
                        <p class="help-block">
                            {{ $errors->first('company_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
            @if(auth()->user()->role_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('company_id', trans('quickadmin.users.fields.service-center').'', ['class' => 'control-label']) !!}
                    {!! Form::text('service_center_name', $service_centers[auth()->user()->service_center_id], ['class' => 'form-control', 'placeholder' => 'Comapny Name','disabled' => '']) !!}
                    {!! Form::hidden('service_center_id', auth()->user()->service_center_id, ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_id'))
                        <p class="help-block">
                            {{ $errors->first('company_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('service_center_id', trans('quickadmin.users.fields.service-center').'', ['class' => 'control-label']) !!}
                    {!! Form::select('service_center_id', $service_centers, old('service_center_id'), ['class' => 'form-control select2','id' => 'userServiceCenter']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('service_center_id'))
                        <p class="help-block">
                            {{ $errors->first('service_center_id') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('quickadmin.users.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('phone', trans('quickadmin.users.fields.phone').'', ['class' => 'control-label']) !!}
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('address_1', trans('quickadmin.users.fields.address-1').'', ['class' => 'control-label']) !!}
                    {!! Form::text('address_1', old('address_1'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address_1'))
                        <p class="help-block">
                            {{ $errors->first('address_1') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('address_2', trans('quickadmin.users.fields.address-2').'', ['class' => 'control-label']) !!}
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
                <div class="col-xs-12 form-group">
                    {!! Form::label('city', trans('quickadmin.users.fields.city').'', ['class' => 'control-label']) !!}
                    {!! Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('city'))
                        <p class="help-block">
                            {{ $errors->first('city') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('state', trans('quickadmin.users.fields.state').'', ['class' => 'control-label']) !!}
                    {!! Form::text('state', old('state'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('state'))
                        <p class="help-block">
                            {{ $errors->first('state') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('zipcode', trans('quickadmin.users.fields.zipcode').'', ['class' => 'control-label']) !!}
                    {!! Form::text('zipcode', old('zipcode'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zipcode'))
                        <p class="help-block">
                            {{ $errors->first('zipcode') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('location_address', trans('quickadmin.users.fields.location').'', ['class' => 'control-label']) !!}
                    {!! Form::text('location_address', old('location_address'), ['class' => 'form-control map-input', 'id' => 'location-input']) !!}
                    {!! Form::hidden('location_latitude', 0 , ['id' => 'location-latitude']) !!}
                    {!! Form::hidden('location_longitude', 0 , ['id' => 'location-longitude']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('location'))
                        <p class="help-block">
                            {{ $errors->first('location') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div id="location-map-container" style="width:100%;height:200px; ">
                <div style="width: 100%; height: 100%" id="location-map"></div>
            </div>
            @if(!env('GOOGLE_MAPS_API_KEY'))
                <b>'GOOGLE_MAPS_API_KEY' is not set in the .env</b>
            @endif
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('email', trans('quickadmin.users.fields.email').'*', ['class' => 'control-label']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('password', trans('quickadmin.users.fields.password').'*', ['class' => 'control-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('status', trans('quickadmin.users.fields.status').'*', ['class' => 'control-label']) !!}
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
    <div class="panel panel-default">
        <div class="panel-heading">
            Service request
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>@lang('quickadmin.service-request.fields.make')</th>
                        <th>@lang('quickadmin.service-request.fields.model-no')</th>
                        <th>@lang('quickadmin.service-request.fields.bill-no')</th>
                        <th>@lang('quickadmin.service-request.fields.bill-date')</th>
                        <th>@lang('quickadmin.service-request.fields.serial-no')</th>
                        <th>@lang('quickadmin.service-request.fields.purchase-from')</th>
                        <th>@lang('quickadmin.service-request.fields.adavance-amount')</th>
                        <th>@lang('quickadmin.service-request.fields.service-charge')</th>
                        <th>@lang('quickadmin.service-request.fields.service-tag')</th>
                        <th>@lang('quickadmin.service-request.fields.note')</th>
                        <th>@lang('quickadmin.service-request.fields.additional-charges')</th>
                        <th>@lang('quickadmin.service-request.fields.amount')</th>
                        
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="service-request">
                    @foreach(old('service_requests', []) as $index => $data)
                        @include('admin.users.service_requests_row', [
                            'index' => $index
                        ])
                    @endforeach
                </tbody>
            </table>
            <a href="#" class="btn btn-success pull-right add-new">@lang('quickadmin.qa_add_new')</a>
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
   <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
   <script src="/adminlte/js/mapInput.js"></script>

    <script type="text/html" id="service-request-template">
        @include('admin.users.service_requests_row',
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

        </script>
@stop