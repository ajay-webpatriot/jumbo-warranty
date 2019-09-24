@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.assign-product.title')</h3> -->
    {!! Form::open(['method' => 'POST', 'route' => ['admin.assign_products.store'],'id' => 'formAssignParts','onsubmit' => "return saveButton()"]) !!}

        @include('admin.assign_products.content')

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formAssignPartsButton']) !!}
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