@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.assign-product.title')</h3> -->
    
    {!! Form::model($assign_product, ['method' => 'PUT', 'route' => ['admin.assign_products.update', $assign_product->company_id],'id' => 'formAssignParts','onsubmit' => "return saveButton()"]) !!}

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.assign-product.formTitle')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('company_id', trans('quickadmin.assign-product.fields.company').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'assign_company_id','disabled' => '','style' => 'width:100%']) !!}
                    {!! Form::hidden('company_id', old('company_id'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_id'))
                        <p class="help-block">
                            {{ $errors->first('company_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('product_id', trans('quickadmin.assign-product.fields.product-id').'*', ['class' => 'control-label']) !!}
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-product_id">
                        {{ trans('quickadmin.qa_select_all') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-product_id">
                        {{ trans('quickadmin.qa_deselect_all') }}
                    </button>
                    {!! Form::select('product_id[]', $product_ids, old('product_id') ? old('product_id') : $assign_product->product_id, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-product_id' , 'required' => '','style' => 'width:100%']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('product_id'))
                        <p class="help-block">
                            {{ $errors->first('product_id') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger','id' => 'formAssignPartsButton']) !!}
    <a href="{{ route('admin.assign_products.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <script>
        $("#selectbtn-product_id").click(function(){
            $("#selectall-product_id > option").prop("selected","selected");
            $("#selectall-product_id").trigger("change");
        });
        $("#deselectbtn-product_id").click(function(){
            $("#selectall-product_id > option").prop("selected","");
            $("#selectall-product_id").trigger("change");
        });
    </script>
@stop