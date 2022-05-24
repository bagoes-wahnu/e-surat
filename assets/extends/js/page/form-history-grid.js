var fileCounter = 0,
    ajaxUrl = baseUrl('surat'),
    laddaButtonUpload;

$(document).ready(function () {
    setMenu('#daftarSuratNav');

    $('#btn_upload').on('click', function (e) {
        e.preventDefault();
        laddaButtonUpload = Ladda.create(this);
        laddaButtonUpload.start();
        uploadFile();
    });
});

function uploadFile() {
    let file = new FormData($("#form1")[0]);

    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "json_upload_file_history",
        data: file,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            preventLeaving();
            $('.se-pre-con').show();
        },
        success: function (response) {
            laddaButtonUpload.stop();
            window.onbeforeunload = false;
            $('.se-pre-con').hide();

            var obj = response;

            if (obj.status == "OK") {
                Swal.fire('OK', obj.message, 'success');
                setTimeout(function () {
                    window.location = baseUrl('home');
                }, 1500);
            } else {
                Swal.fire('Pemberitahuan', obj.message, 'warning');
            }
        },
        error: function (response) {
            window.onbeforeunload = false;
            laddaButtonUpload.stop();
            $('.se-pre-con').hide();
            
            swalErrorLog(response);
        }
    });
}

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