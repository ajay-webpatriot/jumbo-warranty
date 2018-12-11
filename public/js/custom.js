
$(document).ready(function(){
	$(document).on('change', '#userRole', function () {
       var loggedUser_role=$("#loggedUser_role").val();

       $('#userCompany').removeAttr('required');
       $("#userServiceCenter").removeAttr('required');
       if(loggedUser_role == 1 || loggedUser_role == 3)
       {
            if($(this).val() == 4 || $(this).val() == 7)
            {
                $('#userCompany').attr('required', true);
            }
            else if($(this).val() == 5 || $(this).val() == 6)
	       	{
	       		$('#userServiceCenter').attr('required', true);
	       	}
       }

    });
})