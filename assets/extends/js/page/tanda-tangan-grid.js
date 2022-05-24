var tableData, myDropzoneHitam, myDropzoneBiru, fileCounter = 0, isFileHitamRequired=false, isFileBiruRequired=false,
tableTarget = '#table1',
ajaxUrl = baseUrl('tanda-tangan'),
ajaxSource = ajaxUrl + 'json_grid',
laddaButton;

var KTBootstrapSelect = function () {

    // Private functions
    var demos = function () {
        // minimum setup
        $('.kt-selectpicker').selectpicker();
    }

    return {
        // public functions
        init: function() {
        	demos(); 
        }
    };
}();

jQuery(document).ready(function() {
	KTBootstrapSelect.init();
});


$(document).ready(function () {
	setMenu('#tandaTanganNav');
	init_dropzone();

	$('#table1').DataTable();

	$('[data-togle="m-tooltip"]').tooltip();

	$('#btn_save').on('click', function (e) {
		e.preventDefault();
		laddaButton = Ladda.create(this);
		laddaButton.start();
		simpan();
	});

	$('.input-tanda_tangan').on("keyup", function (event) {
		event.preventDefault();
		if (event.keyCode === 13) {
			$("#btn_save").click();
		}
	});
});

function edit(id_tanda_tangan) {
	$.ajax({
		type:"GET",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl+"json_get/"+id_tanda_tangan,
		beforeSend: function() {
			preventLeaving();
			$('#field-ttd-hitam').hide();
			$('#field-ttd-biru').hide();
			$('#ttd-hitam-exist').removeAttr('src');
			$('#ttd-biru-exist').removeAttr('src');
			isFileHitamRequired = false;
			isFileBiruRequired = false;
			$('.se-pre-con').show();
		},
		success:function(response){
			window.onbeforeunload = false;

			var obj = response;

			if(obj.status == "OK"){
				$('#id_tanda_tangan').val(id_tanda_tangan);
				let rowData = obj.data.tanda_tangan;
				$('#jabatan').val(rowData['nama']);
				$('#pejabat').val(rowData['id_pegawai']);
				$('#pejabat').change();

				if($.isEmptyObject(rowData['path_file_hitam'])){
					isFileHitamRequired = true;
				}

				if($.isEmptyObject(rowData['path_file_biru'])){
					isFileBiruRequired = true;
				}

				if(!$.isEmptyObject(rowData['path_file_hitam'])){
					$('#ttd-hitam-exist').attr('src', baseUrl() + 'watch/' + rowData['nama'] + '?un=' + rowData['id_ttd'] + '&ct=tanda_tangan&src=thumbnail-500x261_' + rowData['path_file_hitam']);
					
					$('#field-ttd-hitam').show();
					$('#field-ttd-hitam').removeClass('hide');
				}

				if(!$.isEmptyObject(rowData['path_file_biru'])){
					$('#ttd-biru-exist').attr('src', baseUrl() + 'watch/' + rowData['nama'] + '?un=' + rowData['id_ttd'] + '&ct=tanda_tangan&src=thumbnail-500x261_' + rowData['path_file_biru']);
					
					$('#field-ttd-biru').show();
					$('#field-ttd-biru').removeClass('hide');
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
	let file = ((myDropzoneHitam.files.length) + (myDropzoneBiru.files.length));
	var data = $("#form1").serializeArray();

	if (
		((isFileHitamRequired == true && myDropzoneHitam.files.length > 0) || (isFileHitamRequired == false)) &&
		((isFileBiruRequired == true && myDropzoneBiru.files.length > 0) || (isFileBiruRequired == false))
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
						myDropzoneHitam.options.url = ajaxUrl + 'json_upload/hitam/' + obj.data.tanda_tangan.id_ttd;
						myDropzoneBiru.options.url = ajaxUrl + 'json_upload/biru/' + obj.data.tanda_tangan.id_ttd;
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
	$('#id_tanda_tangan').val('');
	$('#id_tanda_tangan').change();
	$('#pejabat').val('');
	$('#pejabat').change();
	myDropzoneHitam.removeAllFiles(true);
	myDropzoneBiru.removeAllFiles(true);
}

function init_dropzone() {
	myDropzoneHitam = new Dropzone("#dropzone_file_hitam", {
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
		},
		data : {warna : 'hitam'}
	});

	myDropzoneBiru = new Dropzone("#dropzone_file_biru", {
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
		},
		data : {warna : 'biru'}
	});
}

function dropzone_exec() {
	$('.btn_close_modal').addClass('hide');
	$('.se-pre-con').show();
	$('.form-control-danger').removeClass('form-control-danger');

	if (typeof myDropzoneHitam === 'undefined' && typeof myDropzoneBiru === 'undefined') {
		window.onbeforeunload = false;
		$('.se-pre-con').hide();
		$('.btn_close_modal').removeClass('hide');
		$('#modal_form').modal('hide');
		Swal.fire('Ok', 'Data berhasil disimpan', 'success');
		laddaButton.stop();
	} else {
		if (typeof myDropzoneHitam != 'undefined' && !$.isEmptyObject(myDropzoneHitam.files)) {
			fileCounter += myDropzoneHitam.files.length;
			myDropzoneHitam.processQueue();
			myDropzoneHitam.on("complete", function (file) {
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

		if (typeof myDropzoneBiru != 'undefined' && !$.isEmptyObject(myDropzoneBiru.files)) {
			fileCounter += myDropzoneBiru.files.length;
			myDropzoneBiru.processQueue();
			myDropzoneBiru.on("complete", function (file) {
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