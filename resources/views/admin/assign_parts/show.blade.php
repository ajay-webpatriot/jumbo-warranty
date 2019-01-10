@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.assign-parts.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.assign-parts.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.assign-parts.fields.company')</th>
                            <td field-key='company'>{{ $assign_part->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.assign-parts.fields.product-parts')</th>
                            <td field-key='product_parts'>{{ $assign_part->product_parts->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.assign-parts.fields.quantity')</th>
                            <td field-key='quantity'>{{ $assign_part->quantity }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.assign_parts.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop


