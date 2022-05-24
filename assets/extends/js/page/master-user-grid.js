var tableData, myDropzone, fileCounter = 0,
tableTarget = '#table1',
ajaxUrl = baseUrl('master/user'),
ajaxSource = ajaxUrl + 'json_grid',
laddaButtonSetting;

$(document).ready(function () {
    setMenu('#masterUserNav');
    load_table();

    protectString('#judul', 150);
    protectNumber('#total_limit_access', 2);

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

    $('#btn_save_setting').on('click', function (e) {
        e.preventDefault();
        laddaButtonSetting = Ladda.create(this);
        laddaButtonSetting.start();
        simpanKonfigurasi();
    });

    $('.input-user').on("keyup", function (event) {
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
            "mData": "id_user"
        },
        {
            "mData": "nama"
        },
        {
            "mData": "username"
        },
        {
            "mData": "email", "defaultContent" : "-"
        },
        {
            "mData": "role"
        },
        {
            "mData": "aktif"
        },
        {
            "mData": "id_user"
        }
        ],
        "aaSorting": [
        [1, 'asc']
        ],
        "lengthMenu": [10, 25, 50, 75, 100],
        "pageLength": 10,
        "aoColumnDefs": [{
            "aTargets": [0],
            "mData": "id_user",
            "mRender": function (data, type, full, draw) {
                var row = draw.row;
                var start = draw.settings._iDisplayStart;
                var length = draw.settings._iDisplayLength;

                var counter = (start + 1 + row);

                return counter;
            }
        },
        {
            "aTargets": [1],
            "mData": "nama",
            "mRender": function (data, type, full) {
                let nama = full.nama;

                if(full.uptd == true){
                    nama += '<br><span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill kt-badge--rounded kt-font-bold">UPTD</span>';
                }
                return nama;
            }
        },
        {
            "aTargets": [4],
            "mData": "role",
            "mRender": function (data, type, full) {
                return full.nama_role;
            }
        },
        {
            "aTargets": [5],
            "mData": "aktif",
            "mRender": function (data, type, full) {
                let checked ='';
                let disabled ='';

                if(full.aktif == true){
                    checked = 'checked="checked"';
                }

                if(full.role == 1){
                    disabled = 'disabled=""';
                }

                let switchBtn = '<span class="kt-switch kt-switch--success kt-switch--icon"><label><input type="checkbox" '+disabled+' '+checked+' name="aktif" id="aktif-'+full.id_user+'" onchange="switch_status(\''+full.id_user+'\')"><span></span></label></span>';

                return switchBtn;
            }
        },
        {
            "aTargets": [6],
            "mData": "id_user",
            "mRender": function (data, type, full) {
                var btn_action = '\
                <button type="button" onclick="modalSetting(\''+full.id_user + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip"  title="Konfigurasi"><i class="la la-cog"></i></button>';

                if(roleUser == 1){
                    btn_action += ' <button type="button" onclick="resetPassword(\''+full.id_user + '\')" class="btn btn-outline-green-esurat btn-elevate btn-circle btn-icon" data-toggle="kt-tooltip"  title="Reset Password"><i class="la la-refresh"></i></button>';
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
        }
    });
}

function modalSetting(id_user) {
    resetFormSetting();
    $.ajax({
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "json_get/"+id_user,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
            $('#modal_setting').modal('hide');
        },
        success: function (response) {
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();
            
            var obj = response;

            if (obj.status == "OK") {
                $('#id_user_setting').val(id_user);

                let rowData = obj.data.user;

                $('#allow_access').prop('checked', rowData['token_permission']);

                if(rowData['token_limits'] > 0){
                    $('#limit_access').prop('checked', false);
                    $('#total_limit_access').val(rowData['token_limits']);
                }else{
                    $('#limit_access').prop('checked', true);
                    $('#total_limit_access').val('');
                }

                check_limit();

                $('#modal_setting').modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
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
            $('.btn_close_modal').removeClass('hide');
            $('#modal_setting').modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');
            laddaButtonSetting.stop();

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

function simpanKonfigurasi() {
    var data = $("#formSetting").serializeArray();
    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "json_save_setting",
        data: data,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
            $('#modal_setting').modal('hide');
        },
        success: function (response) {
            laddaButtonSetting.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            var obj = response;

            if (obj.status == "OK") {
                Swal.fire('Ok', obj.message, 'success');
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
            $('.btn_close_modal').removeClass('hide');
            $('#modal_setting').modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');
            laddaButtonSetting.stop();

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

function switch_status(id_user='') {
    var aktif = $('#aktif-'+id_user).prop('checked');

    if(aktif == true){
        aktif = 't';
    }else{
        aktif = 'f';
    }

    $.ajax({
        type:"POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl+"json_set_status",
        data:{id_user:id_user, aktif:aktif},
        beforeSend: function() {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success:function(response){
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            var obj = response;

            if(obj.status == "OK"){
                toastr.success(obj.message);
            } else {
                Swal.fire('Pemberitahuan', obj.message, 'warning');

                if(aktif == 't'){
                    $('#aktif-'+id_user).prop('checked', true);
                }else{
                    $('#aktif-'+id_user).prop('checked', false);
                }
            }

        },
        error: function(response) {
            var head = 'Maaf', message = 'Terjadi kesalahan koneksi', type = 'error';
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            if(aktif == 't'){
                $('#aktif-'+id_user).prop('checked', true);
            }else{
                $('#aktif-'+id_user).prop('checked', false);
            }

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

function check_limit() {
    let limit_access = $('#limit_access').prop('checked');

    if(limit_access == true){
        $('#total_limit_access').prop('disabled', true);
    }else{
        $('#total_limit_access').prop('disabled', false);
    }
}

function resetPassword(id_user) {
    Swal.fire({
        title: "Apakah Anda yakin ingin mereset password?",
        text: "NB: Jika Anda sudah mereset password maka password Anda akan diset ke default",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
    })
    .then((result) => {
        if (result.value) {
            $.ajax({
                beforeSend: () => {
                    preventLeaving();
                    $('.btn_close_modal').addClass('hide');
                    $('.se-pre-con').show();
                },
                url:ajaxUrl+"json_reset_password/"+id_user,
                success: (response) => {
                    window.onbeforeunload = false;
                    $('.btn_close_modal').removeClass('hide');
                    $('.se-pre-con').hide();

                    var obj = response;

                    if(obj.status == "OK"){
                        toastr.success(obj.message);
                    } else {
                        Swal.fire('Pemberitahuan', obj.message, 'warning');

                        if(aktif == 't'){
                            $('#aktif-'+id_user).prop('checked', true);
                        }else{
                            $('#aktif-'+id_user).prop('checked', false);
                        }
                    }
                },
                error:(response) => {
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

function resetFormSetting() {
    $('#id_user_setting').val('');
    $('#id_user_setting').change();
    $('#allow_access').prop('checked', false);
    $('#limit_access').prop('checked', false);
    $('#total_limit_access').val('');
    $('#total_limit_access').change();
}