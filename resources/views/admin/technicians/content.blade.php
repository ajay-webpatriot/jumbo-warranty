<div class="panel panel-default">
    <div class="panel-heading headerTitle">
        @lang('quickadmin.users.technicianFormTitle')
    </div>
    
    <div class="panel-body">
        
       <!--  {!! Form::hidden('loggedUser_role',auth()->user()->role_id, ['class' => 'form-control', 'placeholder' => '','id' => 'loggedUser_role']) !!} -->
        
        @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
            {!! Form::hidden('service_center_id', auth()->user()->service_center_id, ['class' => 'form-control', 'id' => 'userServiceCenter']) !!}
        @else
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            {!! Form::label('service_center_id', trans('quickadmin.users.fields.service-center').'*', ['class' => 'control-label']) !!}
                            {!! Form::select('service_center_id', $service_centers, old('service_center_id'), ['class' => 'form-control select2','id' => 'userServiceCenter', 'required' => '','style' => 'width:100%']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('service_center_id'))
                                <p class="help-block">
                                    {{ $errors->first('service_center_id') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 ">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('name', trans('quickadmin.users.fields.name').'*', ['class' => 'control-label']) !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('name'))
                            <p class="help-block">
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('phone', trans('quickadmin.users.fields.phone').'*', ['class' => 'control-label']) !!}
                        {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','minlength' => '11','maxlength' => '11']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('phone'))
                            <p class="help-block">
                                {{ $errors->first('phone') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('email', trans('quickadmin.users.fields.email').'*', ['class' => 'control-label']) !!}
                        {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('email'))
                            <p class="help-block">
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('password', trans('quickadmin.users.fields.password').'*', ['class' => 'control-label']) !!}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('password'))
                            <p class="help-block">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('password_confirmation', trans('quickadmin.users.fields.confirm-password'), ['class' => 'control-label']) !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('password_confirmation'))
                            <p class="help-block">
                                {{ $errors->first('password_confirmation') }}
                            </p>
                        @endif
                    </div>

                    @if(isset($enum_technician_status))
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('status', trans('quickadmin.users.fields.status').'*', ['class' => 'control-label']) !!}
                        {!! Form::select('status', $enum_technician_status, old('status'), ['class' => 'form-control select2', 'required' => '', 'id' => 'technician_status','style' => 'width:100%']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('status'))
                            <p class="help-block">
                                {{ $errors->first('status') }}
                            </p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6 ">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('address_1', trans('quickadmin.users.fields.address-1').'*', ['class' => 'control-label']) !!}
                        {!! Form::text('address_1', old('address_1'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('address_1'))
                            <p class="help-block">
                                {{ $errors->first('address_1') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-12 col-sm-12">
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

                {!! Form::hidden('location_latitude', 0 , ['id' => 'location-latitude']) !!}
                {!! Form::hidden('location_longitude', 0 , ['id' => 'location-longitude']) !!}

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('city', trans('quickadmin.users.fields.city').'*', ['class' => 'control-label']) !!}
                        {!! Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('city'))
                            <p class="help-block">
                                {{ $errors->first('city') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('state', trans('quickadmin.users.fields.state').'*', ['class' => 'control-label']) !!}
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
                    <div class="col-md-12 col-sm-12">
                        {!! Form::label('zipcode', trans('quickadmin.users.fields.zipcode').'*', ['class' => 'control-label']) !!}
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
    </div>
</div>