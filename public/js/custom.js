
$(document).ready(function(){

	$(document).on("keypress","input[name=phone], input[name=zipcode]",function(evt)
	{
		// allows only number for phone number field
		evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;
	    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	        return false;
	    }
	    return true;
	});

	$(document).on("change","#service_center_id",function(evt)
	{
		var serviceCenterId = $(this).val();

		// deselect radio if same service center is not available in radio button
		if($("input[name='suggested_service_center'][value='"+serviceCenterId+"']").length <= 0)
		{
			$("input[name='suggested_service_center']").attr('checked', false);
		}
		else
		{
			$("input[name='suggested_service_center'][value='"+serviceCenterId+"']").attr('checked', true);
		}
		// get technician according to service center for service request 
		getTechnicians(serviceCenterId);
	
	});
	$(document).on("click","input[name='suggested_service_center']",function(evt)
	{
		// set selected service center dropdown on click of suggested radio button
		$("#service_center_id").val($(this).val()).trigger("change");
		
	});
	$(document).on("change","#company_id",function(evt)
	{
		$("#product_error").html('');
		// get technician according to service center for service request 
		var companyId = $(this).val();
		$(".custAddress").html('');
		$.ajax({
	       	type:'GET',
	       	url:APP_URL+'/admin/getCompanyDetails',
	       	data:{
	       		'companyId':companyId
	       	},
	       	dataType: "json",
	       	success:function(data) {
				   
				if(data.no_products == 1){
					
					$("#product_error").html('<p>There are no products for this company.<a href="javascript:void(0);" onclick="AssignProducts('+companyId+')"> Click here to assign.</a> </p>');

					$(".custDiv").hide();
					$("#customer_id").html('');
					$("#customer_id").removeAttr('required');
					$("#selectall-parts").html('');
					$("#product_id").html('');
					return false;
				}else{
					$(".custDiv").show();
					$(".custDiv").find(".select2").select2();
					$('#customer_id').attr('required', 'required');
					$("#customer_id").html(data.custOptions);
					$("#selectall-parts").html(data.partOptions);
					$("#product_id").html(data.productOptions);
				}
	       		
	       	}
	    });
	});
	$(document).on("change","#customer_id",function(evt)
	{
		// get technician according to service center for service request 
		var customerId = $(this).val();

		$.ajax({
	       	type:'GET',
	       	url:APP_URL+'/admin/getCustomerAddress',
	       	data:{
	       		'customerId':customerId
	       	},
	       	dataType: "json",
	       	success:function(data) {
	       		$(".custDiv").show();
	       		$(".custAddress").html(data.address);
	       		getSuggestedServiceCenter(customerId);

	       		
	       	}
	    });
	});

	// $(document).on('submit','#formServiceRequest',function (){
		
		// var status = true;
		// $('*[id^="Additional_charge_for_existing-"]').each(function() {
		// $('.multiple_Additional_charge_for').each(function() {
		// 	var selectedid = $(this).attr("id").split('-');
		// 	$(".error-block_"+selectedid[1]).text('');
		// 	$(".error-amount-block_"+selectedid[1]).text('');
			
		// 	var selectedOption =$("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val();
		// 	var selectedAmount =$("#existingAdditional_charge_"+selectedid[1]).val();

		// 	if(typeof(selectedOption) !== 'undefined' && typeof(selectedAmount) !== 'undefined'){
		// 		// console.log('in ');
		// 		if($("#existingAdditional_charge_"+selectedid[1]).val().trim() != '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() <= 0){
		// 			// console.log('in if ');
		// 			$(".error-amount-block_"+selectedid[1]).text('The additional amount must be greater than 0.');
		// 			status = false;

		// 		}else if($("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val() == '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() != ''){
		// 			// console.log('in else if 1 ');
		// 			$(".error-block_"+selectedid[1]).text('Please select option');
		// 			status = false;
		// 		}else if($("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val() != '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() == ''){
		// 			// console.log('in else if 2 ');
		// 			$(".error-amount-block_"+selectedid[1]).text('Please Enter amount');
		// 			status = false;
		// 		}
		// 	}
		// });
		

		// validate additional charges title and amount on form submit in add/edit service request page
		
		// $("#additional_charges_title").next(".help-block").html("");
		// // $("#additional_charges").next(".help-block").html("");
		// $(".addamountError").html("");
		// var valueAdditionalCharge = $("#additional_charges").val();
		// var valueAdditionalChargeTitle = $("#additional_charges_title").val();
		
		// if(typeof(valueAdditionalCharge) !== 'undefined' && typeof(valueAdditionalChargeTitle) !== 'undefined'){

		// 	if($("#additional_charges").val().trim() != "" && $("#additional_charges").val() <= 0)
		// 	{
		// 		// $("#additional_charges").next(".help-block").html("The additional charges must be greater than 0.");
		// 		$(".addamountError").html("The additional amount must be greater than 0.");
		// 		status = false;
		// 	}
		// 	else if($("#additional_charges_title").val().trim() != "" && $("#additional_charges").val().trim() == "")
		// 	{
		// 		// $("#additional_charges").next(".help-block").html("The additional amount field is required when additional charges title is present.");
		// 		$(".addamountError").html("The additional amount field is required when additional charges title is present.");
		// 		status = false;
		// 	}
		// 	else if($("#additional_charges_title").val().trim() == "" && $("#additional_charges").val().trim() != "")
		// 	{
		// 		$("#additional_charges_title").next(".help-block").html("The additional charges title field is required when additional amount is present.");
		// 		status = false;
		// 	}
		// }
    //     return status;
	// });

	$(document).on('click','#serviceRequest.datatable .select-checkbox, #company.datatable .select-checkbox, #company_admin.datatable .select-checkbox, #company_user.datatable .select-checkbox, #customer.datatable .select-checkbox, #assign_product.datatable .select-checkbox, #assign_part.datatable .select-checkbox, #service_center_admin.datatable .select-checkbox, #technician.datatable .select-checkbox',function (){
		// service request check single checkbox work
		if($(this).closest('tr').hasClass('selected'))
		{
			$(this).closest('tr').removeClass('selected');
		}	
		else
		{
			$(this).closest('tr').addClass('selected');
		}
		var tbl_datatable = $(this).closest(".datatable");
		selected_checkbox_length=tbl_datatable.DataTable().rows('.selected').data().length;
		if(selected_checkbox_length > 0)
		{
			if(tbl_datatable.parent().find(".select-info").length == 0 )
            {
            	tbl_datatable.parent().find('.dataTables_info').append('<span class="select-info"><span class="select-item">'+selected_checkbox_length+' row selected</span><span class="select-item"></span><span class="select-item"></span></span>');
            }
            else{
            	tbl_datatable.parent().find('.select-info span:nth-child(1)').html(selected_checkbox_length+' rows selected');
            }
			// alert( table.rows('.selected').data().length +' row(s) selected' );
		}
		else
		{
			tbl_datatable.parent().find(".select-info").remove();
		}
	});

	$(document).on('click','#serviceRequest.datatable #select-all, #company.datatable #select-all, #company_admin.datatable #select-all, #company_user.datatable #select-all, #customer.datatable #select-all, #assign_product.datatable #select-all, #assign_part.datatable #select-all, #service_center_admin.datatable #select-all, #technician.datatable #select-all',function (){
		// service request check multiple checkbox work
		var selected = $(this).is(':checked');
        $(this).closest('table.datatable, table.ajaxTable').find('td:first-child').each(function () {
            if (selected != $(this).closest('tr').hasClass('selected')) {
                $(this).click();
            }
        });
	});

	// filter + and - icon set on collapse in/out
	// Add minus icon for collapse element which is open by default
    $(".collapse.in").each(function(){
    	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
    });
    
    // Toggle plus minus icon on show hide of collapse element
    $(".collapse").on('show.bs.collapse', function(){
    	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    }).on('hide.bs.collapse', function(){
    	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
	});
	
	$(document).on('show.bs.collapse',".collapse", function(){
    	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
	})
	$(document).on('hide.bs.collapse',".collapse", function(){
    	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });

    $("#company-modal").on('shown.bs.modal', function(){
    	// quick add company from add/edit service request
	    $("#company-modal").find(".select2").select2();
	});
	$("#company-modal").on('hidden.bs.modal', function() { 
		// quick add company model close from add/edit service request
	    $("#company-modal").find('.alert-danger').html('').hide();
        $("#company-modal").find("form")[0].reset();
        $("#company_status option:selected").prop("selected", false);
		$("#company_status option:first").prop("selected", "selected");
	});
	$("#company-modal").find("form").on('submit', function (e) {

		$("#company-modal").find('.alert-danger').hide().html('');
		$("#company-modal").find('.message').html('');
		// $("#company_id").html('');

		// add company on click of save button
		e.preventDefault();
	    if ($("#company-modal").find("form")[0].checkValidity()) {
	        
	        var form=$("#company-modal").find("form");
			$.ajax({
		       	type:'POST',
		       	url:form.attr("action"),
	        	data:form.serialize(),
		       	dataType: "json",
		       	success:function(data) {
		       		if(data.success)
		       		{
		       			// alert(data.message);
		       			// $('#company-modal').modal('hide');
		       			$("#company_id").html(data.companyOptions);
						// $("#company_id").trigger('change');
						   
		       			if($("#company_id option[value='"+data.last_inserted_company_id+"']").length != 0){
			       			$("#company_id").val(data.last_inserted_company_id).trigger('change');
						}
						var alertBox = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Company created successfully!</div>';
						
						$("#company-modal").find('.message').html(alertBox);
						setTimeout(function() {$('#company-modal').modal('hide');}, 2000);
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#company-modal").find('.alert-danger').show();
                  			$("#company-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
				},
				error: function(xhr, ajaxOptions, thrownError) {

					var alertBox = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText +'</div>';
							
					$("#company-modal").find('.message').html(alertBox);
					$('#company-modal').modal('show');
					// alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					return false;
				}
				   
		    });
	    }
	    return false;
	});

	$("#customer-modal").on('shown.bs.modal', function(){
    	// quick add customer from add/edit service request
	    $("#customer-modal").find(".select2").select2();
	    $("#customer-modal").find("#customer_company_id").val($("#company_id").val()).trigger('change');
	});
	$("#customer-modal").on('hidden.bs.modal', function() { 
		// quick add customer model close from add/edit service request
	    $("#customer-modal").find('.alert-danger').html('').hide();
        $("#customer-modal").find("form")[0].reset();
        $("#customer_status option:selected").prop("selected", false);
		$("#customer_status option:first").prop("selected", "selected");
	});
	$("#customer-modal").find("form").on('submit', function (e) {

		$("#customer-modal").find('.message').html('');
		$("#customer-modal").find('.alert-danger').hide().html('');
		$(".custAddress").html('');

		// add company on click of save button
		e.preventDefault();
	    if ($("#customer-modal").find("form")[0].checkValidity()) {
	        
	        var form=$("#customer-modal").find("form");
			$.ajax({
		       	type:'POST',
		       	url:form.attr("action"),
	        	data:form.serialize(),
		       	dataType: "json",
		       	success:function(data) {
					console.log(data);
					
		       		if(data.success)
		       		{
		       			// alert(data.message);
		       			// $('#customer-modal').modal('hide');
		    // // //    			if($("#loggedUser_role_id").val() == ADMIN_ROLE_ID || $("#loggedUser_role_id").val() == SUPER_ADMIN_ROLE_ID)
			// // 			// {
		    //    				$("#company_id").trigger('change');
			// //    			// }

						var company_id = $("#company_id").val();
						// alert(company_id);

						var selected = "";
						if(company_id == data.last_inserted_company_id){

							selected = "selected";

							$('select#customer_id').append('<option '+selected+' value="'+data.last_inserted_customer_id+'" >'+data.last_inserted_customer_name+'</option>');

							$(".custAddress").html(data.last_inserted_customer_address);
	       					getSuggestedServiceCenter(data.last_inserted_customer_id);
						}
						var alertBox = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Customer created successfully!</div>';
						
						$("#customer-modal").find('.message').html(alertBox);
						setTimeout(function() {$('#customer-modal').modal('hide');}, 2000);
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#customer-modal").find('.alert-danger').show();
                  			$("#customer-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					
					var alertBox = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText +'</div>';
							
					$("#customer-modal").find('.message').html(alertBox);
					$('#customer-modal').modal('show');
					// alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					return false;
				}
				   
		    });
	    }
	    return false;
	});
	$("#service-center-modal").on('shown.bs.modal', function(){
    	// quick add service cneter from add/edit service request
	    $("#service-center-modal").find(".select2").select2();
	    // $("#service-center-modal").find("#customer_company_id").val($("#company_id").val()).trigger('change');
	});
	$("#service-center-modal").on('hidden.bs.modal', function() { 
		// quick add service center model close from add/edit service request
	    $("#service-center-modal").find('.alert-danger').html('').hide();
        $("#service-center-modal").find("form")[0].reset();
        $("#service_center_status option:selected").prop("selected", false);
		$("#service_center_status option:first").prop("selected", "selected");
	});
	$("#service-center-modal").find("form").on('submit', function (e) {

		$("#service-center-modal").find('.message').html('');
		$("#service-center-modal").find('.alert-danger').hide().html('');
		// $("#service_center_id").html('');

		// add service center on click of save button
		e.preventDefault();
	    if ($("#service-center-modal").find("form")[0].checkValidity()) {
	        
	        var form=$("#service-center-modal").find("form");
			$.ajax({
		       	type:'POST',
		       	url:form.attr("action"),
	        	data:form.serialize(),
		       	dataType: "json",
		       	success:function(data) {
		       		if(data.success)
		       		{
		       			// alert(data.message);
		       			// $('#service-center-modal').modal('hide');
						// $("#service_center_id").html(data.serviceCenterOptions).trigger("change");
						$("#service_center_id").html(data.serviceCenterOptions);

						$("#customer_id").select2().trigger("change");

						if($("#service_center_id option[value='"+data.last_inserted_service_center_id+"']").length != 0){

							$("#service_center_id").val(data.last_inserted_service_center_id).trigger('change');
						}
						var alertBox = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Service Center created successfully!</div>';
						
						$("#service-center-modal").find('.message').html(alertBox);
						setTimeout(function() {$('#service-center-modal').modal('hide');}, 2000);
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#service-center-modal").find('.alert-danger').show();
                  			$("#service-center-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					
					var alertBox = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText +'</div>';
												
					$("#service-center-modal").find('.message').html(alertBox);
					$('#service-center-modal').modal('show');
					// alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					return false;
				}
		    });
	    }
	    return false;
	});
	$("#technician-modal").on('shown.bs.modal', function(){
    	// quick add technician from add/edit service request
	    $("#technician-modal").find(".select2").select2();
	    $("#technician-modal").find("#userServiceCenter").val($("#service_center_id").val()).trigger('change');
	    // $("#service-center-modal").find("#customer_company_id").val($("#company_id").val()).trigger('change');
	});
	$("#technician-modal").on('hidden.bs.modal', function() { 
		// quick add technician model close from add/edit service request
	    $("#technician-modal").find('.alert-danger').html('').hide();
        $("#technician-modal").find("form")[0].reset();
        $("#technician_status option:selected").prop("selected", false);
		$("#technician_status option:first").prop("selected", "selected");
	});
	$("#technician-modal").find("form").on('submit', function (e) {

		$("#technician-modal").find('.message').html('');
		$("#technician-modal").find('.alert-danger').hide().html('');

		// add technician on click of save button
		e.preventDefault();
	    if ($("#technician-modal").find("form")[0].checkValidity()) {
	        
	        var form=$("#technician-modal").find("form");
			$.ajax({
		       	type:'POST',
		       	url:form.attr("action"),
	        	data:form.serialize(),
		       	dataType: "json",
		       	success:function(data) {
		       		if(data.success)
		       		{
						// $('#technician-modal').modal('hide');
						// $("#service_center_id").trigger("change");
						var service_centerId = $("#service_center_id").val();

						var selected = "";
						if(service_centerId == data.last_inserted_serviceCenter_id){

							selected = "selected";

							$('select#technician_id').append('<option '+selected+' value="'+data.last_inserted_technician_id+'" >'+data.last_inserted_technician_name+'</option>');
						}
						// var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';

						var alertBox = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Technician created successfully!</div>';
						
						$("#technician-modal").find('.message').html(alertBox);
						setTimeout(function() {$('#technician-modal').modal('hide');}, 2000);
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#technician-modal").find('.alert-danger').show();
                  			$("#technician-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
				},
				error: function(xhr, ajaxOptions, thrownError) {

					var alertBox = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText +'</div>';
						
					$("#technician-modal").find('.message').html(alertBox);
					$('#technician-modal').modal('show');
					// alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					return false;
				}
				   
		    });
	    }
	    return false;
	});
});
function getTechnicians(serviceCenterId) {
	$.ajax({
       	type:'GET',
       	url:APP_URL+'/admin/getTechnicians',
       	data:{
       		'serviceCenterId':serviceCenterId
       	},
       	dataType: "json",
       	success:function(data) {
       		$(".techDiv").show();
			$(".techDiv").find(".select2").select2();
			// $("#technician_id").attr('required','required');
       		$("#technician_id").html(data.options);
       	}
    });
	    getTransporationCharge();
}
function getSuggestedServiceCenter(customerId) {
	$.ajax({
	       	type:'GET',
	       	url:APP_URL+'/admin/getSuggestedServiceCenter',
	       	data:{
	       		'customerId':customerId
	       	},
	       	dataType: "json",
	       	success:function(data) {

	       		if($("#loggedUser_role_id").val() == ADMIN_ROLE_ID || $("#loggedUser_role_id").val() == SUPER_ADMIN_ROLE_ID)
				{
					suggestedServiceCenters = "";
		       		if(data.service_centers.length > 0)
		       		{
		       			$(".suggestedServiceCenterDiv").show();
		                $.each(data.service_centers, function(key, value) {
		                        suggestedServiceCenters+='<div><input type="radio" name="suggested_service_center" value="'+ value.id +'"><label class="control-label lblSuggestedCenter fontweight">'+value.name+'</label></div>';
		                });
		       		}
		       		else
		       		{
		       			$(".suggestedServiceCenterDiv").hide();
		       		}
		       		$("#suggestedHTML").html(suggestedServiceCenters);	
				}
	       		
	       		getTransporationCharge();
	       	}
	    });
}
function requestCharge(ele) {

	// display charges for service and installation in service request
	var companyId=$("#company_id").val();
	var serviceType=$("#service_type").val();
	var productId=$("#product_id").val();
	
	if(serviceType == "installation")
	{
		$(".installationChargeDiv").show();
		$(".serviceChargeDiv").hide();

		// $(".partsDiv").css('display','none');
		$(".partsDiv").hide();
		// if(companyId == "")
		// {
		// 	return false;
		// }
	}
	else
	{
		$(".installationChargeDiv").hide();
	    $(".serviceChargeDiv").show();

		$(".partsDiv").css('display','block');
		$(".partsDiv").show();
		$(".partsDiv").find(".select2").select2();
		// if(productId == "")
		// {
		// 	return false;
		// }
	}

	// if(companyId || productId)
	// {
		$.ajax({
	       	type:'GET',
	       	url:APP_URL+'/admin/getCharge',
	       	data:{
	       		'companyId':companyId,
	       		'serviceType':serviceType,
	       		'productId':productId
	       	},
	       	dataType: "json",
	       	success:function(data) {

	       		$("#installation_charge").val(data.installation_charge);
	       		
	       		$("#lbl_installation_charge").html('<i class="fa fa-rupee"></i>'+(parseFloat(data.installation_charge)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
	       		 
	       		$("#service_charge").val(data.service_charge);

	       		$("#lbl_service_charge").html('<i class="fa fa-rupee"></i>'+(parseFloat(data.service_charge)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') );

				var additional_amount=isNaN(parseFloat($("#additional_charges").val()))?0:parseFloat($("#additional_charges").val());
				   
				var pre_additional_charge = 0;
				$('.existingAdditional_charge').each(function() {
					pre_additional_charge+= isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val());
				});

	       		var km_charge=isNaN(parseFloat($("#km_charge").val()))?0:parseFloat($("#km_charge").val());
				   var km_distance=isNaN(parseFloat($("#km_distance").val()))?0:parseFloat($("#km_distance").val());
				   
				var transportation_charge = isNaN(parseFloat($("#transportation_charge").val()))?0:parseFloat($("#transportation_charge").val());

	          	var total_amount=(parseFloat(data.installation_charge)+parseFloat(data.service_charge)+additional_amount+ transportation_charge+pre_additional_charge).toFixed(2);
	          	$("#amount").val(total_amount);
	          	$("#lbl_total_amount").html('<i class="fa fa-rupee"></i>'+(parseFloat(total_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

	          	$("#status").html(data.statusOptions);
	       	}
	    });
	// }
	
}
function totalServiceAmount() {
	// called on keyup of additional amount in service request

	var pre_additional_charge = 0;
	$('.existingAdditional_charge').each(function() {
		console.log($(this).val());
		pre_additional_charge+= isNaN(parseFloat($(this).val()))?0:parseFloat($(this).val());
	});
	
	var installation_charge=isNaN(parseFloat($("#installation_charge").val()))?0:parseFloat($("#installation_charge").val());
	var service_charge=isNaN(parseFloat($("#service_charge").val()))?0:parseFloat($("#service_charge").val());

	var additional_amount=isNaN(parseFloat($("#additional_charges").val()))?0:parseFloat($("#additional_charges").val());
	var km_charge=isNaN(parseFloat($("#km_charge").val()))?0:parseFloat($("#km_charge").val());
	var km_distance=isNaN(parseFloat($("#km_distance").val()))?0:parseFloat($("#km_distance").val());
	
	// if($("#loggedUser_role_id").val() == ADMIN_ROLE_ID)
	// {

	// }
	var transportation_charge = isNaN(parseFloat($("#transportation_charge").val()))?0:parseFloat($("#transportation_charge").val());
	// var total_amount=(installation_charge+service_charge+additional_amount+(km_distance * km_charge)).toFixed(2);
	var total_amount=(installation_charge+service_charge+additional_amount+transportation_charge+pre_additional_charge).toFixed(2);
  	
  	$("#amount").val(total_amount);
  	$("#lbl_total_amount").html('<i class="fa fa-rupee"></i>'+(parseFloat(total_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
}

function checkIsDecimalNumber(ele, evt) {
	// check amount field to allow number and single decimal point
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    
    if (charCode == 46) {
        //Check if the text already contains the . character
        if (ele.value.indexOf('.') === -1) {
        	return true;
        } else {
            return false;
        }
    } else {
        if (charCode > 31
             && (charCode < 48 || charCode > 57))
        {
        	// check if text values is character
        	return false;
        }
            
    }
    return true;
}
function allowNumberWithComma(ele, evt) {
	// check number field to allow number and comma
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    
    if (charCode == 44) {
        //Check if the value is comma(,)
        return true;
    } else {
        if (charCode > 31
             && (charCode < 48 || charCode > 57))
        {
        	// check if text values is character
        	return false;
        }
            
    }
    return true;
}

function getTransporationCharge() {

	var customerId = $("#customer_id").val();

	var serviceCenterId = $("#service_center_id").val();

	if(typeof serviceCenterId === "undefined")
	{
		return false;
	}
	if(serviceCenterId == "")
	{
		if(typeof $("input[name='suggested_service_center']:checked").val() !== "undefined")
		{
			serviceCenterId=$("input[name='suggested_service_center']:checked").val();
		}
	}
	$("#transportation_charge").val('');
	$("#km_distance").val('');
	$("#km_charge").val('');
	$("#lbl_trans_amount").html('');

	if(customerId != "" && serviceCenterId != "")
	{

		$.ajax({
		       	type:'GET',
		       	url:APP_URL+'/admin/getTransporationCharge',
		       	data:{
		       		'customerId':customerId,
		       		'serviceCenterId' : serviceCenterId
		       	},
		       	dataType: "json",
		       	success:function(data) {
					
		       		if(!data.supported)
		       		{
		       			if($("#loggedUser_role_id").val() != ADMIN_ROLE_ID && $("#loggedUser_role_id").val() != SUPER_ADMIN_ROLE_ID)
						{
							$("#lbl_trans_amount").html((parseFloat(data.transportation_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
						}
						$("#transportation_charge").val(data.transportation_amount);
						$("#km_distance").val(data.km_distance);
						$("#km_charge").val(data.km_charge);
						$(".transportationDiv").show();

						totalServiceAmount();
		       		}
		       		else
		       		{
		       			$(".transportationDiv").hide();
		       		}
		       		
		       	}
	    });
	}
	else
	{
		$(".transportationDiv").hide();
	}
}

function getAssignedProducts(ele) {
	
	var companyId = $(ele).val();
	// alert(companyId);
	if(companyId != "")
	{

		$.ajax({
		       	type:'GET',
		       	url:APP_URL+'/admin/getAssignedProductsAjax',
		       	data:{
		       		'companyId':companyId
		       	},
		       	dataType: "json",
		       	success:function(data) {

		       		if(data.selectedProductOptions.length > 0)
		       		{
		       			$("#selectall-product_id").val(data.selectedProductOptions).trigger('change');
		       		}		       		
		       	}
	    });
	}
	else
	{
		$(".transportationDiv").hide();
	}
}

$("#call_type").change(function(){
	var selectedCallType = $(this).children("option:selected").val();
	var serialNumber = $('#onlineSerialNumber');
	var warrantyNumber = $('#warrantyCardNumber');
	// alert(selectedCallType);
	if(selectedCallType == 'Warranty'){
		$('.warrantycardnumber').css('display','block');
		$('.onlineserialnumber').css('display','block');
		serialNumber.attr('required', true);
		warrantyNumber.attr('required', true);
	}else{
		warrantyNumber.attr('required', false);
		serialNumber.attr('required', false);
		$('.warrantycardnumber').css('display','none');
		$('.onlineserialnumber').css('display','none');
	}
});

function quickadd(type){
	$('#renderCompanyHtml').html('');
	$('#renderCustomerHtml').html('');
	$('#renderServiceCenterHtml').html('');
	$('#renderTechnicianHtml').html('');
	if(type != ''){
		$.ajax({
			type:'GET',
			url:APP_URL+"/admin/quickadd",
			data:{
				'type':type,
				// '_token': '{{csrf_token()}}'
			},
			dataType: "json",
			success:function(data) {
				if(data.success == 1){
					if(type == 'company'){
						$('#renderCompanyHtml').html(data.html);
						$('#company-modal').modal('show');
					}else if(type == 'customer'){
						$('#renderCustomerHtml').html(data.html);
						$('#customer-modal').modal('show');
					}else if(type == 'service_center'){
						$('#renderServiceCenterHtml').html(data.html);
						$('#service-center-modal').modal('show');
					}else if(type == 'technician'){
						$('#renderTechnicianHtml').html(data.html);
						$('#technician-modal').modal('show');
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				return false;
			}
		});
	}
}

$("#assign-products-modal").on('hidden.bs.modal', function() { 
	// quick add technician model close from add/edit service request
	$("#assign-products-modal").find('.alert-danger').html('').hide();
	$("#assign-products-modal").find("form")[0].reset();
	$("#assign_company_id option:selected").prop("selected", false);
	$("#assign_company_id option:first").prop("selected", "selected");
});
function AssignProducts(company_id) {
	$('#renderAssignProductsHtml').html('');
	if(company_id != ''){
		$.ajax({
			type:'GET',
			url:APP_URL+"/admin/ajax_assign_products",
			data:{
				'company_id':company_id
			},
			dataType: "json",
			success:function(data) {
				
				if(data.success == 1){

					if(data.company_id == ''){
						$('#assign_company_id').val('').trigger('change');
					}else{
						$('#assign_company_id').val(data.company_id).trigger('change');
					}
					
					$('#renderAssignProductsHtml').html(data.html);
					$(".select2").select2();	
					$('#assign-products-modal').modal('show');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				return false;
			}
		});
	}
}
$("#assign-products-modal").find("form").on('submit', function (e) {

	$("#assign-products-modal").find('.alert-danger').hide().html('');
	$("#assign-products-modal").find('.message').html('');

	e.preventDefault();

	if ($("#assign-products-modal").find("form")[0].checkValidity()) {
		
		var form=$("#assign-products-modal").find("form");
			$.ajax({
			   	type:'POST',
			   	url:form.attr("action"),
				data:form.serialize(),
			   	dataType: "json",
			   	success:function(data) {
				   	// console.log(data);
				   	if(data.success)
				   	{
						var company_id = $("#company_id").val();
						// console.log(company_id);
						if(company_id == data.last_inserted_company_id){
							$("#company_id").val(data.last_inserted_company_id).trigger('change');
						}
						var alertBox = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Assign Products added successfully!</div>';
						
						$("#assign-products-modal").find('.message').html(alertBox);
						setTimeout(function() {$('#assign-products-modal').modal('hide');}, 2000);
				   	}
					else
					{
						$.each(data.errors, function(key, value){
							$("#assign-products-modal").find('.alert-danger').show();
							$("#assign-products-modal").find('.alert-danger').append('<p>'+value+'</p>');
						});
					}
			},
			error: function(xhr, ajaxOptions, thrownError) {

				var alertBox = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+ thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText +'</div>';
						
				$("#assign-products-modal").find('.message').html(alertBox);
				$('#assign-products-modal').modal('show');
				return false;
			}
			   
		});
	}
	return false;
});

/* function saveButton(button) {

	var loggedUserId  = $("#loggedUser_role_id").val();

	console.log(COMPANY_ADMIN_ROLE_ID);
	console.log(SERVICE_ADMIN_ROLE_ID);
	console.log(TECHNICIAN_ROLE_ID);
	console.log(COMPANY_USER_ROLE_ID);
	console.log(ADMIN_ROLE_ID);
	console.log(SUPER_ADMIN_ROLE_ID);
	console.log(loggedUserId);

	var  elementId = event.target.id;
	$('#'+elementId).attr('disabled', 'disabled');
	var error = 0;
	// if (email.val() == '') {
	// 	email.addClass('custom_error');
	// 	email.next().text("Please enter email");
	// 	error = 1;
	// }
	// if (email.val() != '') {
	// 	var atpos = email.val().indexOf("@");
	// 	var dotpos = email.val().lastIndexOf(".");
	// 	if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.val().length) {
	// 		email.addClass('custom_error');
	// 		email.next().text("Please enter valid email address");
	// 		error = 1;
	// 	} else {
	// 		ulname.addClass('custom_valid');
	// 		ulname.next().text("");
	// 	}
	// } 

	if(button == 'forgotpassword'){
		
		var currentPassword = $('#current_password').val();
		var newPassword = $('#new_password').val();
		var newConfirmPassword = $('#new_password_confirmation').val();

		if ( currentPassword == '') {
			$('#current_password').next().text("Please enter Current Password");
			error = 1;
		} else {
			$('#current_password').next().text("");
			error = 0;
		}

		if ( newPassword == '') {
			$('#new_password').next().text("Please enter New Password");
			error = 1;
		} else {
			$('#new_password').next().text("");
			error = 0;
		}

		if ( newConfirmPassword == '') {
			$('#new_password_confirmation').next().text("Please enter New Confirm Password");
			error = 1;
		} else {
			$('#new_password_confirmation').next().text("");
			error = 0;
		}
	}else if(button == 'formServiceRequest'){

		if(loggedUserId != '' && loggedUserId != 'undefined'){
			if(loggedUserId == ADMIN_ROLE_ID || loggedUserId == SUPER_ADMIN_ROLE_ID){

				var comapnyName = $('#company_id').val();
				var customerName = $('#customer_id').val();

				if ( comapnyName == '') {
					$('#company_id').next().text("Please select Company");
					error = 1;
				} else {
					$('#company_id').next().text("");
					error = 0;
				}

				if ( customerName == '') {
					$('#customer_id').next().text("Please select Customer");
					error = 1;
				} else {
					$('#customer_id').next().text("");
					error = 0;
				}
			}
	
			if(loggedUserId == COMPANY_ADMIN_ROLE_ID || loggedUserId == COMPANY_USER_ROLE_ID){

				var customerName = $('#customer_id').val();
				if ( customerName == '') {
					$('#customer_id').next().text("Please select Customer");
					error = 1;
				} else {
					$('#customer_id').next().text("");
					error = 0;
				}
			}

			if(loggedUserId == SUPER_ADMIN_ROLE_ID || loggedUserId == ADMIN_ROLE_ID || loggedUserId == SERVICE_ADMIN_ROLE_ID || loggedUserId == TECHNICIAN_ROLE_ID){

				var serviceCenterName = $('#service_center_id').val();
				var technicianName = $('#technician_id').val();

				if ( serviceCenterName == '') {
					$('#service_center_id').next().text("Please select Service Center");
					error = 1;
				} else {
					$('#service_center_id').next().text("");
					error = 0;
				}

				if ( technicianName == '') {
					$('#technician_id').next().text("Please select Technician");
					error = 1;
				} else {
					$('#technician_id').next().text("");
					error = 0;
				}
			}

			if(loggedUserId != COMPANY_ADMIN_ROLE_ID && loggedUserId != COMPANY_USER_ROLE_ID){
				completion_date
				var complitionDate = $('#completion_date').val();

				if ( complitionDate == '') {
					$('#completion_date').next().text("Please select Complition Date");
					error = 1;
				} else {
					$('#completion_date').next().text("");
					error = 0;
				}
			}
		}
		var serviceType = $('#service_type').val();
		var productName = $('#product_id').val();

		if ( serviceType == '') {
			$('#service_type').next().text("Please select Service Type");
			error = 1;
		} else {
			$('#service_type').next().text("");
			error = 0;
		}

		if ( productName == '') {
			$('#product_id').next().text("Please select Product");
			error = 1;
		} else {
			$('#product_id').next().text("");
			error = 0;
		}
		
		// if(serviceType == 'repair'){
		// 	var productParts = $('#selectall-parts').val();

		// 	if ( productParts == '') {
		// 		$('#selectall-parts').next().text("Please select Product Part");
		// 		error = 1;
		// 	} else {
		// 		$('#selectall-parts').next().text("");
		// 		error = 0;
		// 	}
		// }

		var callType = $('#call_type').val();
		var callLocation = $('#call_location').val();
		var priority = $('#priority').val();

		if ( callType == '') {
			$('#call_type').next().text("Please select Call type");
			error = 1;
		} else {
			$('#call_type').next().text("");
			error = 0;
		}

		if(callType == 'Warranty'){

			var onlineSerialNumber = $('#onlineSerialNumber').val();
			var warrantyCardNumber = $('#warrantyCardNumber').val();
			
			if ( onlineSerialNumber == '') {
				$('#onlineSerialNumber').next().text("Please add Online Serial Number");
				error = 1;
			} else {
				$('#onlineSerialNumber').next().text("");
				error = 0;
			}
	
			if ( warrantyCardNumber == '') {
				$('#warrantyCardNumber').next().text("Please select Warranty Card Number");
				error = 1;
			} else {
				$('#warrantyCardNumber').next().text("");
				error = 0;
			}
		}
		if ( callLocation == '') {
			$('#call_location').next().text("Please select Product Part");
			error = 1;
		} else {
			$('#call_location').next().text("");
			error = 0;
		}

		if ( priority == '') {
			$('#priority').next().text("Please select Priority");
			error = 1;
		} else {
			$('#priority').next().text("");
			error = 0;
		}
	}
	if (error > 0) { 
		console.log("if");
		console.log('elementId');
		console.log(elementId);
		console.log('error');
		console.log(error);
		$('#'+elementId).removeAttr('disabled', 'disabled');
		// $(this).find("input[type='submit']").removeAttr("readonly");
		event.preventDefault();
	}else{
		
		$('#'+elementId).attr('disabled', 'disabled');
		console.log('elementId');
		console.log(elementId);
		var formId = $('#'+elementId).closest("form").attr('id');
		console.log('formId');
		console.log(formId);
		// $('#'+formId).submit();
	}
}*/
function saveButton() {
	// var  elementId = event.target.id;
	var buttonId = $(document.activeElement).attr('id');
	allowdisable = true;

	$( "input" ).each(function() {
		if(this.hasAttribute('required')){
			// if ( $(this).css('display') === 'none')
			if ( $(this).is(":visible")){
				return true;
			}else{
				if($(this).val() == ""){
					allowdisable = false;
				}
			}
		}
	});

	$( "select" ).each(function(index,value) {
		if(this.hasAttribute('required')){

			if ($(this).is(":visible")){
				// $(this).attr('required','required');
				return true;
			}else{
				if($(this).val() == ""){
					allowdisable = false;
				}
			}
		}
	});

	if(allowdisable ==  true){

		$('.multiple_Additional_charge_for').each(function() {
			var selectedid = $(this).attr("id").split('-');
			
			$(".error-block_"+selectedid[1]).text('');
			$(".error-amount-block_"+selectedid[1]).text('');
			
			var selectedOption =$("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val();
			var selectedAmount =$("#existingAdditional_charge_"+selectedid[1]).val();
			console.log('selectedAmount');
			console.log(selectedAmount);
			console.log('===============');
			if(typeof(selectedOption) !== 'undefined' && typeof(selectedAmount) !== 'undefined'){
				// console.log('in ');
				if($("#existingAdditional_charge_"+selectedid[1]).val().trim() != '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() <= 0){
					// console.log('in if ');
					$(".error-amount-block_"+selectedid[1]).text('The additional amount must be greater than 0.');
					allowdisable = false;
	
				}else if($("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val() == '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() != ''){
					// console.log('in else if 1 ');
					$(".error-block_"+selectedid[1]).text('Please select option');
					allowdisable = false;
				}else if($("#Additional_charge_for_existing-"+selectedid[1]+"  option:selected").val() != '' && $("#existingAdditional_charge_"+selectedid[1]).val().trim() == ''){
					// console.log('in else if 2 ');
					$(".error-amount-block_"+selectedid[1]).text('Please Enter amount');
					allowdisable = false;
				}
			}
		});

		$("#additional_charges_title").next(".help-block").html("");
		$(".addamountError").html("");
		var valueAdditionalCharge = $("#additional_charges").val();
		var valueAdditionalChargeTitle = $("#additional_charges_title").val();
		
		if(typeof(valueAdditionalCharge) !== 'undefined' && typeof(valueAdditionalChargeTitle) !== 'undefined'){

			if($("#additional_charges").val().trim() != "" && $("#additional_charges").val() <= 0)
			{
				$(".addamountError").html("The additional amount must be greater than 0.");
				allowdisable = false;
			}
			else if($("#additional_charges_title").val().trim() != "" && $("#additional_charges").val().trim() == "")
			{
				$(".addamountError").html("The additional amount field is required when additional charges title is present.");
				allowdisable = false;
			}
			else if($("#additional_charges_title").val().trim() == "" && $("#additional_charges").val().trim() != "")
			{
				$("#additional_charges_title").next(".help-block").html("The additional charges title field is required when additional amount is present.");
				allowdisable = false;
			}
		}
		// event.preventDefault();	
		// Check additional charge
		// var multiAdditionalCharge = $('.multiple_Additional_charge_for').val();
		// var existingAdditionalCharge = $('.existingAdditional_charge').val();
		
		// console.log('existingAdditionalCharge');
		// console.log(existingAdditionalCharge);
		// console.log('=============');
		
		// if(((multiAdditionalCharge != '' && multiAdditionalCharge <= 0) && (existingAdditionalCharge == '' && existingAdditionalCharge < 0)) || ((multiAdditionalCharge == '' && multiAdditionalCharge == 0) && existingAdditionalCharge != '')){
		// 	allowdisable =  false;
		// }
		
		

		// Check other addtional charge
		// var OtherAdditionalCharge = $('#additional_charges_title').val();
		// var AdditionalCharge = $('#additional_charges').val();

		// $('.multiple_Additional_charge_for').each(function() {
		// 	var selectedid = $(this).attr("id").split('-');
		// 	console.log(selectedid);
			
		// });
		
		// if((OtherAdditionalCharge == '' && AdditionalCharge != '') || (OtherAdditionalCharge != '' && AdditionalCharge == '')){
		// 	allowdisable =  false;
		// }	

		//Check for conditional required
		if(allowdisable ==  true){
			
			$('#'+buttonId).attr('disabled', 'disabled');
			return true;
		}else{
			
			event.preventDefault();	
			$('#'+buttonId).removeAttr('disabled', 'disabled');
			return false;
		}
		
	}else{
		event.preventDefault();	
		$('#'+buttonId).removeAttr('disabled', 'disabled');
		return false;
	}
}
