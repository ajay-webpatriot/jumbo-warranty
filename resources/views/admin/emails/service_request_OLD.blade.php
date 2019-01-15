@include('admin.emails.header') 

                <table border="0" cellpadding="0" cellspacing="0" class="container" style="width:50%;">
                    <tr>
                        <td align="center" height="35"></td>
                    </tr>

                    <tr>
                        <td align="center" valign="top" class="bodyContent" bgcolor="#ffffff">
                            <div>
                                <h2>Hello {{ $user_name }}!</h2>
                                <span class="divider">―</span>

                                <h2>Service Request Details</h3>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bodyContent" bgcolor="#ffffff">
                            <table style="border-collapse:collapse;border: 1px solid #f4f4f4;border-spacing: 0;width: 94%;margin:15px;" class="table table-bordered table-striped">
                                <tbody>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.company')</th>
                                    <td field-key="company">{{  $service_request->company->name or ''  }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.service-type')</th>
                                    <td style="border-collapse:collapse;" field-key="service_type">{{ $service_request->service_type }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.service-center')</th>
                                    <td style="border-collapse:collapse;" field-key="service_center">{{ $service_request->service_center->name or '' }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.technician')</th>
                                    <td style="border-collapse:collapse;" field-key='technician'>{{ $service_request->technician->name or '' }}</td>
                                    
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.call-type')</th>
                                    <td style="border-collapse:collapse;" field-key='call_type'>{{ $service_request->call_type }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.call-location')</th>
                                    <td style="border-collapse:collapse;" field-key='call_location'>{{ $service_request->call_location }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.priority')</th>
                                    <td style="border-collapse:collapse;" field-key='call_location'>{{ $service_request->priority }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.product')</th>
                                    <td style="border-collapse:collapse;" field-key='product'>{{ $service_request->product->name or '' }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.make')</th>
                                    <td style="border-collapse:collapse;" field-key='make'>{{ $service_request->make }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.model-no')</th>
                                    <td style="border-collapse:collapse;" field-key='model_no'>{{ $service_request->model_no }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.is-item-in-warrenty')</th>
                                    <td style="border-collapse:collapse;" field-key='is_item_in_warrenty'>{{ $service_request->is_item_in_warrenty }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.bill-no')</th>
                                    <td style="border-collapse:collapse;" field-key='bill_no'>{{ $service_request->bill_no }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.bill-date')</th>
                                    <td style="border-collapse:collapse;" field-key='bill_date'>{{ $service_request->bill_date }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.serial-no')</th>
                                    <td style="border-collapse:collapse;" field-key='serial_no'>{{ $service_request->serial_no }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.mop')</th>
                                    <td style="border-collapse:collapse;" field-key='mop'>{{ $service_request->mop }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.purchase-from')</th>
                                    <td style="border-collapse:collapse;" field-key='purchase_from'>{{ $service_request->purchase_from }}</td>
                                </tr>
                                <!-- <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">Adavance Amount</th>
                                    <td style="border-collapse:collapse;" field-key='adavance_amount'>{{ $service_request->adavance_amount }}</td>
                                </tr> -->
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.installation-charge')</th>
                                     <td style="border-collapse:collapse;" field-key='installation_charge'>{{ $service_request->installation_charge }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.service-charge')</th>
                                    <td style="border-collapse:collapse;" field-key='service_charge'>{{ $service_request->service_charge }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.distance')</th>
                                    <td style="border-collapse:collapse;" field-key='km_distance'>{{ $service_request->km_distance }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.distance-charge')</th>
                                    <td style="border-collapse:collapse;" field-key='km_charge'>{{ $service_request->km_charge }}</td>
                                </tr>
                                <!-- <tr>
                                    <th style="text-align: left;">Service Tag</th>
                                    <td style="border-collapse:collapse;" field-key='service_tag'>{{ $service_request->service_tag }}</td>
                                </tr> -->
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.complain-details')</th>
                                    <td style="border-collapse:collapse;" field-key='complain_details'>{!! $service_request->complain_details !!}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.note')</th>
                                    <td style="border-collapse:collapse;" field-key='note'>{{ $service_request->note }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.completion-date')</th>
                                    <td style="border-collapse:collapse;" field-key='completion_date'>{{ $service_request->completion_date }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.parts')</th>
                                    <td style="border-collapse:collapse;" field-key='parts'>
                                        @foreach ($service_request->parts as $singleParts)
                                            <span class="label label-info label-many">{{ $singleParts->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.charges_for')</th>
                                    <td style="border-collapse:collapse;" field-key='additional_charges_title'>{{ $service_request->additional_charges_title }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.additional-charges')</th>
                                    <td style="border-collapse:collapse;" field-key='additional_charges'>{{ $service_request->additional_charges }}</td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.amount')</th>
                                    <td style="border-collapse:collapse;" field-key='amount'>{{ $service_request->amount }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">@lang('quickadmin.service-request.fields.status')</th>
                                    <td style="border-collapse:collapse;" field-key='status'>{{ $service_request->status }}</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                