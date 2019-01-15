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
                        {{$update_message}}
                        </td>
                    </tr>

                    <tr>
                        <td align="center" height="20"></td>
                    </tr>
                </table>

@include('admin.emails.footer') 
                