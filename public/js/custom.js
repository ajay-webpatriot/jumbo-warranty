
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
	       		$(".custDiv").show();
	       		$(".custDiv").find(".select2").select2();
	       		$("#customer_id").html(data.custOptions);
	       		$("#selectall-parts").html(data.partOptions);
	       		$("#product_id").html(data.productOptions);
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
	$(document).on('submit','#formServiceRequest',function (){

		// validate additional charges title and amount on form submit in add/edit service request page
		var status = true;
		$("#additional_charges_title").next(".help-block").html("");
		$("#additional_charges").next(".help-block").html("");

		if($("#additional_charges").val().trim() != "" && $("#additional_charges").val() <= 0)
        {
        	$("#additional_charges").next(".help-block").html("The additional charges must be greater than 0.");
			status = false;
        }
		else if($("#additional_charges_title").val().trim() != "" && $("#additional_charges").val().trim() == "")
		{
			$("#additional_charges").next(".help-block").html("The additional amount field is required when additional charges title is present.");
			status = false;
		}
		else if($("#additional_charges_title").val().trim() == "" && $("#additional_charges").val().trim() != "")
        {
        	$("#additional_charges_title").next(".help-block").html("The additional charges title field is required when additional amount is present.");
        	status = false;
        }
        
        
        return status;
	});
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
		       			alert(data.message);
		       			$('#company-modal').modal('hide');
		       			$("#company_id").html(data.companyOptions);
		       			// if($("#company_id option[value='"+data.selectedCompany+"']").length != 0){
			       		// 	$("#company_id").val(data.selectedCompany).trigger('change');
		       			// }
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#company-modal").find('.alert-danger').show();
                  			$("#company-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
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
		       		if(data.success)
		       		{
		       			alert(data.message);
		       			$('#customer-modal').modal('hide');
		       			$("#company_id").trigger('change');
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#customer-modal").find('.alert-danger').show();
                  			$("#customer-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
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
		       			alert(data.message);
		       			$('#service-center-modal').modal('hide');
		       			$("#service_center_id").html(data.serviceCenterOptions).trigger("change");
		       			$("#customer_id").select2().trigger("change");
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#service-center-modal").find('.alert-danger').show();
                  			$("#service-center-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
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
		       			alert(data.message);
		       			$('#technician-modal').modal('hide');
		       			$("#service_center_id").trigger("change");
		       		}
		       		else
		       		{
		       			$.each(data.errors, function(key, value){
                  			$("#technician-modal").find('.alert-danger').show();
                  			$("#technician-modal").find('.alert-danger').append('<p>'+value+'</p>');
                  		});
		       		}
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
	       		
	       		$("#lbl_installation_charge").html((parseFloat(data.installation_charge)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
	       		 
	       		$("#service_charge").val(data.service_charge);

	       		$("#lbl_service_charge").html((parseFloat(data.service_charge)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') );

	       		var additional_amount=isNaN(parseFloat($("#additional_charges").val()))?0:parseFloat($("#additional_charges").val());
	       		var km_charge=isNaN(parseFloat($("#km_charge").val()))?0:parseFloat($("#km_charge").val());
	       		var km_distance=isNaN(parseFloat($("#km_distance").val()))?0:parseFloat($("#km_distance").val());

	          	var total_amount=(parseFloat(data.installation_charge)+parseFloat(data.service_charge)+additional_amount+(km_distance * km_charge)).toFixed(2);
	          	$("#amount").val(total_amount);
	          	$("#lbl_total_amount").html((parseFloat(total_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

	          	$("#status").html(data.statusOptions);
	       	}
	    });
	// }
	
}
function totalServiceAmount() {
	// called on keyup of additional amount in service request

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
	var total_amount=(installation_charge+service_charge+additional_amount+transportation_charge).toFixed(2);
  	
  	$("#amount").val(total_amount);
  	$("#lbl_total_amount").html((parseFloat(total_amount)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
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