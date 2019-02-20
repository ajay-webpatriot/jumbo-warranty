@include('admin.emails.header') 
    <table border="0" cellpadding="0" cellspacing="0" class="container" style="width:90%;">
        <tr>
            <td align="center" height="35"></td>
        </tr>

        <tr>
            <td align="center" valign="top" class="bodyContent" bgcolor="#ffffff">
                
                    <h2 style="background:none;">Hello {{ $user_name }}!</h2>
                    <span class="divider">―</span>
                
            </td>
        </tr>
        <tr>
            <td align="center" class="bodyContent" bgcolor="#ffffff">
                
                    <span>Your one time password is: <b style="font-weight: bold;font-size: 18px;color: #3a4a6b;display:inline !important;background:none;">{{$OTP}}</b></span>
                
            </td>
        </tr>

        <tr>
            <td align="center" height="20"></td>
        </tr>
    </table>
@include('admin.emails.footer') 