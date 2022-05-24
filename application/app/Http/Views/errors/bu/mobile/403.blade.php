<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{app_info('description')}}" />
    <meta name="author" content="{{app_info('vendor.company')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <link rel="icon" href="{{ app_info('icon') }}">

    <title>403 | Akses Ditolak</title>
</head>

<link href="https://fonts.googleapis.com/css?family=Questrial&display=swap" rel="stylesheet">


<style>
    body {
        font-family: 'Questrial', sans-serif;
    }
</style>

<link rel="stylesheet" href="{{asset('assets/extends/403-mobile/materialize403-mobile.css')}}">
<link rel="stylesheet" href="{{asset('assets/extends/403-mobile/custom.css')}}">

<body>
    <div class="center-screen" style="margin-top: -20px">
        <div class="col s12 m12">
            <div class="card">
                <div class="card-image" style="padding:60px; margin-bottom: -20px">
                    <img src="{{asset('assets/extends/403-mobile/403-mobile.png')}}">
                    <h4>Akses Ditolak</h4>
                    <p>Anda tidak diijinkan untuk mengakses halaman ini</p>
                </div>
                <div class="card-action text-center">
                    <a class="buttowaves-effect waves-light btn-large" style="background:#CE2949"
                        href="{{url('/')}}">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>
</body>

{{-- <script src="{{asset('assets/extends/404-mobile/materialize404-mobile.js')}}"></script> --}}

</html>