@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.service-request-log.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.company')</th>
                            <td field-key='company'>{{ $service_request_logs->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <td field-key='service_type'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-center')</th>
                            <td field-key='service_center'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.technician')</th>
                            <td field-key='technician'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.call-type')</th>
                            <td field-key='call_type'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.call-location')</th>
                            <td field-key='call_location'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.priority')</th>
                            <td field-key='priority'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <td field-key='product'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.make')</th>
                            <td field-key='make'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.model-no')</th>
                            <td field-key='model_no'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.is-item-in-warrenty')</th>
                            <td field-key='is_item_in_warrenty'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.bill-no')</th>
                            <td field-key='bill_no'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.bill-date')</th>
                            <td field-key='bill_date'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.serial-no')</th>
                            <td field-key='serial_no'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.mop')</th>
                            <td field-key='mop'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.purchase-from')</th>
                            <td field-key='purchase_from'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.adavance-amount')</th>
                            <td field-key='adavance_amount'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-charge')</th>
                            <td field-key='service_charge'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-tag')</th>
                            <td field-key='service_tag'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.complain-details')</th>
                            <td field-key='complain_details'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.note')</th>
                            <td field-key='note'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.completion-date')</th>
                            <td field-key='completion_date'></td>
                        </tr>
                        <?php /*
                        <!-- <tr>
                            <th>@lang('quickadmin.service-request.fields.parts')</th>
                            <td field-key='parts'>
                                @foreach ($service_request_logs->parts as $singleParts)
                                    <span class="label label-info label-many"></span>
                                @endforeach
                            </td>
                        </tr> -->
                        */ ?>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.additional-charges')</th>
                            <td field-key='additional_charges'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <td field-key='amount'></td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <td field-key='status'></td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.service_request_logs.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop
