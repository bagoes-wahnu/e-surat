var tableData, fileCounter = 0, isRevise = false,
tableTarget = '#table1',
ajaxUrl = baseUrl('arsip-surat'),
ajaxSource = ajaxUrl + 'json_grid',
arrPejabat = {1 : {name:'Kadis', type:'bg-green-custom'}, 2 : {name:'Sekretaris', type:'kt-badge--warning'}, 3 : {name:'UPTD', type:'kt-badge--info'}};

$(document).ready(function () {
	setMenu('#daftarArsipSuratNav');
	load_table();

	$('#jenis_surat').selectpicker();

	protectString('#pegawai', 150);
	protectString('#judul', 150);

	if (typeof tableData !== 'undefined') {
		tableData.on('draw.dt', function () {
			$('.se-pre-con').hide();

			$('[data-toggle=kt-tooltip]').tooltip({
				trigger : 'hover'
			});

			$('[data-toggle=kt-tooltip]').on('click', function () {
				$(this).tooltip('hide')
			})
		});
	}
});

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
			"mData": "jenis", 'defaultContent' : '-'
		},
		{
			"mData": "tanggal"
		},
		{
			"mData": "nama_pegawai", 'defaultContent' : '-'
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
		[1, 'desc']
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

				if(!$.isEmptyObject(full.tanggal)){
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

				pejabat = '<span class="kt-badge '+arrPejabat[index].type+' kt-badge--dot"></span>&nbsp;\
				<span class="kt-font-bold">'+arrPejabat[index].name+'</span>';

				return pejabat;
			}
		},
		{
			"aTargets": [6],
			"mData": "status",
			"mRender": function (data, type, full) {
				let status = '';

				if (full.state == 1) {
					if(full.rollback == true){
						status = '<span class="kt-badge bg-orange-dark-esurat kt-badge--inline kt-badge--pill">Rollback</span>';
					}else{
						status = '<span class="kt-badge bg-gray-custom kt-badge--inline kt-badge--pill">Waiting'+( (full.id_jabatan == idJabatan)? ' You' : '' )+'</span>';
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
				var btn_action='';

				pattern = /['"/]/g;
				let nama_file = full.judul.replace(pattern, '-');

				btn_action += '\
				<a href="' + baseUrl() + 'watch/' + nama_file + '?un=' + full.id_surat + '&ct=surat&src=' + full.path_file + '" target="_blank" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip"  title="Lihat"><i class="la la-eye"></i></a>\
				<button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" onclick="showTimeline(\''+full.id_surat+'\')" title="Timeline"><i class="la la-calendar"></i></button>';

				if(!$.isEmptyObject(full.tanggal_approve) && gradeUser == lastGrade){
					if (parseInt(full.portrait) > 0) {
						btn_action += ' <a href="' + ajaxUrl + 'v/' + full.id_surat + '?orientation=portrait" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" target="_blank" title="Atur Tanda Tangan Portrait"><i class="la la-arrows"></i></a>';
					}

					if (parseInt(full.landscape) > 0) {
						btn_action += ' <a href="' + ajaxUrl + 'v/' + full.id_surat + '?orientation=landscape" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" target="_blank" title="Atur Tanda Tangan Landscape"><i class="la la-arrows"></i></a>';
					}

					if (parseInt(full.portrait) <= 0 && parseInt(full.landscape) <= 0) {
						btn_action += ' <a href="' + ajaxUrl + 'v/' + full.id_surat + '?orientation=default" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" target="_blank" title="Atur Tanda Tangan"><i class="la la-arrows"></i></a>';
					}

					if (full.srs_srt_id == null) {
						btn_action += ' <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" onclick="unggah_surat_selesai(\'' + full.id_surat + '\')" title="Unggah Surat Selesai"><i class="la la-upload"></i></button>';
					} else {
						btn_action += ' <a target="_blank" href="' + baseUrl() + 'watch/' + full.judul + '?un=' + full.id_surat + '&ct=surat_selesai&src=' + full.srs_path_file + '"><button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Lihat Surat Selesai"><i class="la la-file"></i></button></a>';
					}
				}

				if (full.state == 3) {
					btn_action += ' <a href="" id="kt_sweetalert_delete" class=" btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip" data-placement="top" title="Download"><i class="la la-download"></i></a>';
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

function showTimeline(id_surat) {
	$.ajax({
		type:"POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: ajaxUrl+"json_timeline",
		data: {id_surat:id_surat},
		beforeSend: function() {
			preventLeaving();
			$('.se-pre-con').show();
			$('.timeline-surat').remove();
		},
		success:function(response){
			window.onbeforeunload = false;


			var obj = response;

			if(obj.status == "OK"){
				for (var i = 0; i < obj.data.timeline.length; i++) {
					let rowData = obj.data.timeline[i];
					let template = '<span class="kt-timeline-v2__item-time">'+getDisplayDate(rowData['tanggal_input'], false, 'd F Y')+'<br><span class="pull-right">'+advanceDateFormat(rowData['tanggal_input'], 'H:i')+'</span></span>\
					<div class="kt-timeline-v2__item-cricle">\
					<i class="fa fa-genderless kt-font-success"></i>\
					</div>\
					<div class="kt-timeline-v2__item-text font-boldd black kt-padding-top-5">'+rowData['keterangan']+'</div>';

					if($('#timeline-'+rowData['id_timeline']).length > 0){
						$('#timeline-'+rowData['id_timeline']).html(template);
					}else{
						template = '<div class="kt-timeline-v2__item timeline-surat" id="timeline-'+rowData['id_timeline']+'">'+template+'</div>';
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