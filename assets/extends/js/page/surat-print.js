var ajaxUrl = baseUrl('surat'),
	ajaxSource = ajaxUrl + 'json_grid';

$(document).ready(function () {
	loadTtd();

	$('#btn_save').on('click', function (e) {
		e.preventDefault();
		laddaButton = Ladda.create(this);
		laddaButton.start();
		simpan();
	});
});

function loadTtd() {
	let id_surat = $('#id_surat').val();
	$.ajax({
		type: "GET",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_get_ttd/" + id_surat,
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
		},
		success: function (response) {
			var obj = response;
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			if (obj.status == "OK") {
				let suratTtdData = obj.data.surat_ttd;
				for (var i = 0; i < suratTtdData.length; i++) {
					// let template = '<img class="page-ttd" id="ttd-' + suratTtdData[i]['id_detail'] + '" src="' + baseUrl() + 'watch/' + suratTtdData[i]['nama'] + '?un=' + suratTtdData[i]['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData[i]['path_file'] + '" style="left:' + suratTtdData[i]['left'] + 'px;top:' + suratTtdData[i]['top'] + 'px">';

					let template = '<div class="kt-avatar kt-avatar--outline kt-avatar--circle-" id="ttd-' + suratTtdData[i]['id_detail'] + '" style="left:' + suratTtdData[i]['left'] + 'px;top:' + suratTtdData[i]['top'] + 'px"><img class="page-ttd"  src="' + baseUrl() + 'watch/' + suratTtdData[i]['nama'] + '?un=' + suratTtdData[i]['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData[i]['path_file'] + '"><label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Hapus ttd" onclick="hapusTtd()"><i class="fa fa-times"></i></label></div>';
					// let template = '<div id="ttd-' + suratTtdData[i]['id_detail'] + '" style="left:' + suratTtdData[i]['left'] + 'px;top:' + suratTtdData[i]['top'] + 'px" class="page-ttd"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratTtdData[i]['nama'] + '?un=' + suratTtdData[i]['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData[i]['path_file'] + '"><label class="kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus ttd" onclick="hapusTtd()"><i class="fa fa-times"></i></label></div>';

					$('.page-boundary').eq(0).before(template);
					setAction(suratTtdData[i]['id_detail'], suratTtdData[i]['id_ttd']);
				}

				$('[data-toggle="kt-tooltip]').tooltip(); 

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

function tambahTtd() {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_add_ttd",
		data: {
			id_surat: $('#id_surat').val(),
			top: $('html').scrollTop(),
			left: 25
		},
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
		},
		success: function (response) {
			var obj = response;
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			if (obj.status == "OK") {
				let suratTtdData = obj.data.surat_ttd;

				let template = '<div id="ttd-' + suratTtdData['id_detail'] + '" style="left:' + suratTtdData['left'] + 'px;top:0px" class="page-ttd"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratTtdData['nama'] + '?un=' + suratTtdData['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData['path_file'] + '"><label class="kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus ttd" onclick="hapusTtd()"><i class="fa fa-times"></i></label></div>';
				// let template = '<img class="page-ttd" id="ttd-' + suratTtdData['id_detail'] + '" src="' + baseUrl() + 'watch/' + suratTtdData['nama'] + '?un=' + suratTtdData['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData['path_file'] + '" style="left:' + suratTtdData['left'] + 'px;top:' + suratTtdData['top'] + 'px">';

				$('.page-boundary').eq(0).before(template);
				setAction(suratTtdData['id_detail'], suratTtdData['id_ttd']);
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

function setAction(id_detail, id_ttd) {
	$("#ttd-" + id_detail).draggable({
		addClasses: false,
		cursor: "move"
	});

	$('#ttd-' + id_detail).on('drag', function (e) {
		console.log('h');
	});

	$('#ttd-' + id_detail).on('dragstop', function (e) {
		setPosition(id_detail, id_ttd);
	});
}

function setPosition(id_detail, id_ttd) {
	let left = $('#ttd-' + id_detail).css('left').replace('px', '');
	let top = $('#ttd-' + id_detail).css('top').replace('px', '');
	let id_surat = $('#id_surat').val();

	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {
			id_detail: id_detail,
			id_ttd: id_ttd,
			id_surat: id_surat,
			left,
			top
		},
		url: ajaxUrl + 'json_save_position',
		success: function (response) {
			console.log(response);
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
	let file = myDropzone.files.length;
	var data = $("#form1").serializeArray();
	if (file > 0) {
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
					myDropzone.options.url = ajaxUrl + 'json_upload/' + obj.data.surat.id_surat;
					dropzone_exec();
				} else {
					Swal.fire('Pemberitahuan', obj.message, 'warning');
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
		Swal.fire('Pemberitahuan', 'Berkas wajib diisi!', 'warning');
	}
}

function hapusTtd() {
	Swal.fire({
		title: "Konfirmasi?",
		text: "Apakah Anda yakin akan menghapus tanda tangan ini?",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn btn-danger m-btn m-btn--custom",
		confirmButtonText: "Ya, hapus!",
		cancelButtonText: "Tidak, batalkan!"
	}).then(function(isConfirm) {
		console.log(isConfirm);
		if (isConfirm['value']) {
			// $.ajax({
			// 	type:"POST",
			// 	headers: {
			// 		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			// 	},
			// 	url: ajaxUrl+"json_remove_file",
			// 	data: {id_file:id_file},
			// 	beforeSend: function() {
			// 		preventLeaving();
			// 		$('.btn_close_modal').addClass('hide');
			// 		$('.se-pre-con').show();
			// 	},
			// 	success:function(response){
			// 		window.onbeforeunload = false;
			// 		$('.btn_close_modal').removeClass('hide');
			// 		$('.se-pre-con').hide();

			// 		var obj = response;

			// 		if(obj.status == "OK"){
			// 			$('#item-file-'+id_file).remove();
			// 			$('[role=tooltip]').remove();
			// 			Swal.fire('OK', obj.message, 'success');
			// 		} else {
			// 			Swal.fire('Pemberitahuan', obj.message, 'warning');
			// 		}

			// 	},
			// 	error: function(response) {
			// 		var head = 'Maaf', message = 'Terjadi kesalahan koneksi', type = 'error';
			// 		window.onbeforeunload = false;
			// 		$('.btn_close_modal').removeClass('hide');
			// 		$('.se-pre-con').hide();            

			// 		if(response['status'] == 401 || response['status'] == 419){
			// 			location.reload();
			// 		}else{
			// 			if(response['status'] != 404 && response['status'] != 500 ){
			// 				var obj = JSON.parse(response['responseText']);

			// 				if(!$.isEmptyObject(obj.message)){
			// 					if(obj.code > 400){
			// 						head = 'Maaf';
			// 						message = obj.message;
			// 						type = 'error';
			// 					}else{
			// 						head = 'Pemberitahuan';
			// 						message = obj.message;
			// 						type = 'warning';
			// 					}
			// 				}
			// 			}

			// 			Swal.fire(head, message, type);
			// 		}
			// 	}
			// });
		}
	});
}