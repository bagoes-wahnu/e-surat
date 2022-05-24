<?php
$sesi = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->

<head>
	<meta charset="utf-8" />
	<title>{{(empty($title)? '' : $title.' | ').app_info('name')}}</title>
	<meta name="csrf-token" content="{{csrf_token()}}" />
	<meta name="description" content="{{app_info('description')}}" />
	<meta name="keywords" content="{{app_info('client.fullname').' '.app_info('client.city')}}" />
	<meta name="author" content="{{app_info('vendor.company')}}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="base_url" content="{{base_url()}}">

	<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>   -->
	<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" ></script> -->

	<script src="{{aset_tema()}}assets/jquery-1.12.4.js"></script>
	<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

	<!--begin::Fonts -->
	<!-- FONT -->
	<link href="https://fonts.googleapis.com/css?family=Oswald:700|Roboto&display=swap" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	<script type="text/javascript">
		WebFont.load({
			google: {
				"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});

		var idUser = "{{$sesi->usr_id}}";
		var idJabatan = "{{session('userdata')['id_jabatan']}}";
		var idAtasan = "{{session('userdata')['id_atasan']}}";
		var gradeUser = "{{$sesi->usr_grade}}";
		var roleUser = "{{$sesi->usr_role}}";
		var date = new Date();
	</script>

	<!--end::Fonts -->
	<link href="{{aset_tema()}}assets/vendors/general/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/animate.css/animate.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/toastr/build/toastr.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/morris.js/morris.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/socicon/css/socicon.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/line-awesome/css/line-awesome.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/fontawesome5/css/all.min.css" rel="stylesheet" type="text/css" />
	<!--end:: Global Optional Vendors -->
	<!--begin::Global Theme Styles(used by all pages) -->
	<link href="{{aset_tema()}}assets/demo/demo7/base/style.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles -->
	<link rel="shortcut icon" href="{{aset_extends()}}img/logo/favicon-esurat.png" />

	<!-- CSS CUSTOM -->
	<link href="{{aset_tema()}}assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_extends()}}css/main.css" rel="stylesheet" type="text/css" />

	<!-- fancybox -->
	<link rel="stylesheet" href="{{aset_extends('plugins/fancybox-3.5.7/dist/jquery.fancybox.min.css')}}" />
	<script src="{{aset_extends('plugins/fancybox-3.5.7/dist/jquery.fancybox.min.js')}}"></script>
	<!-- ladda -->
	<link rel="stylesheet" href="{{aset_extends('plugins/ladda/dist/ladda-themeless.min.css')}}">
	<script src="{{aset_extends('plugins/ladda/dist/spin.min.js')}}"></script>
	<script src="{{aset_extends('plugins/ladda/dist/ladda.min.js')}}"></script>
	<!-- energeek -->
	<link rel="stylesheet" href="{{aset_extends('css/general.css')}}">
	<link rel="stylesheet" href="{{aset_extends('css/pre-loader.css')}}">
	<script type="text/javascript" src="{{aset_extends('js/energeek.bundle.js')}}"></script>
	<script type="text/javascript" src="{{aset_extends('js/energeek.app.js')}}"></script>
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body class="kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-aside--minimize kt-page--loading">

	<!-- begin:: Page -->

	<!-- begin:: Header Mobile -->
	<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
		<div class="kt-header-mobile__logo">
			<a href="index.html">
				<img alt="Logo" class="logo-brand-header-mobile" src="{{aset_extends()}}img/logo/logo-esurat.png" />
			</a>
		</div>
		<div class="kt-header-mobile__toolbar">
			<button class="kt-header-mobile__toolbar-toggler" id="kt_header_mobile_toggler"><span></span></button>
			<button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler">
				<i class="flaticon-more"></i>
			</button>
		</div>
	</div>

	<!-- end:: Header Mobile -->
	<div class="kt-grid kt-grid--hor kt-grid--root">
		<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
				<!-- begin:: Header -->
				<div id="kt_header" class="kt-header kt-grid kt-grid--ver  kt-header--fixed ">

					<!-- begin: Header Menu -->
					<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
					<div class="kt-header-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_header_menu_wrapper">
						<div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout- ">

							<ul class="kt-menu__nav ">

								<li class="kt-menu__item  kt-menu__item--active kt-menu__item--titleApp " aria-haspopup="true">
									<a href="{{url('/home')}}" class="kt-menu__link ">
										<!-- <span class="kt-menu__link-text">Dashboard</span> -->
										<img alt="Logo" class="logo-brand-header-mobile" src="{{aset_extends()}}img/logo/logo-esurat.png" />
									</a>
								</li>
								<li id="daftarSuratNav" class="kt-menu__item" aria-haspopup="true">
									<a href="{{ url('/home')}}" class="kt-menu__link "><span class="kt-menu__link-text">Daftar Surat Keluar</span></a>
								</li>
								<?php
								if (in_array($sesi->usr_role, [1, 4])) {
								?>
									<li id="daftarSuratMasukNav" class="kt-menu__item" aria-haspopup="true">
										<a href="{{ url('/surat-masuk')}}" class="kt-menu__link "><span class="kt-menu__link-text">Daftar Surat Masuk</span></a>
									</li>
								<?php
								}
								?>
								<li id="daftarArsipSuratNav" class="kt-menu__item" aria-haspopup="true">
									<a href="{{ url('arsip-surat')}}" class="kt-menu__link "><span class="kt-menu__link-text">Arsip Surat Keluar</span></a>
								</li>
								<li id="daftarSuratIzinTerminalNav" class="kt-menu__item" aria-haspopup="true">
									<a href="{{ url('surat-izin-terminal')}}" class="kt-menu__link "><span class="kt-menu__link-text">Surat Izin Terminal</span></a>
								</li>
								<?php
								if ($sesi->usr_role == 1) {
								?>
									<li id="tandaTanganNav" class="kt-menu__item" aria-haspopup="true">
										<a href="{{ url('/tanda-tangan')}}" class="kt-menu__link "><span class="kt-menu__link-text">Tanda Tangan</span></a>
									</li>
									<li id="stempelNav" class="kt-menu__item" aria-haspopup="true">
										<a href="{{ url('/stempel')}}" class="kt-menu__link "><span class="kt-menu__link-text">Stempel</span></a>
									</li>
									<li id="masterUserNav" class="kt-menu__item" aria-haspopup="true">
										<a href="{{ url('master/user')}}" class="kt-menu__link "><span class="kt-menu__link-text">Master User</span></a>
									</li>
								<?php
								}
								?>
							</ul>
						</div>
					</div>
					<!-- end: Header Menu -->
					<!-- begin:: Header Topbar -->
					<div class="kt-header__topbar">
						<!--begin: User bar -->
						<div class="kt-header__topbar-item kt-header__topbar-item--user">

							<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
								<span class="kt-hidden kt-header__topbar-welcome">Hi,</span>
								<span class="kt-hidden kt-header__topbar-username">{{$sesi->usr_name}}</span>
								<img class="kt-hidden" alt="Pic" src="{{aset_tema()}}assets/media/users/300_21.jpg" />
								<span class="kt-header__topbar-icon"><i class="flaticon2-user-outline-symbol"></i></span>
							</div>
							<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">
								<!--begin: Head -->
								<div class="kt-user-card kt-user-card--skin-light kt-notification-item-padding-x" style="background: #4EB76E">
									<div class="kt-user-card__avatar">
										<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
										<span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg badge--rounded kt-badge--bold kt-hidden">S</span>
									</div>
									<div class="kt-user-card__name white-text" style="color: white">
										{{$sesi->usr_name}}
									</div>
									<span><a href="javascript:;" onclick="modal_password()" class="btn btn-outline-secondary" style="color: white">Ubah Password</a></span>
									<div class="kt-user-card__badge">
										{{-- <span class="btn btn-label-success btn-sm btn-bold btn-font-md">23
										messages</span> --}}
									</div>
								</div>
								<!--end: Head -->
								<!--begin: Navigation -->
								<div class="kt-notification">

									{{-- <div class="kt-notification__custom">
										<a href="{{url('/logout')}}"
									class="btn btn-label-orange-esurat btn-sm btn-bold">Sign Out</a>
								</div> --}}
								<a href="{{url('/logout')}}" class="kt-notification__item">
									<div class="kt-notification__item-icon">
										<i class="fa flaticon2-close-cross kt-font-success"></i>
									</div>
									<div class="kt-notification__item-details">
										<div class="kt-notification__item-title kt-font-bold">
											Keluar
										</div>
									</div>
								</a>
							</div>
							<!--end: Navigation -->
						</div>
					</div>
					<!--end: User bar -->
					<!--begin: Quick panel toggler -->
					<!-- <div class="kt-header__topbar-item kt-header__topbar-item--quickpanel" data-toggle="kt-tooltip"
						title="Quick panel" data-placement="top">
						<div class="kt-header__topbar-wrapper">
							<span class="kt-header__topbar-icon" id="kt_quick_panel_toggler_btn"><i
								class="flaticon2-cube-1"></i></span>
							</div>
						</div> -->
					<!--end: Quick panel toggler -->
				</div>
				<!-- end:: Header Topbar -->
			</div>
			<!-- end:: Header -->
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
				<!-- begin:: Subheader -->
				<div class="kt-subheader   kt-grid__item" id="kt_subheader">
					<div class="kt-subheader__main">

					</div>
					<div class="kt-subheader__toolbar">

					</div>
				</div>
				<!-- end:: Subheader -->
				<!-- begin:: Content -->
				<!-- begin:: Content -->
				<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
					<!--Begin::Section-->
					@yield('content')
					<!--End::Section-->
				</div>
				<!-- end:: Content -->
				<!-- end:: Content -->
			</div>
		</div>
	</div><!-- begin:: Footer -->
	<div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop">
		<div class="kt-footer__copyright">
			2019&nbsp;&copy;&nbsp;
			<a href="#" target="_blank" class="kt-link">Dinas Perhubungan Kota Surabaya</a>
		</div>
		<div class="kt-footer__menu"></div>
	</div>

	<!-- end:: Footer -->
	</div>
	<!-- end:: Page -->

	<!-- begin::Scrolltop -->
	<div id="kt_scrolltop" class="kt-scrolltop">
		<i class="fa fa-arrow-up"></i>
	</div>

	<!-- end::Scrolltop -->

	<div class="se-pre-con" style="display: none;"></div>

	<div class="modal fade" id="modal_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">
						Form Ubah Password
					</h5>
					<button type="button" class="close btn_close_modal" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
				</div>
				<div class="modal-body" style="padding-top: 0;">
					<form id="formPassword" action="" method="post" onsubmit="return false;">
						<input type="hidden" name="id_user" id="id_user" value="">

						<div class="form-group">
							<label class="form-control-label lbl">
								Password Lama:
							</label>
							<input type="password" class="form-control m-input input-password" id="password_lama" name="password_lama" placeholder="Masukkan password lama" autocomplete="off">
						</div>
						<div class="form-group">
							<label class="form-control-label lbl">
								Password Baru:
							</label>
							<input type="password" class="form-control m-input input-password" id="password_baru" name="password_baru" placeholder="Masukkan password baru" autocomplete="off">
						</div>
						<div class="form-group">
							<label class="form-control-label lbl">
								Konfirmasi Password Baru:
							</label>
							<input type="password" class="form-control m-input input-password" id="conf_password_baru" name="conf_password_baru" placeholder="Masukkan kembali password baru Anda" autocomplete="off">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary btn_close_modal" data-dismiss="modal">
						Tutup
					</button>
					<button type="button" class="btn btn-success ladda-button" data-style="zoom-in" id="btn_save_password">
						Simpan
					</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="{{aset_extends('js/page/password.js')}}"></script>

	<!-- begin::Global Config(global config for global JS sciprts) -->
	<script>
		var KTAppOptions = {
			"colors": {
				"state": {
					"brand": "#22b9ff",
					"light": "#ffffff",
					"dark": "#282a3c",
					"primary": "#5867dd",
					"success": "#34bfa3",
					"info": "#36a3f7",
					"warning": "#ffb822",
					"danger": "#fd3995"
				},
				"base": {
					"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
					"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
				}
			}
		};
	</script>
	<!-- end::Global Config -->

	<!--begin:: Global Mandatory Vendors -->
	<!-- <script src="{{aset_tema()}}assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script> -->
	<script src="{{aset_tema()}}assets/vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript">
	</script>
	<!--end:: Global Mandatory Vendors -->

	<!--begin:: Global Optional Vendors -->
	<script src="{{aset_tema()}}assets/vendors/general/jquery-form/dist/jquery.form.min.js" type="text/javascript">
	</script>
	<script src="{{aset_tema()}}assets/vendors/general/owl.carousel/dist/owl.carousel.js" type="text/javascript">
	</script>
	<script src="{{aset_tema()}}assets/vendors/general/dropzone/dist/dropzone.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/toastr/build/toastr.min.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/raphael/raphael.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/morris.js/morris.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/chart.js/dist/Chart.bundle.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
	<link href="{{aset_tema()}}assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css" />
	<link href="{{aset_tema()}}assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
	<!-- sweetalert2 -->
	<script src="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript">
	</script>
	<script src="{{aset_tema()}}assets/vendors/custom/components/vendors/sweetalert2/init.js" type="text/javascript">
	</script>
	<link href="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />


	{{-- bootstrap select --}}
	<script src="{{aset_tema()}}assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js" type="text/javascript"></script>

	<!-- select2 -->
	<script src="{{aset_tema()}}assets/vendors/general/select2/dist/js/select2.min.js" type="text/javascript"></script>

	<!--end:: Global Optional Vendors -->

	<!--begin::Global Theme Bundle(used by all pages) -->
	<script src="{{aset_extends()}}js/scripts.bundle.js" type="text/javascript"></script>

	<!--end::Global Theme Bundle -->

	<!--begin::Page Scripts(used by this page) -->
	<script src="{{aset_tema()}}assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/app/custom/general/dashboard.js" type="text/javascript"></script>
	<!-- <script src="{{aset_extends()}}js/tabel-apps-esurat.js" type="text/javascript"></script> -->

	<!--end::Page Scripts -->

	<!-- CUSTOMM JS -->
	<!-- dropzone -->
	<!-- <script src="{{aset_extends()}}js/js-dropzone.js" type="text/javascript"></script> -->
	<script type="text/javascript">
		Dropzone.autoDiscover = false;
	</script>

</body>

<!-- end::Body -->

</html>