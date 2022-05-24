var tableData, myDropzone, fileCounter = 0,
	isRevise = false,
	myDropzoneUpdate, tableHistoryRollback,
	tableTarget = '#table1',
	ajaxUrl = baseUrl('surat'),
	ajaxSource = ajaxUrl + 'json_grid',
	arrPejabat = {
		1: {
			name: 'Kadis',
			type: 'bg-green-custom'
		},
		2: {
			name: 'Sekretaris',
			type: 'kt-badge--warning'
		},
		3: {
			name: 'UPTD',
			type: 'kt-badge--info'
		}
	},
	laddaButton, laddaButtonUpload;
let penomoranSuratID = "";

$(document).ready(function () {
	setMenu('#daftarSuratNav');
	load_table();
	init_dropzone();

	$('#jenis_surat').selectpicker();

	protectString('#pegawai', 150);
	protectString('#judul', 150);

	let date = new Date();
	let today = advanceDateFormat(date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate(), 'd/m/Y');

	$('#tanggal').datepicker({
		format: 'dd/mm/yyyy',
		endDate: today,
		todayHighlight: true
	});

	$('#penomoran-tanggal').datepicker({
		format: 'dd-mm-yyyy',
		// startDate: today,
		todayHighlight: true
	});

	load_nomor('nomor_penomoran', 'Nomor Surat', '10-10-1000', $('#sector').val());

	load_sektor('sector', 'Sektor', 'get_sector');

    $('#penomoran-tanggal').on('change',function(e){
		load_nomor('nomor_penomoran', 'Nomor Surat', $(this).val(), $('#sector').val());
    })

	$('#sector').on('change',function(e){
		load_nomor('nomor_penomoran', 'Nomor Surat', $('#penomoran-tanggal').val(), $(this).val());
    })

	if (typeof tableData !== 'undefined') {
		tableData.on('draw.dt', function () {
			$('.se-pre-con').hide();

			$('[data-toggle=kt-tooltip]').tooltip({
				trigger: 'hover'
			});

			$('[data-toggle=kt-tooltip]').on('click', function () {
				$(this).tooltip('hide')
			})
		});
	}

	if (typeof tableHistoryRollback !== 'undefined') {
		tableHistoryRollback.on('draw.dt', function () {
			$('[data-toggle=kt-tooltip]').tooltip({
				trigger: 'hover'
			});

			$('[data-toggle=kt-tooltip]').on('click', function () {
				$(this).tooltip('hide')
			})
		});
	}

	$('#btn_save').on('click', function (e) {
		e.preventDefault();
		laddaButton = Ladda.create(this);
		laddaButton.start();
		simpan();
	});

	$('#btn_upload').on('click', function (e) {
		e.preventDefault();
		laddaButtonUpload = Ladda.create(this);
		laddaButtonUpload.start();
		uploadFile();
	});

	$('#btn_rollback').on('click', function () {
		doRollback();
	});

	$('.input-surat').on("keyup", function (event) {
		event.preventDefault();
		if (event.keyCode === 13) {
			$("#btn_save").click();
		}
	});

	$('#pegawai').selectpicker();
});

