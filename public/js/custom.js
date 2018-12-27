
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

});

// function requestCharge(ele) {
// 	$.ajax({
//        type:'GET',
//        url:'/getCharge',
//        data:{
//        		'companyId':'',
//        		'serviceType':''
//        },
//        success:function(data) {
//           $("#msg").html(data.msg);
//        }
//     });
// }

function requestCharge(ele) {

	var companyId=$("#company_id").val().trim();
	var serviceType=$("#service_type").val().trim();
	var productId=$("#product_id").val().trim();
	
	if(serviceType == "installation")
	{
		$(".installationChargeDiv").show();
		$(".serviceChargeDiv").hide();

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
	$(".rowpartsDiv").show();

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
	       		$("#service_charge").val(data.service_charge);
	          	
	       	}
	    });
	}
	
}