$(document).ready(function(){
	 "use strict";
    
    $(".preloader").fadeOut();
    
	$("#loginform").validate({
		rules:{
			userPassword:{
				required: true,
				minlength:6,
				maxlength:25
			}
		},
		errorClass: "error",
		errorElement: "label"
	});
	
	$("#otpVerificationForm").validate({
		errorClass: "error",
		errorElement: "label"
	});

	$("#recoverform").validate({
		errorClass: "error",
		errorElement: "label"
	});
	
	$("#passwordRecoverForm").validate({
		rules:{
			userOtp:{
				required: true,
				numberonly: true,
				minlength:4,
				maxlength:4
			},
			userPassword:{
				required: true,
				minlength:6,
				maxlength:25
			},
			userConfPassword:{
				required: true,
				minlength:6,
				maxlength:25,
				equalTo: "#userPassword"
			}
		},
		errorClass: "error",
		errorElement: "label"
	});
	
	jQuery.validator.addMethod("numberonly", function(value, element) 
	{
		return this.optional(element) || /^[0-9]+$/i.test(value);
	}, "Number only please");
});