function quickStats() {
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_stats",
		beforeSend: function () {
			preventLeaving();
		},
		success: function (response) {
			window.onbeforeunload = false;

			var obj = response;

			if (obj.status == "OK") {
				if (!$.isEmptyObject(obj.data.stats)) {
					$('#stats_approved').html(obj.data.stats['approved']);
					$('#stats_rejected').html(obj.data.stats['rejected']);
					$('#stats_waiting').html(obj.data.stats['waiting']);
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

function load_table() {
	tableData = $(tableTarget).DataTable({
		"aLengthMenu": [
			[10, 25, 50, -1],
			[10, 25, 50, "All"]
		],
		"bStateSave": false,
		"bDestroy": true,
		"processing": true,
		"serverSide": true,
		"ajax": {
			url: ajaxSource,
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			beforeSend: function () {
				quickStats();
				$('.se-pre-con').show();
			},
			error: function (response) {
				var head = 'Maaf',
					message = 'Terjadi kesalahan koneksi',
					type = 'error';
				window.onbeforeunload = false;
				$('.btn_close_modal').removeClass('hide');
				$('.se-pre-con').hide();

				if (response['status'] == 401 || response['status'] == 419) {
					location.reload();
				} else {
					if (response['status'] != 404 && response['status'] != 500) {
						var obj = JSON.parse(response['responseText']);

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

					Swal.fire(head, message, type);
				}
			}
		},
		"sPaginationType": "full_numbers",
		"aoColumns": [{
			"mData": "id_surat"
		},
		{
			"mData": "judul"
		},
		{
			"mData": "jenis",
			'defaultContent': '-'
		},
		{
			"mData": "tanggal"
		},
		{
			"mData": "nama_pegawai",
			'defaultContent': '-'
		},
		{
			"mData": "nama_ttd"
		},
		{
			"mData": "state"
		},
		{
			"mData": "id_surat"
		}
		],
		"aaSorting": [
			[3, 'desc']
		],
		"lengthMenu": [10, 25, 50, 75, 100],
		"pageLength": 10,
		"aoColumnDefs": [{
			"aTargets": [0],
			"mData": "id_surat",
			"mRender": function (data, type, full, draw) {
				var row = draw.row;
				var start = draw.settings._iDisplayStart;
				var length = draw.settings._iDisplayLength;

				var counter = (start + 1 + row);

				return counter;
			}
		},
		{
			"aTargets": [3],
			"mData": "tanggal",
			"mRender": function (data, type, full) {
				let tanggal = '';

				if (!$.isEmptyObject(full.tanggal)) {
					tanggal = getDisplayDate(full.tanggal, false, 'd F Y');
				}

				return tanggal;
			}
		},
		{
			"aTargets": [5],
			"mData": "nama_ttd",
			"mRender": function (data, type, full) {
				let pejabat = '';
				let index = full.id_ttd;

				pejabat = '<span class="kt-badge ' + arrPejabat[index].type + ' kt-badge--dot"></span>&nbsp;\
				<span class="kt-font-bold">' + arrPejabat[index].name + '</span>';

				return pejabat;
			}
		},
		{
			"aTargets": [6],
			"mData": "status",
			"mRender": function (data, type, full) {
				let status = '';

				if (full.state == 1) {
					if (full.rollback == true) {
						status = '<span class="kt-badge bg-orange-dark-esurat kt-badge--inline kt-badge--pill">Rollback</span>';
					} else {
						status = '<span class="kt-badge bg-gray-custom kt-badge--inline kt-badge--pill">Waiting' + ((full.id_jabatan == idJabatan) ? ' You' : '') + '</span>';
					}
				} else {
					if (!$.isEmptyObject(full.tanggal_approve)) {
						status = '<span class="kt-badge bg-green-custom kt-badge--inline kt-badge--pill">Approved</span>';
					} else {
						status = '<span class="kt-badge bg-orange-dark-esurat kt-badge--inline kt-badge--pill">Rejected</span>';
					}
				}

				return status;
			}
		},
		{
			"aTargets": [7],
			"mData": "id_surat",
			"mRender": function (data, type, full) {
				var btn_action = '';

				if (!$.isEmptyObject(full.tanggal_approve)) {
					btn_action += ' <button type="button" onclick="modalDetailPenomoran(\'' + full.id_surat + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Penomoran"><i class="fa fa-hashtag"></i></button>';
				}


				if (advanceMode == 't') {
					btn_action += '<button type="button" onclick="modalUpload(\'' + full.id_surat + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Update File"><i class="la la-upload"></i></button>';

					btn_action += ' <button type="button" onclick="getHistoryRollback(\'' + full.id_surat + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="History Rollback"><i class="la la-folder-open"></i></button>';
				}

				if (full.rollback == true && full.id_jabatan == idJabatan && gradeUser == lastGrade) {
					btn_action = '<button type="button" onclick="revisi(\'' + full.id_surat + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Revisi Surat"><i class="la la-pencil"></i></button>';
				}

				pattern = /['"/]/g;
				let nama_file = full.judul.replace(pattern, '-');

				btn_action += '\
				<a href="' + baseUrl() + 'watch/' + nama_file + '?un=' + full.id_surat + '&ct=surat&src=' + full.path_file + '" target="_blank" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip"  title="Lihat"><i class="la la-eye"></i></a>\
				<button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" onclick="showTimeline(\'' + full.id_surat + '\')" title="Timeline"><i class="la la-calendar"></i></button>';

				if (!$.isEmptyObject(full.tanggal_approve) && gradeUser == lastGrade) {
					if (parseInt(full.portrait) > 0) {
						btn_action += ' <a id="btn-set-ttd'+full.id_surat+'" href="javascript:;" onclick="aturTandaTangan(\''+full.id_surat+'\',\''+full.srt_nomor_surat+'\',\''+full.tanggal+'\',\'portrait\',\''+full.judul+'\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Atur Tanda Tangan Portrait"><i class="la la-arrows"></i></a>';
					}

					if (parseInt(full.landscape) > 0) {
						btn_action += ' <a id="btn-set-ttd'+full.id_surat+'" href="javascript:;" onclick="aturTandaTangan(\''+full.id_surat+'\',\''+full.srt_nomor_surat+'\',\''+full.tanggal+'\',\'landscape\',\''+full.judul+'\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Atur Tanda Tangan Landscape"><i class="la la-arrows"></i></a>';
					}

					if (parseInt(full.portrait) <= 0 && parseInt(full.landscape) <= 0) {
						btn_action += ' <a id="btn-set-ttd'+full.id_surat+'" href="javascript:;" onclick="aturTandaTangan(\''+full.id_surat+'\',\''+full.srt_nomor_surat+'\',\''+full.tanggal+'\',\'default\',\''+full.judul+'\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Atur Tanda Tangan"><i class="la la-arrows"></i></a>';
					}

					btn_action += ' <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" onclick="archive(\'' + full.id_surat + '\')" title="Arsipkan Surat"><i class="la la-archive"></i></button>';
					if (full.srs_srt_id == null) {
						btn_action += ' <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" onclick="unggah_surat_selesai(\'' + full.id_surat + '\')" title="Unggah Surat Selesai"><i class="la la-upload"></i></button>';
					} else {
						btn_action += ' <a target="_blank" href="' + baseUrl() + 'watch/' + full.judul + '?un=' + full.id_surat + '&ct=surat_selesai&src=' + full.srs_path_file + '"><button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Lihat Surat Selesai"><i class="la la-file"></i></button></a>';
					}
				}


				/* <a href="' + ajaxUrl + 'p/' + full.id_surat + '" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" target="_blank" title="Cetak"><i class="la la-print"></i></a>';*/

				if (full.state == 3) {
					btn_action += ' <a href="" id="kt_sweetalert_delete" class=" btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Download"><i class="la la-download"></i></a>';
					/* btn_action += '<a href="" id="kt_sweetalert_delete" class=" btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Cetak"><i class="la la-print"></i></a>';*/
				}

				if (roleUser == 1 && full.id_jabatan_pembuat != full.id_jabatan && $.isEmptyObject(full.tanggal_approve) && full.state == 1) {
					btn_action += ' <button type="button" onclick="rollback(\'' + full.id_surat + '\', \'' + full.judul + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Rollback"><i class="la la-undo"></i></button>';
				}

				if (full.id_jabatan_pembuat == idJabatan && $.isEmptyObject(full.tanggal_approve) && ((full.state == 1 && full.id_jabatan == idJabatan) || (full.langkah < 3))) {
					btn_action += ' <button type="button" onclick="hapus(\'' + full.id_surat + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Hapus Surat"><i class="la la-trash"></i></button>';
				}

				return btn_action;
			}
		}
		],
		"fnHeaderCallback": function (nHead, aData, iStart, iEnd, aiDisplay) {
			$(nHead).children('th:nth-child(1), th:nth-child(3), th:nth-child(4)').addClass('text-center');
		},
		"fnFooterCallback": function (nFoot, aData, iStart, iEnd, aiDisplay) {
			$(nFoot).children('th:nth-child(1), th:nth-child(3), th:nth-child(4)').addClass('text-center');
		},
		"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			$(nRow).children('td:nth-child(1),td:nth-child(3),td:nth-child(4),td:nth-child(5)').addClass('text-center');
			$(nRow).children('td:nth-child(2)').addClass('align-left');
		}
	});

	/*$(tableTarget).closest( '.dataTables_wrapper' ).find( 'select' ).selectpicker( {//
		minimumResultsForSearch: -1
	});*/
}

function aturTandaTangan(id_surat, nomor_surat, tanggal_surat, orientation,judul)
{
	//var getYear = tanggal_surat.split('-');

	// if(id_jenis=="13"){
    //     console.log(ajaxUrl + 'v/' + id_surat + '?orientation=' + orientation)
	// 	window.open(ajaxUrl + 'v/' + id_surat + '?orientation=' + orientation, '_blank');
    // }else 
    //console.log(id_surat+" - "+ nomor_surat+" - "+ tanggal_surat+" - "+ orientation+" - "+judul)
    if (nomor_surat == 'null')
	{
		//Swal.fire('Pemberitahuan', 'Belum ada nomor surat', 'warning');
        swal.fire({
            title: "Anda belum mengisi nomor surat",
            text: "Untuk Surat : "+judul+". Apakah anda yakin ingin melanjutkan cetak surat ?",
            type: "warning",
            buttons: true,
            confirmButtonText: 'Yakin, cetak surat',
            showCancelButton: true,
            cancelButtonText: 'Tidak',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-outline-primary',
                cancelButton: 'btn btn-primary',
            }
          })
          .then((willDo) => {
            if (willDo?.value!==undefined) {
                swal.fire({
                    title: "Opss..",
                    text: "Anda akan mencetak surat tanpa nomor. Lanjutkan ?",
                    type: "warning",
                    buttons: true,
                    confirmButtonText: 'Lanjut, cetak surat',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-outline-primary',
                        cancelButton: 'btn btn-primary',
                    }
                  })
                  .then((willDo) => {
                    if (willDo?.value!==undefined) {
                        window.open(ajaxUrl + 'v/' + id_surat + '?orientation=' + orientation, '_blank');
                    } else {}
                  });
            }
          });
	}
	else
	{
		console.log(ajaxUrl + 'v/' + id_surat + '?orientation=' + orientation)
		window.open(ajaxUrl + 'v/' + id_surat + '?orientation=' + orientation, '_blank');
	}
}

function tambah() {
	resetForm();
	$('#id_surat').val('');
	$('#action').val('add');
	$('#btn_save').html('Tambah Data');
	$('#modal_form .modal-title').html('Tambah Data surat');
	$('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait daftar surat.');

	$('#modal_form').modal({
		backdrop: 'static',
		keyboard: false
	}, 'show');
}

function revisi(id_surat) {
	isRevise = true;
	resetForm();
	$('.form-text').hide();
	$('.form-keterangan').show();
	$('#file-rollback').html('');
	$('#id_surat').val(id_surat);
	$('#action').val('edit');
	$('#btn_save').html('Simpan Revisi');
	$('#modal_form .modal-title').html('Revisi Data surat');
	$('#modal_form .modal-info').html('Isilah form dibawah ini untuk memperbaiki data terkait daftar surat.');

	$.ajax({
		url: ajaxUrl + "json_get/" + id_surat,
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
			$('.timeline-surat').remove();
		},
		success: function (response) {
			window.onbeforeunload = false;


			var obj = response;

			if (obj.status == "OK") {
				let rowData = obj.data.surat;

				$('#judul').val(rowData['judul']);
				$('#pegawai').val(rowData['id_pegawai']);
				$('#jenis_surat').val(rowData['id_jenis']);
				$('#tanggal').datepicker('setDate', advanceDateFormat(rowData['tanggal'], 'd/m/Y'));
				$('#ttd-' + rowData['id_ttd']).prop('checked', true);
				$('#keterangan').html(rowData['keterangan']);

				let user_revisi = '';

				for (var i = 0; i < obj.data.user_revisi.length; i++) {
					let rowData = obj.data.user_revisi[i];
					user_revisi += ((user_revisi == '') ? '' : ', ');
					user_revisi += rowData['nama_jabatan'];
				}

				$('#user_revisi').html(helpEmpty(user_revisi, '-'));

				for (var i = 0; i < obj.data.note_rollback.length; i++) {
					let rowFile = obj.data.note_rollback[i];
					let template = '<a href="' + rowFile['url'] + '" class="btn btn-success" style="' + ((i > 0) ? 'display:none;' : '') + '" data-fancybox="gallery-rollback" data-caption="Halaman ' + rowFile['page'] + '">Lihat file</a>';

					$('#file-rollback').append(template)
				}

				if (obj.data.with_upload == false) {
					$('#file-rollback').html('<span style="color:#fd2731 !important;">* Rollback tanpa file</span>');
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
					myDropzone.options.url = ajaxUrl + 'json_upload/' + obj.data.surat.id_surat + '?mode=' + obj.data.mode;
					dropzone_exec();
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
	$('#id_surat').val('');
	$('#id_surat').change();
	$('#jenis_surat').val('');
	$('#jenis_surat').change();
	$('#judul').val('');
	$('#judul').change();
	$('#pegawai').val('');
	$('#pegawai').change();
	$('#tanggal').datepicker('setDate', null);
	myDropzone.removeAllFiles(true);

	$('.option-ttd').prop('checked', false);

	$('.form-text').show();
	$('.form-keterangan').hide();

	isRevise = false;
}

function init_dropzone() {
	myDropzone = new Dropzone("#dropzone_file", {
		url: ajaxUrl + "upload_file",
		dictCancelUpload: "Cancel",
		maxFilesize: 4,
		parallelUploads: 1,
		maxFiles: 1,
		addRemoveLinks: true,
		acceptedFiles: 'application/pdf',
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
	});

	myDropzoneUpdate = new Dropzone("#dropzone_file_update", {
		url: ajaxUrl + "upload_file",
		dictCancelUpload: "Cancel",
		maxFilesize: 5,
		parallelUploads: 1,
		maxFiles: 1,
		addRemoveLinks: true,
		acceptedFiles: 'application/pdf',
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
	});


	myDropzoneSelesai = new Dropzone("#dropzone_surat_selesai", {
		url: ajaxUrl + "upload_surat_selesai",
		dictCancelUpload: "Cancel",
		maxFilesize: 5,
		parallelUploads: 1,
		maxFiles: 1,
		addRemoveLinks: true,
		acceptedFiles: 'application/pdf',
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
	});
}

function dropzone_exec() {
	$('.btn_close_modal').addClass('hide');
	$('.se-pre-con').show();
	$('.form-control-danger').removeClass('form-control-danger');

	if (typeof myDropzone != 'undefined') {
		if (!$.isEmptyObject(myDropzone.files)) {
			fileCounter = myDropzone.files.length;
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
						$('#modal_file').modal('hide');
						Swal.fire('Ok', 'Data berhasil disimpan', 'success');
						laddaButton.stop();
						tableData.ajax.reload();
						quickStats();
					}
				}
				this.removeFile(file);
			});
		} else {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();
			$('.btn_close_modal').removeClass('hide');
			$('#modal_file').modal('hide');
			Swal.fire('Ok', 'Data berhasil disimpan', 'success');
			laddaButton.stop();
			tableData.ajax.reload();
			quickStats();
		}
	} else {
		window.onbeforeunload = false;
		$('.se-pre-con').hide();
		$('.btn_close_modal').removeClass('hide');
		$('#modal_file').modal('hide');
		Swal.fire('Ok', 'Data berhasil disimpan', 'success');
		laddaButton.stop();
		tableData.ajax.reload();
		quickStats();
	}
}

function showTimeline(id_surat) {
	$.ajax({
		type: "POST",
		data: {
			id_surat: id_surat
		},
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl + "json_timeline",
		beforeSend: function () {
			preventLeaving();
			$('.se-pre-con').show();
			$('.timeline-surat').remove();
		},
		success: function (response) {
			window.onbeforeunload = false;


			var obj = response;

			if (obj.status == "OK") {
				for (var i = 0; i < obj.data.timeline.length; i++) {
					let rowData = obj.data.timeline[i];
					let template = '<span class="kt-timeline-v2__item-time">' + getDisplayDate(rowData['tanggal_input'], false, 'd F Y') + '<br><span class="pull-right">' + advanceDateFormat(rowData['tanggal_input'], 'H:i') + '</span></span>\
					<div class="kt-timeline-v2__item-cricle">\
					<i class="fa fa-genderless kt-font-success"></i>\
					</div>\
					<div class="kt-timeline-v2__item-text font-boldd black kt-padding-top-5">' + rowData['keterangan'] + '</div>';

					if ($('#timeline-' + rowData['id_timeline']).length > 0) {
						$('#timeline-' + rowData['id_timeline']).html(template);
					} else {
						template = '<div class="kt-timeline-v2__item timeline-surat" id="timeline-' + rowData['id_timeline'] + '">' + template + '</div>';
						$('#batas_timeline').after(template);
					}
				}

				$('.se-pre-con').hide();
				$('#modal_timeline').modal({
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

function archive(id_surat) {
	Swal.fire({
		title: "Apakah Anda yakin ingin mengarsipkan surat ini?",
		text: "NB: Surat yang diarsipkan tidak bisa dihapus",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'Ya, arsipkan surat',
		cancelButtonText: 'Tidak, batalkan'
	})
		.then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: ajaxUrl + "json_archive",
					data: {
						id_surat: id_surat
					},
					beforeSend: () => {
						preventLeaving();
						$('.btn_close_modal').addClass('hide');
						$('.se-pre-con').show();
					},
					success: (response) => {
						window.onbeforeunload = false;
						$('.btn_close_modal').removeClass('hide');
						$('.se-pre-con').hide();

						var obj = response;

						if (obj.status == "OK") {
							tableData.ajax.reload();
							Swal.fire('Ok', obj.message, 'success');
						} else {
							Swal.fire('Pemberitahuan', obj.message, 'warning');
						}
					},
					error: (response) => {
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
		})
		.catch();
}

function hapus(id_surat) {
	Swal.fire({
		title: "Apakah Anda yakin ingin menghapus surat ini?",
		text: "NB: Surat yang sudah dihapus tidak bisa dikembalikan lagi",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'Ya, hapus surat',
		cancelButtonText: 'Tidak, batalkan'
	})
		.then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: ajaxUrl + "json_remove",
					data: {
						id_surat: id_surat
					},
					beforeSend: () => {
						preventLeaving();
						$('.btn_close_modal').addClass('hide');
						$('.se-pre-con').show();
					},
					success: (response) => {
						window.onbeforeunload = false;
						$('.btn_close_modal').removeClass('hide');
						$('.se-pre-con').hide();

						var obj = response;

						if (obj.status == "OK") {
							tableData.ajax.reload();
							Swal.fire('Ok', obj.message, 'success');
						} else {
							Swal.fire('Pemberitahuan', obj.message, 'warning');
						}
					},
					error: (response) => {
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
		})
		.catch();
}

function rollback(id_surat, judul) {
	$('#id_surat_rollback').val(id_surat);
	$('#judul_rollback').val(judul);
	$('#keterangan_rollback').val('');
	$('#btn_rollback').html('Rollback');
	$('#modal_rollback .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait daftar surat.');

	$('#modal_rollback').modal({
		backdrop: 'static',
		keyboard: false
	}, 'show');
}

function doRollback() {
	Swal.fire({
		title: "Apakah Anda yakin ingin me-rollback surat ini?",
		// text: "NB: Surat yang diarsipkan tidak bisa dihapus",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'Ya, rollback surat',
		cancelButtonText: 'Tidak, batalkan'
	})
		.then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: ajaxUrl + "json_rollback",
					data: $('#formRollback').serializeArray(),
					beforeSend: () => {
						preventLeaving();
						$('.btn_close_modal').addClass('hide');
						$('.se-pre-con').show();
					},
					success: (response) => {
						window.onbeforeunload = false;
						$('.btn_close_modal').removeClass('hide');
						$('.se-pre-con').hide();

						var obj = response;

						if (obj.status == "OK") {
							$('#modal_rollback').modal('hide');
							tableData.ajax.reload();
							Swal.fire('Ok', obj.message, 'success');
						} else {
							Swal.fire('Pemberitahuan', obj.message, 'warning');
						}
					},
					error: (response) => {
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
		})
		.catch();
}

// start : advance mode
function modalUpload(id_surat) {
	myDropzoneUpdate.options.url = ajaxUrl + 'json_upload/' + id_surat + '?mode=advance';
	$('#modal_upload').modal({
		backdrop: 'static',
		keyboard: false
	}, 'show');
}

function uploadFile() {
	$('.btn_close_modal').addClass('hide');
	$('.se-pre-con').show();
	$('.form-control-danger').removeClass('form-control-danger');

	if (typeof myDropzoneUpdate != 'undefined') {
		if (!$.isEmptyObject(myDropzoneUpdate.files)) {
			fileCounter = myDropzoneUpdate.files.length;
			myDropzoneUpdate.processQueue();
			myDropzoneUpdate.on("complete", function (file) {
				fileCounter--;
				if (file.status != 'success') {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
						laddaButtonUpload.stop();
					}

					Swal.fire('Maaf', 'Terjadi kesalahan koneksi', 'error');
				} else {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
						$('#modal_upload').modal('hide');
						Swal.fire('Ok', 'Data berhasil disimpan', 'success');
						laddaButtonUpload.stop();
					}
				}
				this.removeFile(file);
			});
		} else {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();
			$('.btn_close_modal').removeClass('hide');
			$('#modal_upload').modal('hide');
			Swal.fire('Ok', 'Data berhasil disimpan', 'success');
			laddaButtonUpload.stop();
		}
	} else {
		window.onbeforeunload = false;
		$('.se-pre-con').hide();
		$('.btn_close_modal').removeClass('hide');
		$('#modal_upload').modal('hide');
		Swal.fire('Ok', 'Data berhasil disimpan', 'success');
		laddaButtonUpload.stop();
	}
}

function getHistoryRollback(id_surat) {
	$('#modal_history_rollback').modal({
		backdrop: 'static',
		keyboard: false
	}, 'show');

	let urlHistory = ajaxUrl + 'json_grid_history_rollback/' + id_surat;

	if (typeof tableHistoryRollback !== 'undefined') {
		tableHistoryRollback.ajax.url(urlHistory).load();
	} else {
		tableHistoryRollback = $('#table-history-rollback').DataTable({
			"aLengthMenu": [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
			],
			"bStateSave": false,
			"bDestroy": true,
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: urlHistory,
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				beforeSend: function () {
					$('.se-pre-con').show();
				},
				error: function (response) {

					window.onbeforeunload = false;
					$('.btn_close_modal').removeClass('hide');
					$('.se-pre-con').hide();

					swalErrorLog(response);
				},
				complete: function () {
					$('.se-pre-con').hide();
				}
			},
			"sPaginationType": "full_numbers",
			"aoColumns": [{
				"mData": "id_history"
			},
			{
				"mData": "nama_jabatan"
			},
			{
				"mData": "keterangan",
				'defaultContent': '-'
			},
			{
				"mData": "tanggal"
			},
			{
				"mData": "batch"
			},
			{
				"mData": "id_history"
			}
			],
			"aaSorting": [
				[3, 'desc']
			],
			"lengthMenu": [10, 25, 50, 75, 100],
			"pageLength": 10,
			"aoColumnDefs": [{
				"aTargets": [0],
				"mData": "id_history",
				"mRender": function (data, type, full, draw) {
					var row = draw.row;
					var start = draw.settings._iDisplayStart;
					var length = draw.settings._iDisplayLength;

					var counter = (start + 1 + row);

					return counter;
				}
			},
			{
				"aTargets": [3],
				"mData": "tanggal",
				"mRender": function (data, type, full) {
					let tanggal = '';

					if (!$.isEmptyObject(full.tanggal)) {
						tanggal = getDisplayDate(full.tanggal, false, 'd F Y');
					}

					return tanggal;
				}
			},
			{
				"aTargets": [5],
				"mData": "id_history",
				"mRender": function (data, type, full) {
					var btn_action = '';

					if (advanceMode == 't') {
						btn_action = '<button type="button" onclick="loadFileRollback(\'' + full.id_history + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Load File"><i class="la la-file"></i></button>';

						btn_action += ' <button type="button" onclick="newTab(\'' + ajaxUrl + 'form_history_rollback/' + full.id_history + '?code=iuvadfb\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Upload File"><i class="la la-upload"></i></button>';
					}

					return btn_action;
				}
			}
			],
			"fnHeaderCallback": function (nHead, aData, iStart, iEnd, aiDisplay) {
				$(nHead).children('th:nth-child(1), th:nth-child(3), th:nth-child(4)').addClass('text-center');
			},
			"fnFooterCallback": function (nFoot, aData, iStart, iEnd, aiDisplay) {
				$(nFoot).children('th:nth-child(1), th:nth-child(3), th:nth-child(4)').addClass('text-center');
			},
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).children('td:nth-child(1),td:nth-child(3),td:nth-child(4),td:nth-child(5)').addClass('text-center');
				$(nRow).children('td:nth-child(2)').addClass('align-left');
			}
		});
	}
}

function loadFileRollback(id_history) {
	$.ajax({
		type: "GET",
		url: ajaxUrl + "json_file_history_rollback/" + id_history,
		beforeSend: () => {
			preventLeaving();
			$('.btn_close_modal').addClass('hide');
			$('.se-pre-con').show();
			$('#list-file-history').html('');
		},
		success: (response) => {
			window.onbeforeunload = false;
			$('.btn_close_modal').removeClass('hide');
			$('.se-pre-con').hide();

			var obj = response;

			if (obj.status == "OK") {
				if (obj.data.file.length > 0) {
					for (var i = 0; i < obj.data.file.length; i++) {
						let rowFile = obj.data.file[i];

						let template = '<a href="' + rowFile['url'] + '" class="btn btn-success list-history-' + id_history + ' fancybox fancybox-a" style="display:none;" data-fancybox="gallery-history-' + id_history + '" data-caption="Halaman ' + rowFile['page'] + '">Lihat file</a>';

						$('#list-file-history').append(template);
					}

					$('.list-history-' + id_history).eq(0).click();
				} else {
					Swal.fire('Pemberitahuan', 'File tidak tersedia', 'warning');
				}
			} else {
				Swal.fire('Pemberitahuan', obj.message, 'warning');
			}
		},
		error: (response) => {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();

			swalErrorLog(response);
		}
	});
}
// end : advance mode

function swalErrorLog(response) {
	var head = 'Maaf',
		message = 'Terjadi kesalahan koneksi',
		type = 'error';

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

function newTab(url) {
	window.open(url, '_blank');
}

function unggah_surat_selesai(id) {
	$('#btn_upload_selesai').attr("onclick", "simpan_surat_selesai(\"" + id + "\")");
	$('#modal_surat_selesai').modal('show')
}

function simpan_surat_selesai(id) {
	myDropzoneSelesai.options.url = ajaxUrl + 'json_upload_selesai/' + id;
	dropzone_surat_selesai();
}


function dropzone_surat_selesai() {
	$('.btn_close_modal').addClass('hide');
	$('.se-pre-con').show();
	$('.form-control-danger').removeClass('form-control-danger');

	if (typeof myDropzoneSelesai != 'undefined') {
		if (!$.isEmptyObject(myDropzoneSelesai.files)) {
			fileCounter = myDropzoneSelesai.files.length;
			myDropzoneSelesai.processQueue();
			myDropzoneSelesai.on("complete", function (file) {
				fileCounter--;
				if (file.status != 'success') {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
					}

					Swal.fire('Maaf', 'Terjadi kesalahan koneksi', 'error');
				} else {
					if (fileCounter == 0) {
						window.onbeforeunload = false;
						$('.se-pre-con').hide();
						$('.btn_close_modal').removeClass('hide');
						$('#modal_surat_selesai').modal('hide');
						load_table()
						quickStats();
						Swal.fire('Ok', 'Data berhasil disimpan', 'success');
					}
				}
				this.removeFile(file);
			});
		} else {
			window.onbeforeunload = false;
			$('.se-pre-con').hide();
			$('.btn_close_modal').removeClass('hide');
			load_table()
			quickStats();
			Swal.fire('Warning', 'Pastikan file tercantum', 'warning');
		}
	} else {
		window.onbeforeunload = false;
		$('.se-pre-con').hide();
		$('.btn_close_modal').removeClass('hide');
		$('#modal_surat_selesai').modal('hide');
		load_table()
		quickStats();
		Swal.fire('Ok', 'Data berhasil disimpan', 'success');
	}
}

function modalDetailPenomoran(id) {
	clear()
	$.ajax({
		type: "GET",
		url: baseUrl('surat') + "json_penomoran/"+id,
		beforeSend: function () {
			$('.se-pre-con').show();
		},
		success: (response) => {
			let res = response.data
			$('.se-pre-con').hide();
			$('#xa-tanggal').html(tanggalStandard(res.tanggal))
			if(res.nomor_surat == null) {
				$('#xa-edit-nomor-surat').show()
			} else {
				$('#xa-edit-nomor-surat').hide()
				$('#xa-nomor-surat').html(res.nomor_surat)
			}
			$('#xa-judul-surat').html(res.judul)
			$('#xa-pengirim').html(res.nama_pegawai)
			$('#xa-file-surat').html('<a target="_blank" href="'+baseUrl('watch') + res.judul + '?un=' + id + '&ct=surat&src=' + res.path_file+'">'+res.judul+'</a>')
			$('#modal_detail_penomoran').modal({ backdrop: 'static', keyboard: false }, 'show');
			$('#xa-edit-nomor-surat').attr('onclick','modalPenomoran(\''+id+'\')')
		},
		error: (response) => {
			$('.se-pre-con').hide();
			swalErrorLog(response);
		}
	});
}

function tanggalStandard(tg) {
    let tgl = new Date(tg);
    let tanggal = (tgl.getDate() < 10 ? '0' : '') + tgl.getDate();
    let bulan = ((tgl.getMonth() + 1) < 10 ? '0' : '') + (tgl.getMonth() + 1);
    let tahun = tgl.getFullYear();
    val = tanggal + "-" + bulan + "-" + tahun;
    return val
}

function modalPenomoran(id_surat) {
	clearForm()
	$('#penomoran-nomor-surat').val('')
	$('#penomoran-id-surat').val(id_surat)
	getLetterNumberToday()
	$('#modal_penomoran').modal({backdrop: 'static', keyboard: false}, 'show');
	penomoranSuratID = id_surat
}

function pilihTipe(type){
    if(type == 1) {
        $('#val-tipe').val(1)
        $('#btn-tipe-pilih-nomor').attr('class','')
        $('#btn-tipe-pilih-nomor').attr('class','btn btn-success')
        $('#btn-tipe-hari-ini').attr('class','')
		$('#btn-tipe-hari-ini').attr('class','btn btn-outline-success')
		$('.show-form-number').show()
		$('.show-form-today').hide()
    } else {
        $('#val-tipe').val(2)
        $('#btn-tipe-hari-ini').attr('class','')
        $('#btn-tipe-hari-ini').attr('class','btn btn-success')
        $('#btn-tipe-pilih-nomor').attr('class','')
        $('#btn-tipe-pilih-nomor').attr('class','btn btn-outline-success')
		$('.show-form-number').hide()
		$('.show-form-today').show()
    }
}

function getLetterNumberToday() {
	var id_surat = $('#penomoran-id-surat').val()
	$.ajax({
		type: "POST",
		url: baseUrl('penomoran') + "get_letter_number_today",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {
			id_surat: id_surat
        },
		dataType: "json",
		beforeSend: function () {
			$('.se-pre-con').show();
		},
		success: (response) => {
			$('.se-pre-con').hide();
			$('#penomoran-nomor-surat').val(response.data)

			if (response.data == '-'){
				$('#penomoran-nomor-surat').attr("disabled", true);
			} else {
				$('#penomoran-nomor-surat').removeAttr("disabled");
			}
		},
		error: (response) => {
			$('.se-pre-con').hide();
		}
	});
}

function getDetailPenomoran() {
	$.ajax({
		type: "GET",
		url: baseUrl('penomoran') + "get_letter_number_today",
		beforeSend: function () {
			$('.se-pre-con').show();
		},
		success: (response) => {
			$('.se-pre-con').hide();
			$('#penomoran-nomor-surat').val(response.data)
		},
		error: (response) => {
			$('.se-pre-con').hide();
		}
	});
}

function tanggalHariIni() {
    let val = new String();
	let tgl = new Date();
    let tanggal = (tgl.getDate() < 10 ? '0' : '') + tgl.getDate();
    let bulan = ((tgl.getMonth() + 1) < 10 ? '0' : '') + (tgl.getMonth() + 1);
    let tahun = tgl.getFullYear();
    val = tanggal + "-" + bulan + "-" + tahun;
    return val
}



function load_nomor(type, place_holder, tanggal, id_sektor) {
		$('#penomoran-nomor-surat').removeAttr("disabled");
    $(".list_" + type).select2({
      dropdownParent: $("#form-penomoran"),
      placeholder: "Pilih " + place_holder,
      ajax: {
        url: baseUrl('penomoran') + 'get_letter_number_by_date',
        dataType: "json",
        type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
        quietMillis: 50,
        data: function (params) {
          search = params.term;
          var query = {
            search: search,
			tanggal: tanggal,
			id_surat: penomoranSuratID
          };
          return query;
        },
        processResults: function (data) {
		// 	let comma = ','
		// 	let detail = ``
		// 	$.each(data.data,function(index, value){
		// 		$.each(value.name.number,function(index1, value1){
		// 			let letterCode = (value.name.letter_code == null) ? "(-)"+value1 : "("+value.name.letter_code+") "+value1
		// 			detail += `{"id" : "`+value1+`","name":"`+letterCode+`"}`+comma
		// 		})
		// 	})
		// 	let dataJson = JSON.parse('['+detail.substring(0, detail.length - 1)+']')
          return {
            results: $.map(data.data, function (item) {
				return {
					id: item.id,
					text: item.name
				};
            }),
          };
        },
      },
    });
}

function load_sektor(type, place_holder, url) {
    $(".list_" + type).select2({
      dropdownParent: $("#form-penomoran"),
      placeholder: "Pilih " + place_holder,
      ajax: {
        url: baseUrl('penomoran') + url,
        dataType: "json",
        type: "GET",
        quietMillis: 50,
        data: function (params) {
          search = params.term;
          var query = {
            search: search,
          };
          return query;
        },
        processResults: function (data) {
			return {
				results: $.map(data.data, function (item) {
				return {
					text: item.id.name,
					id: item.id.id,
				};
				}),
			};
        },
      },
    });
}

function saveSuratPenomoran(){
    var data = $("#form-penomoran").serializeArray();
	$.ajax({
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: baseUrl('surat') + "save_surat_penomoran",
		data: {
			id_surat: penomoranSuratID,
			tipe: $('#val-tipe').val(),
			nomor_surat: ($('#val-tipe').val() == 2) ? $('#penomoran-nomor-surat').val() : $('#nomor-penomoran').val(),
			tanggal_penomoran: $('#penomoran-tanggal').val()
		},
		beforeSend: function () {
			$('.se-pre-con').show();
		},
		success: function (response) {
			$('.se-pre-con').hide();
			$('.dataTable').each(function() {
				dt = $(this).dataTable();
				dt.fnDraw();
			})
			$('#modal_penomoran').modal('hide')
			$('#xa-edit-nomor-surat').hide()
			$('#xa-nomor-surat').html(response.data.number)
			Swal.fire('Berhasil', 'Data berhasil disimpan', 'success')
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$('.se-pre-con').hide();
			var status = xhr.status
			if(status == 400){
				var res = xhr.responseJSON.data.error_log
				var html = ''
				$.each(res, function (index, value) {
					html += '<label>'+value+'</label><br>'
				})
				Swal.fire('Oopss...', html, 'error')
			} else if (status == 401) {
				var html = xhr.responseJSON.message
				Swal.fire('Oopss...', html, 'error')
			} else {
				Swal.fire('Oopss...', 'Terjadi kesalahan koneksi', 'error')
			}
		}
	});
}

function clear() {
	$('#xa-tanggal').html('-')
	$('#xa-nomor-surat').html('-')
	$('#xa-judul-surat').html('-')
	$('#xa-pengirim').html('-')
	$('#xa-file-surat').html('-')
}

function clearForm() {
	$('#penomoran-tanggal').val('')
	$('#nomor-penomoran').val('').change()
	$('#penomoran-nomor-surat').val('')
	pilihTipe(2)
	load_nomor('nomor_penomoran', 'Nomor Surat', '10-10-1000', $('#sector').val());
	load_sektor('sector', 'Sektor', 'get_sector');
}
