@include('admin.emails.header') 

                <table border="0" cellpadding="0" cellspacing="0" class="container">
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
                            <table class="table table-bordered table-striped">
                                <tbody><tr>
                                    <th>Company</th>
                                    <td field-key="company">{{  $service_request->company->name or ''  }}</td>
                                </tr>
                                <tr>
                                    <th>Service Type</th>
                                    <td field-key="service_type">{{ $service_request->service_type }}</td>
                                </tr>
                                <tr>
                                    <th>Service Center</th>
                                    <td field-key="service_center">{{ $service_request->service_center->name or '' }}</td>
                                </tr>
                                <tr>
                                    <th>Technician</th>
                                    <td field-key='technician'>{{ $service_request->technician->name or '' }}</td>
                                    
                                </tr>
                                <tr>
                                    <th>Call Type</th>
                                    <td field-key='call_type'>{{ $service_request->call_type }}</td>
                                </tr>
                                <tr>
                                    <th>Call Location</th>
                                    <td field-key='call_location'>{{ $service_request->call_location }}</td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td field-key='call_location'>{{ $service_request->priority }}</td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td field-key='product'>{{ $service_request->product->name or '' }}</td>
                                </tr>
                                <tr>
                                    <th>Make</th>
                                    <td field-key='make'>{{ $service_request->make }}</td>
                                </tr>
                                <tr>
                                    <th>Model No</th>
                                    <td field-key='model_no'>{{ $service_request->model_no }}</td>
                                </tr>
                                <tr>
                                    <th>Is Item In Warranty</th>
                                    <td field-key='is_item_in_warrenty'>{{ $service_request->is_item_in_warrenty }}</td>
                                </tr>
                                <tr>
                                    <th>Bill No</th>
                                    <td field-key='bill_no'>{{ $service_request->bill_no }}</td>
                                </tr>
                                <tr>
                                    <th>Bill Date</th>
                                    <td field-key='bill_date'>{{ $service_request->bill_date }}</td>
                                </tr>
                                <tr>
                                    <th>Serial No</th>
                                    <td field-key='serial_no'>{{ $service_request->serial_no }}</td>
                                </tr>
                                <tr>
                                    <th>MOP</th>
                                    <td field-key='mop'>{{ $service_request->mop }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase From</th>
                                    <td field-key='purchase_from'>{{ $service_request->purchase_from }}</td>
                                </tr>
                                <tr>
                                    <th>Adavance Amount</th>
                                    <td field-key='adavance_amount'>{{ $service_request->adavance_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Installation Charges</th>
                                     <td field-key='installation_charge'>{{ $service_request->installation_charge }}</td>
                                </tr>
                                <tr>
                                    <th>Service Charge</th>
                                    <td field-key='service_charge'>{{ $service_request->service_charge }}</td>
                                </tr>
                                <tr>
                                    <th>Distance (km)</th>
                                    <td field-key='km_distance'>{{ $service_request->km_distance }}</td>
                                </tr>
                                <tr>
                                    <th>Charge Per KM</th>
                                    <td field-key='km_charge'>{{ $service_request->km_charge }}</td>
                                </tr>
                                <tr>
                                    <th>Service Tag</th>
                                    <td field-key='service_tag'>{{ $service_request->service_tag }}</td>
                                </tr>
                                <tr>
                                    <th>Complain Details</th>
                                    <td field-key='complain_details'>{!! $service_request->complain_details !!}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td field-key='note'>{{ $service_request->note }}</td>
                                </tr>
                                <tr>
                                    <th>Completion Date</th>
                                    <td field-key='completion_date'>{{ $service_request->completion_date }}</td>
                                </tr>
                                <tr>
                                    <th>Parts</th>
                                    <td field-key='parts'>
                                        @foreach ($service_request->parts as $singleParts)
                                            <span class="label label-info label-many">{{ $singleParts->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Charges For</th>
                                    <td field-key='additional_charges_title'>{{ $service_request->additional_charge_title }}</td>
                                </tr>
                                <tr>
                                    <th>Additional Charges</th>
                                    <td field-key='additional_charges'>{{ $service_request->additional_charges }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td field-key='amount'>{{ $service_request->amount }}</td>
                                </tr>
                                <tr>
                                    <th>Request Status</th>
                                    <td field-key='status'>{{ $service_request->status }}</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                