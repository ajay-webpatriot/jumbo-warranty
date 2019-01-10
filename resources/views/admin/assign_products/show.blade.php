@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.assign-product.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.assign-product.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.assign-product.fields.company')</th>
                            <td field-key='company'>{{ $assign_product->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.assign-product.fields.product-id')</th>
                            <td field-key='product_id'>
                                @foreach ($assign_product->product_id as $singleProductId)
                                    <span class="label label-info label-many">{{ $singleProductId->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.assign_products.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop


