var
ajaxUrl = baseUrl('surat'),
ajaxSource = ajaxUrl + 'json_grid',
defaultWidth = 195,
defaultHeight = 106,
defaultStempelWidth = 150,
defaultStempelHeight = 150;

$(document).ready(function () {
	loadTtd();
	loadQrCode();
	loadStempel();

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
					let template = '<div id="ttd-' + suratTtdData[i]['id_detail'] + '" style="left:' + suratTtdData[i]['left'] + 'px;top:' + suratTtdData[i]['top'] + 'px;" class="page-ttd"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratTtdData[i]['nama'] + '?un=' + suratTtdData[i]['id_ttd'] + '&ct=tanda_tangan&src=' + suratTtdData[i]['path_file'] + '"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus ttd" onclick="hapusTtd(\'' + suratTtdData[i]['id_detail'] + '\')"><i class="fa fa-times"></i></label></div>';
					$('.content').eq(0).append(template);
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function tambahTtd(warna) {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_add_ttd",
		data: {
			id_surat: $('#id_surat').val(),
			warna : warna.toLowerCase(),
			top: $('html').scrollTop(),
			left: 25,
			width: defaultWidth,
			height: defaultHeight
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

				let template = '<div id="ttd-' + suratTtdData['id_detail'] + '" style="left:' + suratTtdData['left'] + 'px;top:' + suratTtdData['top'] + 'px;" class="page-ttd"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratTtdData['nama'] + '?un=' + suratTtdData['id_ttd'] + '&ct=tanda_tangan&src=thumbnail-500x261_' + suratTtdData['path_file'] + '"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus ttd" onclick="hapusTtd(\'' + suratTtdData['id_detail'] + '\')"><i class="fa fa-times"></i></label></div>';

				$('.content').eq(0).append(template);
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

					message += listError;

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

	$('#ttd-' + id_detail).on('dragstop', function (e) {
		setPosition(id_detail, id_ttd);
	});
}

function setPosition(id_detail, id_ttd) {
	let left = $('#ttd-' + id_detail).css('left').replace('px', '');
	let top = $('#ttd-' + id_detail).css('top').replace('px', '');
	let width = $('#ttd-' + id_detail).css('width').replace('px', '');
	let height = $('#ttd-' + id_detail).css('height').replace('px', '');
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
			left:left,
			top:top,
			width:width,
			height:height
		},
		url: ajaxUrl + 'json_save_position',
		success: function (response) {
			/* console.log(response); */
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function simpan() {
	let id_surat = $('#id_surat').val();

	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_save_pdf",
		data: {id_surat:id_surat},
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
		},
		success: function (response) {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			var obj = response;

			if (obj.status == "OK") {
				Swal.fire('OK', obj.message, 'success');
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function hapusTtd(id_detail) {
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
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: ajaxUrl+"json_remove_ttd",
				data: {id_detail:id_detail},
				beforeSend: function() {
					preventLeaving();
					$('.se-pre-con').show();
				},
				success:function(response){
					window.onbeforeunload = false;
					$('.se-pre-con').hide();

					var obj = response;

					if(obj.status == "OK"){
						$('#ttd-'+id_detail).remove();
						/*Swal.fire('OK', obj.message, 'success');*/
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

							message += listError;

							Swal.fire(head, message, type);
						} else {
							Swal.fire(head, message, type);
						}
					}
				}
			});
		}
	});
}

/* QR Code */
function loadQrCode() {
	let id_surat = $('#id_surat').val();
	$.ajax({
		type: "GET",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_get_qr/" + id_surat,
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
		},
		success: function (response) {
			var obj = response;
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			if (obj.status == "OK") {
				let suratQrData = obj.data.surat_qr;
				for (var i = 0; i < suratQrData.length; i++) {
					let template = '<div id="qrcode-' + suratQrData[i]['id_detail_qr'] + '" style="left:' + suratQrData[i]['left'] + 'px;top:' + suratQrData[i]['top'] + 'px" class="page-qrcode"><img style="width: 100%;" src="' + baseUrl() + 'watch/qrcode.png?un='+suratQrData[i]['id_surat']+'&ct=qr&src=qrcode.png"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus QR Code" onclick="hapusQrCode(\'' + suratQrData[i]['id_detail_qr'] + '\')"><i class="fa fa-times"></i></label></div>';
					/*$('#page-'+(i+1)).append(template);*/
					$('.content').eq(0).append(template);
					setQrAction(suratQrData[i]['id_detail_qr']);
					setAction(suratQrData[i]['id_detail_qr']);
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function tambahQrCode() {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_add_qr",
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
				let suratQrData = obj.data.surat_qr;

				let template = '<div id="qrcode-' + suratQrData['id_detail_qr'] + '" style="left:' + suratQrData['left'] + 'px;top:' + suratQrData['top'] + 'px" class="page-qrcode"><img style="width: 100%;" src="' + baseUrl() + 'watch/qrcode.png?un='+suratQrData['id_surat']+'&ct=qr&src=qrcode.png"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus QR Code" onclick="hapusQrCode(\'' + suratQrData['id_detail_qr'] + '\')"><i class="fa fa-times"></i></label></div>';

				$('.content').eq(0).append(template);
				setQrAction(suratQrData['id_detail_qr']);
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function setQrAction(id_detail_qr) {
	$("#qrcode-" + id_detail_qr).draggable({
		addClasses: false,
		cursor: "move"
	});

	/*$('#qrcode-' + id_detail_qr).on('drag', function (e) {
		console.log('h');
	});*/

	$('#qrcode-' + id_detail_qr).on('dragstop', function (e) {
		setPositionQR(id_detail_qr);
	});
}

function setPositionQR(id_detail_qr) {
	let left = $('#qrcode-' + id_detail_qr).css('left').replace('px', '');
	let top = $('#qrcode-' + id_detail_qr).css('top').replace('px', '');
	let id_surat = $('#id_surat').val();

	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {
			id_detail_qr: id_detail_qr,
			id_surat: id_surat,
			left,
			top
		},
		url: ajaxUrl + 'json_save_position_qr',
		success: function (response) {
			/* console.log(response); */
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function hapusQrCode(id_detail_qr) {
	Swal.fire({
		title: "Konfirmasi?",
		text: "Apakah Anda yakin akan menghapus QR Code ini?",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn btn-danger m-btn m-btn--custom",
		confirmButtonText: "Ya, hapus!",
		cancelButtonText: "Tidak, batalkan!"
	}).then(function(isConfirm) {
		console.log(isConfirm);
		if (isConfirm['value']) {
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: ajaxUrl+"json_remove_qr",
				data: {id_detail_qr:id_detail_qr},
				beforeSend: function() {
					preventLeaving();
					$('.se-pre-con').show();
				},
				success:function(response){
					window.onbeforeunload = false;
					$('.se-pre-con').hide();

					var obj = response;

					if(obj.status == "OK"){
						$('#qrcode-'+id_detail_qr).remove();
						/*Swal.fire('OK', obj.message, 'success');*/
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

							message += listError;

							Swal.fire(head, message, type);
						} else {
							Swal.fire(head, message, type);
						}
					}
				}
			});
		}
	});
}

/* Stempel */
function loadStempel() {
	let id_surat = $('#id_surat').val();
	$.ajax({
		type: "GET",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_get_stempel/" + id_surat,
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
		},
		success: function (response) {
			var obj = response;
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			if (obj.status == "OK") {
				let suratStempelData = obj.data.surat_stempel;
				for (var i = 0; i < suratStempelData.length; i++) {
					let template = '<div id="stempel-' + suratStempelData[i]['id_detail'] + '" style="left:' + suratStempelData[i]['left'] + 'px;top:' + suratStempelData[i]['top'] + 'px;" class="page-stempel"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratStempelData[i]['nama_stempel'] + '?un=' + suratStempelData[i]['id_stempel'] + '&ct=stempel&src=thumbnail-500x500_' + suratStempelData[i]['path_file'] + '"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus stempel" onclick="hapusStempel(\'' + suratStempelData[i]['id_detail'] + '\')"><i class="fa fa-times"></i></label></div>';
					$('.content').eq(0).append(template);
					setActionStempel(suratStempelData[i]['id_detail'], suratStempelData[i]['id_stempel']);
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function tambahStempel(warna) {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_add_stempel",
		data: {
			id_surat: $('#id_surat').val(),
			top: $('html').scrollTop(),
			left: 25,
			width: defaultStempelWidth,
			height: defaultStempelHeight
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
				let suratStempelData = obj.data.surat_stempel;

				let template = '<div id="stempel-' + suratStempelData['id_detail'] + '" style="left:' + suratStempelData['left'] + 'px;top:' + suratStempelData['top'] + 'px;" class="page-stempel"><img style="width: 100%;" src="' + baseUrl() + 'watch/' + suratStempelData['nama_stempel'] + '?un=' + suratStempelData['id_stempel'] + '&ct=stempel&src=thumbnail-500x500_' + suratStempelData['path_file'] + '"><label class="btn_hapus kt-avatar__upload no-print" data-toggle="kt-tooltip" title="" data-original-title="Hapus stempel" onclick="hapusStempel(\'' + suratStempelData['id_detail'] + '\')"><i class="fa fa-times"></i></label></div>';

				$('.content').eq(0).append(template);
				setActionStempel(suratStempelData['id_detail'], suratStempelData['id_stempel']);
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function setActionStempel(id_detail, id_stempel) {
	$("#stempel-" + id_detail).draggable({
		addClasses: false,
		cursor: "move"
	});

	$('#stempel-' + id_detail).on('dragstop', function (e) {
		setPositionStempel(id_detail, id_stempel);
	});
}

function setPositionStempel(id_detail, id_stempel) {
	let left = $('#stempel-' + id_detail).css('left').replace('px', '');
	let top = $('#stempel-' + id_detail).css('top').replace('px', '');
	let width = $('#stempel-' + id_detail).css('width').replace('px', '');
	let height = $('#stempel-' + id_detail).css('height').replace('px', '');
	let id_surat = $('#id_surat').val();

	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {
			id_detail: id_detail,
			id_stempel: id_stempel,
			id_surat: id_surat,
			left:left,
			top:top,
			width:width,
			height:height
		},
		url: ajaxUrl + 'json_save_position_stempel',
		success: function (response) {
			/* console.log(response); */
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

					message += listError;

					Swal.fire(head, message, type);
				} else {
					Swal.fire(head, message, type);
				}
			}
		}
	});
}

function hapusStempel(id_detail) {
	Swal.fire({
		title: "Konfirmasi?",
		text: "Apakah Anda yakin akan menghapus stempel ini?",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn btn-danger m-btn m-btn--custom",
		confirmButtonText: "Ya, hapus!",
		cancelButtonText: "Tidak, batalkan!"
	}).then(function(isConfirm) {
		console.log(isConfirm);
		if (isConfirm['value']) {
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: ajaxUrl+"json_remove_stempel",
				data: {id_detail:id_detail},
				beforeSend: function() {
					preventLeaving();
					$('.se-pre-con').show();
				},
				success:function(response){
					window.onbeforeunload = false;
					$('.se-pre-con').hide();

					var obj = response;

					if(obj.status == "OK"){
						$('#stempel-'+id_detail).remove();
						/*Swal.fire('OK', obj.message, 'success');*/
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

							message += listError;

							Swal.fire(head, message, type);
						} else {
							Swal.fire(head, message, type);
						}
					}
				}
			});
		}
	});
}