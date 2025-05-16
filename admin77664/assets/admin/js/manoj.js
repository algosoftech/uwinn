 // create cookies
function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else var expires = "";               
	document.cookie = name + "=" + value + expires + "; path=/";
}

 // read cookies
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length).replace(/%2F/gi,'/').replace(/\+/gi,' ').replace(/\%26%23xa3%3B/gi,'&#xa3;');
	}
	return null;
}

 // erase cookies
function eraseCookie(name) {
	createCookie(name, "", -1);
}

function alertMessageModelPopup(message,type){  
    $.notify({
        message: message
    }, {
        type: type,
        allow_dismiss: false,
        label: 'Cancel',
        className: 'btn-xs btn-inverse',
        placement: {
            from: 'top',
            align: 'right'
        },
        delay: 2500,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        },
        offset: {
            x: 30,
            y: 30
        }
    });
}

$(document).on('change','#Data_Form #showLength',function(){ 
	$('#Data_Form').submit();														 
});

$(document).on('keypress','#Data_Form #searchValue',function(e){ 
	if (e.keyCode == '13') {
        $('#Data_Form').submit();
    }																  
});

$(document).on('change','#Data_Form #showByCommittee',function(){ 
	$('#Data_Form').submit();														 
});

$(document).ready(function(){
	// Form Validation
	if($("#currentPageForm").length) { 
	    $("#currentPageForm").validate({
			rules:{
				new_password: { minlength: 6, maxlength: 25 },
				conf_password: { minlength: 6, equalTo: "#new_password" },
		        mobile_number:{ minlength:10, maxlength:15, numberandsign:true }
			},
			errorClass: "error",
			errorElement: "span"
		});
	}
});

//Profile page tabbing
$(document).on('click','.card .nav-tabs .nav-item a',function(){
	$(this).parent('.nav-item').siblings().find('.nav-link').removeClass('active');
	$(this).addClass('active');
});

///////////			VIEW DETAILS MODEL 		///////////////////
$(document).on('click','.table .view-details-data',function(){ 
	var title		=	$(this).attr('title');
	$("#myViewDetailsModal").modal();
	$("#myViewDetailsModal .modal-header h4.modal-title").html(title);
	var viewid		=	$(this).attr('data-id');
	var modelwidth	=	$(this).attr('data-width');
	if(modelwidth){
		$("#myViewDetailsModal .modal-dialog").css('width',modelwidth);
	}
	$.ajax({
		type: 'post',
		 url: FULLSITEURL+CURRENTCLASS+'/get_view_data',
		data: {viewid:viewid},
	 success: function(response){
	 		$("#myViewDetailsModal .modal-body").html(response);
	 	}	
	});
});
///////////			VIEW SUB DETAILS MODEL 		///////////////////
$(document).on('click','a.view-sub-details-data',function(){ 
	var title		=	$(this).attr('title');
	$("#myViewSubDetailsModal").modal();
	$("#myViewSubDetailsModal .modal-header h4.modal-title").html(title);
	var viewid		=	$(this).attr('data-id');
	var modelwidth	=	$(this).attr('data-width');
	if(modelwidth){
		$("#myViewSubDetailsModal .modal-dialog").css('width',modelwidth);
	}
	$.ajax({
		type: 'post',
		 url: FULLSITEURL+CURRENTCLASS+'/get_view_sub_data',
		data: {viewid:viewid},
	 success: function(response){
	 		$("#myViewSubDetailsModal .modal-body").html(response);
	 	}	
	});
});

//////////////////////////////////   Get State Data Through Ajax
function get_state_data(curobj,countryid,stateid,particularid)
{ alert(countryid);
   if(countryid !=''){  
    $.ajax({
      type: 'post',
      url: FULLSITEURL+'astrologers/astrologers/get_state_data',
      data: {csrf_api_key:csrf_api_value,classid:classid,sectionid:sectionid},
     success: function(zresponse){ 
		 if(particularid == '0'){
			curobj.closest('.class-parent').find('#section_id').html(zresponse);
		 }
		 else{
			curobj.closest('.class-parent').find('#section_id_'+particularid).html(zresponse);
		 }
          
      }
    });
  }
}


jQuery.validator.addMethod("numberonly", function(value, element) 
	{
		return this.optional(element) || /^[0-9]+$/i.test(value);
	}, "Number only please");
