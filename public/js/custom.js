
$(document).ready(function(){

	$(document).on('change', '#userRole', function () {
       // set required validation for company and service center dropdown in add/edit user 
       var loggedUser_role=$("#loggedUser_role").val();

       $('#userCompany').removeAttr('required');
       $("#userServiceCenter").removeAttr('required');
       if(loggedUser_role == SUPER_ADMIN_ROLE_ID || ADMIN_ROLE_ID == 3)
       {
            if($(this).val() == COMPANY_ADMIN_ROLE_ID || $(this).val() == COMPANY_USER_ROLE_ID)
            {
                $('#userCompany').attr('required', true);
            }
            else if($(this).val() == SERVICE_ADMIN_ROLE_ID || $(this).val() == TECHNICIAN_ROLE_ID)
	       	{
	       		$('#userServiceCenter').attr('required', true);
	       	}
       }

  });

  $(document).on('click', '.allRoleCheck', function () {
       // checked current column checkbox on click of permission checkbox in permission management
       var index=$(this).closest("th").index();
       $(this).closest("table").find("tr td:nth-child("+(index+1)+") input[type=checkbox]").prop("checked", $(this).prop("checked"));
       // $("#tblPermission tr td:nth-child("+(index+1)+") input[type=checkbox]").prop("checked", true);
  });
  $(document).on('click', '.roleCheck', function () {
       // checked current row checkbox on click of permission checkbox in permission management
       var index=$(this).closest("tr").index();
       $(this).closest("tr").find("input[type=checkbox]").prop("checked", $(this).prop("checked"));
  });

})