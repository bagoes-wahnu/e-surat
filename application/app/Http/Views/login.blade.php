<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{(empty($title)? '' : $title.' | ').app_info('name')}}</title>
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <meta name="description" content="{{app_info('description')}}" />
    <meta name="author" content="{{app_info('vendor.company')}}" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{aset_extends('img/logo/favicon-esurat.png')}}" />
    <!-- <link rel="stylesheet" href="{{aset_extends()}}login/css/bootstrap.css"> -->
    <link rel="stylesheet" href="{{aset_extends()}}login/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="{{aset_extends()}}css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen"
    href="{{aset_extends()}}login/css/main-login.css" />
    <link rel="stylesheet" type="text/css" media="screen"
    href="{{aset_extends()}}login/css/main-login-esurat.css" />

    <!-- <script src="{{aset_extends()}}login/js/bootstrap.js"></script> -->
    <!-- <script src="{{aset_extends()}}login/js/bootstrap.min.js"></script> -->
    <script src="{{aset_extends()}}login/js/jquery.js"></script>
    <script src="{{aset_extends()}}login/js/jquery.sticky.js"></script>
    <!-- <script src="main.js"></script> -->
    <script type="text/javascript">
        var baseUrl = "{{url('/')}}/";
    </script>
</head>

<body>
    <div class="body">
        <nav class="navbar navbar-expand-sm bg-light justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <!-- <a class="nav-link" href="#">Link 1</a> -->
                    <img class="nav-logo" src="{{aset_extends('img/logo/logo-esurat.png')}}">
                </li>

            </ul>
        </nav>
        <div class="login">

            <div class="row">
                <div class="col-lg-6 col-xs-12 left">
                    <div class="vcenter">
                        <img src="{{aset_extends('img/illustration/login-esurat.png')}}" alt="" srcset="">
                    </div>

                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="login-wrapper">
                        <div class="form-login">
                            <p class="title">
                                Silahkan masuk
                            </p>
                            <form action="">
                                <input type="hidden" name="url_source" id="url_source" value="{{$source}}">
                                <div class="username form-group">

                                    <input type="text" class="form-control form-submit" id="username" name="username" placeholder="masukkan username">
                                    <label for="">Username</label>
                                </div>
                                <div class="password form-group">
                                    <input type="password" class="form-control form-submit" id="password" name="password" placeholder="masukkan password">
                                    <label for="">Password</label>
                                </div>
                                <div class="checkbox" style="margin-top : 5px">
                                    <label><input type="checkbox" value="" onchange="showHidePassword()"><span class="text-light">Show
                                    Password</span></label>
                                </div>
                                <div class="button" style="margin-bottom:250px">
                                    <button type="button" class="btn btn-orange-cta btn-block btn-shadow-login-invert ladda-button" data-style="zoom-in" id="btn_login" style="font-weight: bolder;margin-bottom: unset !important;    padding: 10px;font-size: 1.25rem">
                                        LOGIN
                                    </button>
                                </div>
                            </form>
                        </div>


                    </div>

                </div>
            </div>
            <div class="row">


            </div>


        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <center>
                            <p>©2019 &middot;<a href="javascript:;"> Dinas Perhubungan Kota Surabaya</a> &middot;
                                All rights
                            reserved</p>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sweetalert2 -->
    <script src="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
    <script src="{{aset_tema()}}assets/vendors/custom/components/vendors/sweetalert2/init.js" type="text/javascript"></script>
    <link href="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />
    <!-- ladda -->
    <link rel="stylesheet" href="{{aset_extends('plugins/ladda/dist/ladda-themeless.min.css')}}">
    <script src="{{aset_extends('plugins/ladda/dist/spin.min.js')}}"></script>
    <script src="{{aset_extends('plugins/ladda/dist/ladda.min.js')}}"></script>
    <!-- energeek -->
    <link rel="stylesheet" href="{{aset_extends('css/pre-loader.css')}}">
    <!-- page -->
    <script src="{{aset_extends('js/page/login.js')}}"></script>

    <script type="text/javascript">
        <?php
        $alerts = session('alerts');

        if(!empty($alerts)){
            foreach ($alerts as $key => $value) {
                ?>
                Swal.fire('<?php echo $value[2]; ?>', '<?php echo $value[1]; ?>', '<?php echo $value[0]; ?>');
                <?php
            }
        }
        ?>
    </script>

    <style type="text/css">
        .ladda-button[data-loading]{
            background: #099a6f !important;
        }
        .swal2-container{
            z-index: 9999;
        }
    </style>
</body>

</html>