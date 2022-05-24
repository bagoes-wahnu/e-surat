var tableData, myDropzone, fileCounter = 0, isFileRequired=false,
tableTarget = '#table1',
ajaxUrl = baseUrl('stempel'),
ajaxSource = ajaxUrl + 'json_grid',
laddaButton;

$(document).ready(function () {
	setMenu('#stempelNav');
	init_dropzone();

	$('#table1').DataTable();

	$('[data-togle="m-tooltip"]').tooltip();

	$('#btn_save').on('click', function (e) {
		e.preventDefault();
		laddaButton = Ladda.create(this);
		laddaButton.start();
		simpan();
	});

	$('.input-stempel').on("keyup", function (event) {
		event.preventDefault();
		if (event.keyCode === 13) {
			$("#btn_save").click();
		}
	});
});

function edit(id_stempel) {
	$.ajax({
		type:"GET",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl+"json_get/"+id_stempel,
		beforeSend: function() {
			preventLeaving();
			$('#field-stempel').hide();
			$('#stempel-exist').removeAttr('src');
			isFileRequired = false;
			$('.se-pre-con').show();
		},
		success:function(response){
			window.onbeforeunload = false;

			var obj = response;

			if(obj.status == "OK"){
				$('#id_stempel').val(id_stempel);
				let rowData = obj.data.stempel;
				$('#nama').val(rowData['nama_stempel']);

				if($.isEmptyObject(rowData['path_file'])){
					isFileRequired = true;
				}

				if(!$.isEmptyObject(rowData['path_file'])){
					$('#stempel-exist').attr('src', baseUrl() + 'watch/' + rowData['nama_stempel'] + '?un=' + rowData['id_stempel'] + '&ct=stempel&src=thumbnail-500x500_' + rowData['path_file']);
					
					$('#field-stempel').show();
					$('#field-stempel').removeClass('hide');
				}

				$('.se-pre-con').hide();
				$('#modal_form').modal({
					backdrop: 'static',
					keyboard: false
				}, 'show');
			} else {
				$('.se-pre-con').hide();
				Swal.fire('Pemberitahuan', obj.message, 'warning');
			}
		},
		error: function (response) {
			var head = 'Maaf',
			message = 'Terjadi kesalahan koneksi',
			type = 'error';
			window.onbeforeunload = false;
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

function simpan() {
	let file = (myDropzone.files.length);
	var data = $("#form1").serializeArray();

	if (
		((isFileRequired == true && myDropzone.files.length > 0) || (isFileRequired == false))
		) {
		$.ajax({
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: ajaxUrl + "json_save",
			data: data,
			beforeSend: function () {
				preventLeaving();
				$('.btn_close_modal').addClass('hide');
				$('.se-pre-con').show();
				$('#modal_form').modal('hide');
			},
			success: function (response) {
				var obj = response;

				if (obj.status == "OK") {
					if(file > 0){
						myDropzone.options.url = ajaxUrl + 'json_upload/' + obj.data.stempel.id_stempel;
						dropzone_exec();
					}else{
						$('.se-pre-con').hide();
						Swal.fire('Ok', 'Data berhasil disimpan', 'success');
						laddaButton.stop();
					}
				} else {
					Swal.fire('Pemberitahuan', obj.message, 'warning');
					laddaButton.stop();
					window.onbeforeunload = false;
					$('.btn_close_modal').removeClass('hide');
					$('.se-pre-con').hide();
				}
			},
			error: function (response) {
				var head = 'Maaf',
				message = 'Terjadi kesalahan koneksi',
				type = 'error';
				window.onbeforeunload = false;
				$('.se-pre-con').hide();
				$('.btn_close_modal').removeClass('hide');
				$('#modal_form').modal({
					backdrop: 'static',
					keyboard: false
				}, 'show');
				laddaButton.stop();

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
	} else {
		laddaButton.stop();
		Swal.fire('Pemberitahuan', 'Berkas wajib diisi!', 'warning');
	}
}

function resetForm(method = '') {
	$('#id_stempel').val('');
	$('#id_stempel').change();
	$('#pejabat').val('');
	$('#pejabat').change();
	myDropzone.removeAllFiles(true);
}

function init_dropzone() {
	myDropzone = new Dropzone("#dropzone_stempel", {
		url: ajaxUrl + "upload_file",
		dictCancelUpload: "Cancel",
		maxFilesize: 5,
		parallelUploads: 1,
		maxFiles: 1,
		addRemoveLinks: true,
		acceptedFiles: '.png,.jpg,.jpeg',
		autoProcessQueue: false,
		init: function () {
			this.on("error", function (file) {
				if (!file.accepted) {
					this.removeFile(file);
					Swal.fire('Pemberitahuan', 'Silahkan periksa file Anda lagi', 'warning');
				} else if (file.status == 'error') {
					this.removeFile(file);
					Swal.fire('Maaf', 'Terjadi kesalahan koneksi', 'error');
				}
			});

			this.on('resetFiles', function (file) {
				this.removeAllFiles();
			});
		},
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
}

function dropzone_exec() {
	$('.btn_close_modal').addClass('hide');
	$('.se-pre-con').show();
	$('.form-control-danger').removeClass('form-control-danger');

	if (typeof myDropzone === 'undefined') {
		window.onbeforeunload = false;
		$('.se-pre-con').hide();
		$('.btn_close_modal').removeClass('hide');
		$('#modal_form').modal('hide');
		Swal.fire('Ok', 'Data berhasil disimpan', 'success');
		laddaButton.stop();
	} else {
		if (!$.isEmptyObject(myDropzone.files)) {
			fileCounter += myDropzone.files.length;
			myDropzone.processQueue();
			myDropzone.on("complete", function (file) {
				fileCounter--;
				if (file.status != 'success') {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
						laddaButton.stop();
					}

					Swal.fire('Maaf', 'Terjadi kesalahan koneksi', 'error');
				} else {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
						$('#modal_form').modal('hide');
						Swal.fire('Ok', 'Data berhasil disimpan', 'success');
						laddaButton.stop();
					}
				}
				this.removeFile(file);
			});
		}

		if (fileCounter == 0) {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();
			$('.btn_close_modal').removeClass('hide');
			$('#modal_form').modal('hide');
			Swal.fire('Ok', 'Data berhasil disimpan', 'success');
			laddaButton.stop();
		}
		
	}
}

