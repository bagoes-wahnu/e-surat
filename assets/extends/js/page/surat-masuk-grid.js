var tableData, myDropzone, fileCounter = 0, isFileRequired=false,
tableTarget = '#table1',
ajaxUrl = baseUrl('surat-masuk'),
ajaxSource = ajaxUrl + 'json_grid',
laddaButton;

$(document).ready(function () {
    setMenu('#daftarSuratMasukNav');
    load_table();
    init_dropzone();

    protectString('#judul', 150);

    $('#tanggal').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true
    });

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

    $('#btn_save').on('click', function (e) {
        e.preventDefault();
        laddaButton = Ladda.create(this);
        laddaButton.start();
        simpan();
    });

    $('.input-surat-masuk').on("keyup", function (event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            $("#btn_save").click();
        }
    });
});

function load_table() {
    tableData = $(tableTarget).DataTable({
        "aLengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
        ],
        "bStateSave": false,
        "bDestroy": true,
        "processing": false,
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
            "mData": "id_surat_masuk"
        },
        {
            "mData": "no_surat"
        },
        {
            "mData": "judul"
        },
        {
            "mData": "tanggal"
        },
        {
            "mData": "pengirim"
        },
        {
            "mData": "id_surat_masuk"
        }
        ],
        "aaSorting": [
        [1, 'desc']
        ],
        "lengthMenu": [10, 25, 50, 75, 100],
        "pageLength": 10,
        "aoColumnDefs": [{
            "aTargets": [0],
            "mData": "id_surat_masuk",
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
                    tanggal = advanceDateFormat(full.tanggal, 'd F Y');
                }

                return tanggal;
            }
        },
        {
            "aTargets": [5],
            "mData": "id_surat_masuk",
            "mRender": function (data, type, full) {
                var btn_action='';

                btn_action += '\
                <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Edit" data-togle="kt-tooltip" onclick="edit(\''+full.id_surat_masuk+'\')">\
                <i class="la la-edit"></i>\
                </button>\
                <button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Lihat" data-togle="kt-tooltip" onclick="detail(\''+full.id_surat_masuk+'\')">\
                <i class="la la-eye"></i>\
                </button>'
                /*<button type="button" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" title="Hapus" data-togle="kt-tooltip" onclick="hapusFile(\''+full.id_surat_masuk+'\')">\
                <i class="fa fa-trash-alt"></i>\
                </button>'*/
                ;

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
            $(nRow).children('td:nth-child(1),td:nth-child(3),td:nth-child(4),td:nth-child(6)').addClass('text-center');
            $(nRow).children('td:nth-child(2)').addClass('align-left');
        }
    });

/*$(tableTarget).closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
    minimumResultsForSearch: -1
});*/
}

function tambah() {
    resetForm();
    $('#id_surat_masuk').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Surat Masuk');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait Surat Masuk.');

    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
}

