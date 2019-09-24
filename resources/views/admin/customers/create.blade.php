@extends('layouts.app')

@section('content')
    
    <!-- <h3 class="page-title">@lang('quickadmin.customers.title')</h3> -->
    
    {!! Form::open(['method' => 'POST', 'route' => ['admin.customers.store'],'id' => 'formCustomer','onsubmit' => "return saveButton()"]) !!}

    @include('admin.customers.content')

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger','id' => 'formCustomerButton']) !!}
    <a href="{{ route('admin.customers.index') }}" class="btn btn-default">@lang('quickadmin.qa_cancel')</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <!-- <script type="text/html" id="service-request-template">
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