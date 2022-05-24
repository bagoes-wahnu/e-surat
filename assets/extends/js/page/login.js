var username, password, btn_loader;

$(document).ready(function(){
	$('#btn_login').click(function(e){
		username = $("#username").val();
		password = $("#password").val();

		if(username == "" || password == ""){
			Swal.fire("Oopss...","Form login tidak boleh kosong!", 'warning');
		}else{
			e.preventDefault();
			btn_loader = Ladda.create(this);
			btn_loader.start();

			setTimeout(function(){
				login();
			},1200);
		}
	});

	$('.form-submit').on("keyup", function(event) {
		event.preventDefault();
		if (event.keyCode === 13) {
			$("#btn_login").click();
		}
	});
});


function login() {
	$.ajax({
		type : "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data : {username:username,password:password},
		url : "authenticate",
		success : function(response){

			if(response.code == 200){
				if($('#url_source').val() == ''){
					document.location = baseUrl;
				}else{
					document.location = $('#url_source').val();
				}
			}else{
				btn_loader.stop();
				Swal.fire("Pemberitahuan!",response.message, 'warning');
			}
		},
		error : function(response){
			btn_loader.stop();
			var title = 'Terjadi Kesalahan!', message = 'Koneksi Error!!!', type = 'error';
			
			if(response['status'] == 419){
				location.reload();
			}else{
				if(!$.isEmptyObject(response.responseJSON.message)){
					title = 'Pemberitahuan!';
					message = response.responseJSON.message;
					type = 'warning';
				}

				Swal.fire(title, message, type);
			}
		}
	});
}

function showHidePassword() {
	var type = $('#password').attr('type');

	if(type == 'password'){
		$('#password').attr('type', 'text');
	}else{
		$('#password').attr('type', 'password');
	}
}