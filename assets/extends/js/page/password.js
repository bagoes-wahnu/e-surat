var ajaxUrlPassword = baseUrl('password'),
laddaButtonPassword;

$(document).ready(function() {
    $('#btn_save_password').on('click', function(e){
        e.preventDefault();
        laddaButtonPassword = Ladda.create(this);
        laddaButtonPassword.start();
        $('#id_user').val(idUser);
        simpan_password();
    });

    $('.input-password').on("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            $("#btn_save_password").click();
        }
    });
});

function modal_password() {
    reset_form_password();
    $('#id_user').val(idUser);
    $('#btn_save_password').html('Simpan');
    $('#modal_password').modal({backdrop:'static', keyboard:false}, 'show');
}

function simpan_password() {
    // var file = new FormData($("#form1")[0]);
    var data = $("#formPassword").serializeArray();
    $.ajax({
        type:"POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrlPassword+"json_save",
        data:data,
        // contentType: false,
        // cache: false,
        // processData:false,
        beforeSend: function() {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success:function(response){
            laddaButtonPassword.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            var obj = response;

            if(obj.status == "OK"){
                Swal.fire('Ok', obj.message, 'success');
                $('#modal_password').modal('hide');
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
            laddaButtonPassword.stop();

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

function reset_form_password(method='') {
    $('#pasword_lama').val('');
    $('#pasword_lama').change();
    $('#password_baru').val('');
    $('#password_baru').change();
    $('#conf_password_baru').val('');
    $('#conf_password_baru').change();
}