@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.company.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.company.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.company.fields.name')</th>
                            <td field-key='name'>{{ $company->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.credit')</th>
                            <td field-key='credit'>{{ $company->credit }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.installation-charge')</th>
                            <td field-key='installation_charge'><i class="fa fa-rupee"></i>{{ $company->installation_charge }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.address-1')</th>
                            <td field-key='address_1'>{{ $company->address_1 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.address-2')</th>
                            <td field-key='address_2'>{{ $company->address_2 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.city')</th>
                            <td field-key='city'>{{ $company->city }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.state')</th>
                            <td field-key='state'>{{ $company->state }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.zipcode')</th>
                            <td field-key='zipcode'>{{ $company->zipcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.company.fields.status')</th>
                            <td field-key='status'>{{ $company->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- <p>&nbsp;</p> -->

            <!-- <a href="{{ route('admin.companies.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a> -->
        </div>
    </div>
@stop


