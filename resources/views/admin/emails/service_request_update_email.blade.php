@include('admin.emails.header') 

                <table border="0" cellpadding="0" cellspacing="0" class="container" style="width:90%;">
                    <tr>
                        <td align="center" height="35"></td>
                    </tr>

                    <tr>
                        <td align="center" valign="top" class="bodyContent" bgcolor="#ffffff">
                            <div>
                                <h2>Hello {{ $user_name }}!</h2>
                                <span class="divider">―</span>

                                <!-- <h2>Service Request Details</h3> -->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bodyContent" bgcolor="#ffffff">
                        

                            <div>
                                <h3>Service Request</h3>
                                <span>{{$update_message}}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.service_requests.show',[$service_request->id]) }}" class="btn btn-xs btn-primary">Click here for more detail</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                