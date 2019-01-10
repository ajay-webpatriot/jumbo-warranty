@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.invoices.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.invoices.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.invoices.fields.company')</th>
                            <td field-key='company'>{{ $invoice->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.invoices.fields.status')</th>
                            <td field-key='status'>{{ $invoice->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop


