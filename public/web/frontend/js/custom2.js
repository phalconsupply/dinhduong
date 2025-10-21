var flag_header_filter_submit_trigger_click = false;
$(document).ready(function () {
    if($("#uploadBtn2").length > 0){
        document.getElementById("uploadBtn2").onchange = function () {
            document.getElementById("uploadFile2").value = this.value;
        };
    }
    var show_pass = false 

    $('#SP').click(function(){
        if (show_pass == true){
             show_pass = false
            $("#new-pw").attr('type', 'text');
            $('#SP').removeClass( "fa-eye-slash" );
            $('#SP').addClass( "fa-eye" );
        }
        else {
            show_pass = true
             $("#new-pw").attr('type', 'password'); 
             $('#SP').removeClass( "fa-eye" );
            $('#SP').addClass( "fa-eye-slash" );  
        }
       
    });

    $('#importChangeClassModal').on('click', '.btn-submit-form', function () {
        var own = $(this);
        own.find('i').show();
        var frm = $('.add-profile-transfer-class');

        frm.ajaxSubmit({
            success: function (result) {
                $('#importChangeClassModal').modal('hide');
                $('#confirmAddStudentModal2 .modal-body').html(result);
                $('#confirmAddStudentModal2').modal('show');
                console.log(frm);
            },
            error: function (jqXHR) {
                own.find('i').hide();
                var data = jqXHR.responseText;
                data = $.parseJSON(data);
                frm.find('.alert.alert-danger').show();
                frm.find('.alert.alert-danger .error-msg').text(data.message);
            }
        });
    });

    $('#confirmAddStudentModal2').on('click', '.complete-btn', function () {
        var url = $(this).data('url');
        window.location.href = url;
    });
	
	// Nhap so lieu - Lọc danh sách - There are 2 steps for the same form: STEP_1_AJAX, STEP_2_FORM_SUBMIT
	// $('#header_filter_submit').height(200); // TODO - Debug
	// $('#header_filter_submit').on('click', function() {});
	$('body').on("click", "#header_filter_submit", function() {
		console.log('Click header_filter_submit');
		
		var jele = $(this);
		var form_jele = jele.closest('form');
		if(form_jele.length){
			// alert('flag_header_filter_submit_trigger_click = ' + flag_header_filter_submit_trigger_click);
			// Reset below filtered fields
			if(flag_header_filter_submit_trigger_click){
				flag_header_filter_submit_trigger_click = false; // Reset
			} else {
				form_jele.find('input[name=keyword]').val("");
				form_jele.find('input[name=month]').val("");
				form_jele.find('input[name=lop]').val("");
				form_jele.find('input[name=gender]').val("");
				form_jele.find('input[name=page]').val("");
				console.log(form_jele.find('input[name=lop]'));
			}
			
			var form_post_url = form_jele.attr('action');
			console.log('form_post_url');console.log(form_post_url);
			var form_data = form_jele.serialize();
			console.log('form_data');console.log(form_data);
			// alert(form_data);
			
			// Keep session
			setInterval(function () {
				$.ajax({
					url: jele.data('keep-session')
				});
			}, 60000);
		
			// Show loading forever
			var loading_jele = $('.nuti_loader');
			loading_jele.clone().insertAfter(loading_jele).show();
			
			// STEP_1_AJAX => REQUEST_2
			$.ajax({
				url: form_post_url,
				type: "POST",
				data: form_data,
				success: function(response){
					console.log('response');console.log(response);
					// console.log('form_jele');console.log(form_jele);
					form_jele.submit(); // STEP_2_FORM_SUBMIT - To redirect to url => REQUEST_3
				}
			});
			
			
		}
		
		return false;
	});
	
	// Nhap so lieu - Lưu báo cáo cân đo học sinh
	var timer_keep_session = null;
	$('body').on("click", "#filter_result_export", function() {
		console.log('Click filter_result_export');
		
		var jele = $(this);
		var form_post_url = jele.attr('href');
		
		// Keep session
		timer_keep_session = setInterval(function () {
			$.ajax({
				url: jele.data('keep-session')
			});
		}, 60000);
	
		// Show loading forever - NO NEED
		// var loading_jele = $('.nuti_loader');
		// loading_jele.clone().insertAfter(loading_jele).show();
		
		var form_data = null;
		$.ajax({
			url: form_post_url,
			type: "POST",
			data: form_data,
			success: function(response){
				console.log('response');console.log(response);
				clearInterval(timer_keep_session);
				var json_data = response;
				if(json_data && json_data.file_url){
					location.href = json_data.file_url; // Redirect to download file
				}
			}
		});
		
		
		return false;
	});
	

});