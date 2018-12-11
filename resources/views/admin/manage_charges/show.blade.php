@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.manage-charges.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.manage-charges.fields.km-charge')</th>
                            <td field-key='km_charge'>{{ $manage_charge->km_charge }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.manage-charges.fields.status')</th>
                            <td field-key='status'>{{ $manage_charge->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.manage_charges.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop


