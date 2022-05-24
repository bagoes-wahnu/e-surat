<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{app_info('description')}}" />
    <meta name="author" content="{{app_info('vendor.company')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <link rel="icon" href="{{ app_info('icon') }}">

    <title>404 | Halaman tidak ditemukan</title>
</head>

<link rel="stylesheet" href="{{asset('assets/extends/404-mobile/materialize404-mobile.css')}}">
<link rel="stylesheet" href="{{asset('assets/extends/404-mobile/custom.css')}}">

<body>
    <div class="center-screen">
        <div class="col s12 m12">
            <div class="card">
                <div class="card-image" style="padding:20px">
                    <img src="{{asset('assets/extends/404-mobile/404.png')}}">
                    <h5><strong>Halaman tidak ditemukan</strong></h5>
                </div>

                <div class="card-action" style="margin-top: 30px">
                    <a class="buttowaves-effect waves-light btn-large" style="background:#CE2949"
                    href="{{url('/')}}">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>