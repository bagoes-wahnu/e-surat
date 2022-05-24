var ajaxUrl = baseUrl('advance/id'),
	laddaButtonEncrypt, laddaButtonDecrypt;

$(document).ready(function () {
	$('#btn_encrypt').on('click', function (e) {
		e.preventDefault();
		laddaButtonEncrypt = Ladda.create(this);
		laddaButtonEncrypt.start();
		encryptId();
	});
    
    $('#btn_decrypt').on('click', function (e) {
		e.preventDefault();
		laddaButtonDecrypt = Ladda.create(this);
		laddaButtonDecrypt.start();
		decryptId();
	});
});

function encryptId() {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
        url: ajaxUrl + "json_encrypt",
        data : {category : $('#category').val(), id : $('#id').val()},
		beforeSend: function () {
            preventLeaving();
            $('.se-pre-con').show();
		},
		success: function (response) {
            window.onbeforeunload = false;
            $('.se-pre-con').hide();
            laddaButtonEncrypt.stop();

			var obj = response;

			if (obj.status == "OK") {
                if($.isEmptyObject(obj.data['id'])){
                    $('#result').val('ID Not Found');
                    Swal.fire('Pemberitahuan', 'ID Not Found', 'warning');
                }else{
                    $('#result').val(obj.data['id']);
                }
			} else {
				Swal.fire('Pemberitahuan', obj.message, 'warning');
			}
		},
		error: function (response) {
			var head = 'Maaf',
				message = 'Terjadi kesalahan koneksi',
				type = 'error';
            window.onbeforeunload = false;
            
            $('.se-pre-con').hide();
            laddaButtonEncrypt.stop();

			if (response['status'] == 401 || response['status'] == 419) {
				location.reload();
			} else {
				let error_log = '';
				if (response['status'] != 404 && response['status'] != 401 && response['status'] != 500) {
					var obj = JSON.parse(response['responseText']);

					if (!$.isEmptyObject(obj.data.error_log)) {
						error_log = obj.data.error_log;
					}

					if (!$.isEmptyObject(obj.message)) {
						if (obj.code > 400) {
							head = 'Maaf';
							message = obj.message;
							type = 'error';
						} else {
							head = 'Pemberitahuan';
							message = obj.message;
							type = 'warning';
						}
					}
				}

				if (error_log != '') {
					let listError = '<ul style="text-align:left;">';
					for (var key in error_log) {

						for (var i = 0; i < error_log[key].length; i++) {
							listError += '<li>' + error_log[key][i] + '</li>';
						}

					}
					listError += '</ul>';

					let detail_error = '<div class="accordion accordion-light  accordion-toggle-arrow" id="accordionLogErrorSwal">\
					<div class="card">\
					<div class="card-header" id="headingLogErrorSwal">\
					<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseLogErrorSwal" aria-expanded="false" aria-controls="collapseLogErrorSwal">\
					Detail Error\
					</div>\
					</div>\
					<div id="collapseLogErrorSwal" class="collapse" aria-labelledby="headingLogErrorSwal" data-parent="#accordionLogErrorSwal" style="">\
					<div class="card-body">' + listError + '</div>\
					</div>\
					</div>\
					</div>';

					message += detail_error;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function decryptId() {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
        url: ajaxUrl + "json_decrypt",
        data : {category : $('#category').val(), id : $('#id').val()},
		beforeSend: function () {
            preventLeaving();
            $('.se-pre-con').show();
		},
		success: function (response) {
            window.onbeforeunload = false;
            $('.se-pre-con').hide();
            laddaButtonDecrypt.stop();

			var obj = response;

			if (obj.status == "OK") {
                if($.isEmptyObject(obj.data['id'])){
                    $('#result').val('ID Not Found');
                    Swal.fire('Pemberitahuan', 'ID Not Found', 'warning');
                }else{
                    $('#result').val(obj.data['id']);
                }
			} else {
				Swal.fire('Pemberitahuan', obj.message, 'warning');
			}
		},
		error: function (response) {
			var head = 'Maaf',
				message = 'Terjadi kesalahan koneksi',
				type = 'error';
            window.onbeforeunload = false;
            laddaButtonDecrypt.stop();
            
            $('.se-pre-con').hide();

			if (response['status'] == 401 || response['status'] == 419) {
				location.reload();
			} else {
				let error_log = '';
				if (response['status'] != 404 && response['status'] != 401 && response['status'] != 500) {
					var obj = JSON.parse(response['responseText']);

					if (!$.isEmptyObject(obj.data.error_log)) {
						error_log = obj.data.error_log;
					}

					if (!$.isEmptyObject(obj.message)) {
						if (obj.code > 400) {
							head = 'Maaf';
							message = obj.message;
							type = 'error';
						} else {
							head = 'Pemberitahuan';
							message = obj.message;
							type = 'warning';
						}
					}
				}

				if (error_log != '') {
					let listError = '<ul style="text-align:left;">';
					for (var key in error_log) {

						for (var i = 0; i < error_log[key].length; i++) {
							listError += '<li>' + error_log[key][i] + '</li>';
						}

					}
					listError += '</ul>';

					let detail_error = '<div class="accordion accordion-light  accordion-toggle-arrow" id="accordionLogErrorSwal">\
					<div class="card">\
					<div class="card-header" id="headingLogErrorSwal">\
					<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseLogErrorSwal" aria-expanded="false" aria-controls="collapseLogErrorSwal">\
					Detail Error\
					</div>\
					</div>\
					<div id="collapseLogErrorSwal" class="collapse" aria-labelledby="headingLogErrorSwal" data-parent="#accordionLogErrorSwal" style="">\
					<div class="card-body">' + listError + '</div>\
					</div>\
					</div>\
					</div>';

					message += detail_error;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}