function edit(id_surat_masuk) {
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Surat Masuk');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data Surat Masuk sesuai kebutuhan.');

    $.ajax({
        type:"GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl+"json_get/"+id_surat_masuk,
        beforeSend: function() {
            preventLeaving();
            resetForm();
            $('.field-file').hide();
            isFileRequired = false;
            $('.se-pre-con').show();
        },
        success:function(response){
            window.onbeforeunload = false;

            var obj = response;

            if(obj.status == "OK"){
                let rowData = obj.data.surat_masuk;
                $('#id_surat_masuk').val(id_surat_masuk);

                $('#no_surat').val(rowData['no_surat']);
                $('#judul').val(rowData['judul']);
                $('#tanggal').datepicker('setDate', advanceDateFormat(rowData['tanggal'], 'd/m/Y'));
                $('#pengirim').val(rowData['pengirim']);

                // if($.isEmptyObject(rowData['path_file'])){
                //     isFileRequired = true;
                // }

                if(!$.isEmptyObject(rowData['path_file'])){
                    $('.field-file').html('<br><br><a target="_blank" href="'+baseUrl() + 'watch/' + rowData['judul'] + '?un=' + rowData['id_surat_masuk'] + '&ct=surat_masuk&src=' + rowData['path_file']+'" class="kt-link kt--font-boldest kt-link--state kt-link--primary fancybox fancybox-effects-a" data-fancybox="file-surat-masuk" data-caption="'+rowData['judul']+'" style="font-size: 1.3em !important;">File saat ini</a>\
                        &nbsp;<button type="button" class="btn btn-danger btn-icon btn-sm" style="border-color: #f4516c;background-color: #f4516c;" onclick="hapusFile(\''+id_surat_masuk+'\')"><i class="la la-trash-o"></i></button>');

                    $('.field-file').show();
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

function detail(id_surat_masuk) {
    $.ajax({
        type:"GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl+"json_get/"+id_surat_masuk,
        beforeSend: function() {
            preventLeaving();
            resetForm();
            $('.field-detail').html('-');
            $('.se-pre-con').show();
        },
        success:function(response){
            window.onbeforeunload = false;

            var obj = response;

            if(obj.status == "OK"){
                let rowData = obj.data.surat_masuk;

                $('#detail_no_surat').html(helpEmpty(rowData['no_surat'], '-'));
                $('#detail_judul').html(helpEmpty(rowData['judul'], '-'));
                $('#detail_tanggal').html(helpEmpty(advanceDateFormat(rowData['tanggal'], 'd/m/Y'), '-'));
                $('#detail_pengirim').html(helpEmpty(rowData['pengirim'], '-'));

                if(!$.isEmptyObject(rowData['path_file'])){
                    $('#detail_file').html('<a target="_blank" href="'+baseUrl() + 'watch/' + rowData['judul'] + '?un=' + rowData['id_surat_masuk'] + '&ct=surat_masuk&src=' + rowData['path_file']+'" class="kt-link kt--font-boldest kt-link--state kt-link--primary fancybox fancybox-effects-a" data-fancybox="file-surat-masuk" data-caption="'+rowData['judul']+'">'+rowData['judul']+'.pdf</a>');
                }

                $('.se-pre-con').hide();
                $('#modal_detail').modal({
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

    /*if ( ((isFileRequired == true) && (file > 0)) || (isFileRequired == false) ) {*/
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
                        myDropzone.options.url = ajaxUrl + 'json_upload/' + obj.data.surat_masuk.id_surat_masuk;
                        dropzone_exec();
                    }else{
                        $('.se-pre-con').hide();
                        Swal.fire('Ok', 'Data berhasil disimpan', 'success');
                        laddaButton.stop();
                        tableData.ajax.reload();
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
/*}else{
    laddaButton.stop();
    Swal.fire('Pemberitahuan', 'Berkas wajib diisi!', 'warning');
}*/
}

function resetForm(method = '') {
    $('#id_surat_masuk').val('');
    $('#id_surat_masuk').change();
    $('#no_surat').val('');
    $('#no_surat').change();
    $('#judul').val('');
    $('#judul').change();
    $('#tanggal').datepicker('setDate', null);
    $('.field-file').html('');
    myDropzone.removeAllFiles(true);
}

function init_dropzone() {
    myDropzone = new Dropzone("#dropzone_file", {
        url: ajaxUrl + "upload_file",
        dictCancelUpload: "Cancel",
        maxFilesize: 5,
        parallelUploads: 1,
        maxFiles: 1,
        addRemoveLinks: true,
        acceptedFiles: '.pdf',
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
                        tableData.ajax.reload();
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
                        tableData.ajax.reload();
                    }
                }
                this.removeFile(file);
            });
        } else {
            window.onbeforeunload = false;
            $('.se-pre-con').hide();
            $('.btn_close_modal').removeClass('hide');
            $('#modal_form').modal('hide');
            Swal.fire('Ok', 'Data berhasil disimpan', 'success');
            laddaButton.stop();
            tableData.ajax.reload();
        }
    } else {
        window.onbeforeunload = false;
        $('.se-pre-con').hide();
        $('.btn_close_modal').removeClass('hide');
        $('#modal_form').modal('hide');
        Swal.fire('Ok', 'Data berhasil disimpan', 'success');
        laddaButton.stop();
        tableData.ajax.reload();
    }
}

function hapusFile(id_surat_masuk) {
    Swal.fire({
        title: "Konfirmasi?",
        text: "Apakah Anda yakin akan menghapus file ini?",
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
                url: ajaxUrl+"json_remove_file",
                data: {id_surat_masuk:id_surat_masuk},
                beforeSend: function() {
                    preventLeaving();
                    $('.se-pre-con').show();
                },
                success:function(response){
                    window.onbeforeunload = false;
                    $('.se-pre-con').hide();

                    var obj = response;

                    if(obj.status == "OK"){
                        $('.field-file').html('');
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
    });
}