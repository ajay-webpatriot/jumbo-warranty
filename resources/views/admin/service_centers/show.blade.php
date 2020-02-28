@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-center.formTitle')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.name')</th>
                            <td field-key='name'>{{ $service_center->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.commission')</th>
                            <td field-key='commission'>{{ $service_center->commission }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.address-1')</th>
                            <td field-key='address_1'>{{ $service_center->address_1 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.address-2')</th>
                            <td field-key='address_2'>{{ $service_center->address_2 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.city')</th>
                            <td field-key='city'>{{ $service_center->city }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.state')</th>
                            <td field-key='state'>{{ $service_center->state }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.zipcode')</th>
                            <td field-key='zipcode'>{{ $service_center->zipcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.supported-zipcode')</th>
                            <td field-key='zipcode'>{{ $service_center->supported_zipcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.status')</th>
                            <td field-key='status'>{{ $service_center->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- <p>&nbsp;</p> -->

            <!-- <a href="{{ route('admin.service_centers.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a> -->
        </div>
    </div>
@stop

@section('javascript')
    @parent
@stop
