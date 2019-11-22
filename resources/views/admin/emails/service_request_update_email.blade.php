@include('admin.emails.header') 

                <table border="0" cellpadding="0" cellspacing="0" class="container" style="width:90%;">
                    <tr>
                        <td align="center" height="35"></td>
                    </tr>
<!-- 
                    <tr>
                        <td align="center" valign="top" class="bodyContent" bgcolor="#ffffff">
                            <div>
                                <span class="divider">―</span>

                            </div>
                        </td>
                    </tr> -->
                    <tr>
                        <td align="center" class="bodyContent" bgcolor="#ffffff">
                        

                            <div style="margin-top: 2%;">
                                <h3 style="margin: 1em 0px 1em 0px;">Service Request ( {{ 'JW'.sprintf("%04d", $service_request->id) }} )</h3>
                                <span>{{$update_message}}</span>
                            </div>
                            <div style="margin-top: 2%;">
                                <a href="{{ route('admin.service_requests.show',[$service_request->id]) }}" class="btn btn-xs btn-primary">Click here for more detail</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                