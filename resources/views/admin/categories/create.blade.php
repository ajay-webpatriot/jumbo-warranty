@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.categories.title')</h3> -->
    {!! Form::open(['method' => 'POST', 'route' => ['admin.categories.store'],'id' => 'formCategories','onsubmit' => "return saveButton()"]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.categories.formTitle')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('quickadmin.categories.fields.name').'*', ['class' => 'control-label']) !!}
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
                    {!! Form::label('service_charge', trans('quickadmin.categories.fields.service-charge').'*', ['class' => 'control-label']) !!}
                    <div class="input-group">
                        <label class="input-group-addon" for="service_charge">
                            <span class="fa fa-rupee"></span>
                        </label>
                        {!! Form::text('service_charge', old('service_charge'), ['class' => 'form-control', 'placeholder' => '', 'required' => '', 'onkeypress' => 'return checkIsDecimalNumber(this,event)']) !!}
                    </div>
                    <p class="help-block"></p>
                    @if($errors->has('service_charge'))
                        <p class="help-block">
                            {{ $errors->first('service_charge') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('status', trans('quickadmin.categories.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '','style' => 'width:100%']) !!}
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

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formCategoriesButton']) !!}
    <a href="{{ route('admin.categories.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <!-- <script type="text/html" id="products-template">
        @include('admin.categories.products_row',
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