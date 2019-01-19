
$(document).ready(function(){

	$(document).on("keypress","input[name=phone]",function(evt)
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
		// get technician according to service center for service request 
		var serviceCenterId = $(this).val();

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
	       	}
	    });
	});
	$(document).on('submit','#formServiceRequest',function (){

		// validate additional charges title and amount on form submit in add/edit service request page
		var status = true;
		$("#additional_charges_title").next(".help-block").html("");
		$("#additional_charges").next(".help-block").html("");
		if($("#additional_charges_title").val().trim() != "" && $("#additional_charges").val().trim() == "")
		{
			$("#additional_charges").next(".help-block").html("The additional charges field is required when additional charges title is present.");
			status = false;
		}
		else if($("#additional_charges_title").val().trim() == "" && $("#additional_charges").val().trim() != "")
        {
        	$("#additional_charges_title").next(".help-block").html("The additional charges title field is required when additional charges is present.");
        	status = false;
        }
        
        return status;
	});
});
function requestCharge(ele) {

	// display charges for service and installation in service request
	var companyId=$("#company_id").val().trim();
	var serviceType=$("#service_type").val().trim();
	var productId=$("#product_id").val().trim();
	
	if(serviceType == "installation")
	{
		$(".installationChargeDiv").show();
		$(".serviceChargeDiv").hide();

		// $(".partsDiv").css('display','none');
		$(".partsDiv").hide();
		if(companyId == "")
		{
			return false;
		}
	}
	else
	{
		$(".installationChargeDiv").hide();
	    $(".serviceChargeDiv").show();

		$(".partsDiv").css('display','block');
		$(".partsDiv").show();
		$(".partsDiv").find(".select2").select2();
		if(productId == "")
		{
			return false;
		}
	}

	if(companyId || productId)
	{
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
	}
	
}
function totalServiceAmount() {
	// called on keyup of additional amount in service request

	var installation_charge=isNaN(parseFloat($("#installation_charge").val()))?0:parseFloat($("#installation_charge").val());
	var service_charge=isNaN(parseFloat($("#service_charge").val()))?0:parseFloat($("#service_charge").val());

	var additional_amount=isNaN(parseFloat($("#additional_charges").val()))?0:parseFloat($("#additional_charges").val());
	var km_charge=isNaN(parseFloat($("#km_charge").val()))?0:parseFloat($("#km_charge").val());
	var km_distance=isNaN(parseFloat($("#km_distance").val()))?0:parseFloat($("#km_distance").val());
	var total_amount=(installation_charge+service_charge+additional_amount+(km_distance * km_charge)).toFixed(2);
  	
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