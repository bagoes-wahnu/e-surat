<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="403 Forbidden Page">
	<meta name="keywords" content="Forbidden Page">
	<title>403 Forbidden Access</title>
	<link rel="icon" href="{{ app_info('icon') }}">
	<link rel="stylesheet" href="{{asset('/')}}assets/extends/403/css/vendor.bundle.css">
	<link rel="stylesheet" href="{{asset('/')}}assets/extends/403/css/app.bundle.css">
	<link rel="stylesheet" href="{{asset('/')}}assets/extends/403/css/theme-a.css">
	<link rel="stylesheet" href="{{asset('/')}}assets/extends/403/css/custom.css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:500,600" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,500" rel="stylesheet">
</head>

<body>
	<div id="content" class="container" style="text-align: center;">
		<img src="{{asset('/')}}assets/extends/403/img/gambar_atas.png">
		<p class="fields">
			Halaman yang Anda tuju tidak dapat diakses, dikarenakan oleh kesalahan pengaturan hak akses. Silahkan hubungi pihak terkait aplikasi untuk masalah ini, terimakasih semoga harimu menyenangkan.
		</p>
		<a href="{{url('/')}}" class="btn btn-info oke">KEMBALI KE MENU UTAMA</a>
    </div>

    <div class="bawah">
		<div class="isi">
			Mohon Maaf <span>Anda Tidak Memiliki Hak Untuk Mengakses Halaman Tersebut</span> - Energeek The E-Government Solution 
		</div>
	</div>
</body>

<script src="{{asset('/')}}assets/extends/403/js/vendor.bundle.js"></script>
<script src="{{asset('/')}}assets/extends/403/js/app.bundle.js"></script>

</html>