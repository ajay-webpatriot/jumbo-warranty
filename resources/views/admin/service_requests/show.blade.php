@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.service-request.title')</h3>

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
                            <td field-key='company'>{{ $service_request->company->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-type')</th>
                            <td field-key='service_type'>{{ $service_request->service_type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-center')</th>
                            <td field-key='service_center'>{{ $service_request->service_center->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.technician')</th>
                            <td field-key='technician'>{{ $service_request->technician->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.call-type')</th>
                            <td field-key='call_type'>{{ $service_request->call_type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.call-location')</th>
                            <td field-key='call_location'>{{ $service_request->call_location }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.priority')</th>
                            <td field-key='priority'>{{ $service_request->priority }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.product')</th>
                            <td field-key='product'>{{ $service_request->product->name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.make')</th>
                            <td field-key='make'>{{ $service_request->make }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.model-no')</th>
                            <td field-key='model_no'>{{ $service_request->model_no }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.is-item-in-warrenty')</th>
                            <td field-key='is_item_in_warrenty'>{{ $service_request->is_item_in_warrenty }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.bill-no')</th>
                            <td field-key='bill_no'>{{ $service_request->bill_no }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.bill-date')</th>
                            <td field-key='bill_date'>{{ $service_request->bill_date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.serial-no')</th>
                            <td field-key='serial_no'>{{ $service_request->serial_no }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.mop')</th>
                            <td field-key='mop'>{{ $service_request->mop }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.purchase-from')</th>
                            <td field-key='purchase_from'>{{ $service_request->purchase_from }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.adavance-amount')</th>
                            <td field-key='adavance_amount'>{{ $service_request->adavance_amount }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.installation-charge')</th>
                            <td field-key='installation_charge'>{{ $service_request->installation_charge }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-charge')</th>
                            <td field-key='service_charge'>{{ $service_request->service_charge }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.distance')</th>
                            <td field-key='km_distance'>{{ $service_request->km_distance }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.distance-charge')</th>
                            <td field-key='km_charge'>{{ $service_request->km_charge }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.service-tag')</th>
                            <td field-key='service_tag'>{{ $service_request->service_tag }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.complain-details')</th>
                            <td field-key='complain_details'>{!! $service_request->complain_details !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.note')</th>
                            <td field-key='note'>{{ $service_request->note }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.completion-date')</th>
                            <td field-key='completion_date'>{{ $service_request->completion_date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.parts')</th>
                            <td field-key='parts'>
                                @foreach ($service_request->parts as $singleParts)
                                    <span class="label label-info label-many">{{ $singleParts->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.additional-charges')</th>
                            <td field-key='additional_charges'>{{ $service_request->additional_charges }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.amount')</th>
                            <td field-key='amount'>{{ $service_request->amount }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-request.fields.status')</th>
                            <td field-key='status'>{{ $service_request->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.service_requests.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
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
