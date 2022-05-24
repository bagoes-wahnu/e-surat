<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>{{(empty($title)? '' : $title.' | ').app_info('name')}}</title>
	<meta name="csrf-token" content="{{csrf_token()}}" />
	<meta name="description" content="{{app_info('description')}}" />
	<meta name="keywords" content="{{app_info('client.fullname').' '.app_info('client.city')}}" />
	<meta name="author" content="{{app_info('vendor.company')}}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<script src="{{aset_tema()}}assets/jquery-1.12.4.js"></script>
	<link href="{{asset('assets/extends/css/floating_print.css')}}" rel="stylesheet" type="text/css">
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/fontawesome5/css/all.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Oswald:700|Roboto&display=swap" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	<script type="text/javascript">
		WebFont.load({
			google: {
				"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
			},
			active: function () {
				sessionStorage.fonts = true;
			}
		});
	</script>
	<!-- sweetalert2 -->
	<script src="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
	<script src="{{aset_tema()}}assets/vendors/custom/components/vendors/sweetalert2/init.js" type="text/javascript"></script>
	<link href="{{aset_tema()}}assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />
	<!-- energeek -->
	<!-- <link rel="stylesheet" href="{{aset_extends('css/general.css')}}"> -->
	<link rel="stylesheet" href="{{aset_extends('css/pre-loader.css')}}">
	<!-- <link rel="stylesheet" href="{{aset_extends('css/avatar.css')}}"> -->
	<script type="text/javascript" src="{{aset_extends('js/energeek.bundle.js')}}"></script>
	<script type="text/javascript" src="{{aset_extends('js/energeek.app.js')}}"></script>
	<!--end::Global Theme Bundle -->

	<style type="text/css">
		@page {
			size: Legal;
			margin: 0;
		}
		@media print {
			.content {
		      width: 210mm;
		      height: 351mm;
		      border: none !important;
		      margin-bottom: 10px;
		      page-break-after: always;
		    }
		    .page-ttd{
				border: none !important;
				width: 195px;
			}
			* {-webkit-print-color-adjust:exact;}
			.no-print{
				display: none;
			}
		}
		.content {
		  	width: 210mm;
			height: 351mm;
			border: 1px solid #000000;
			margin: auto;
			margin-bottom: 10px;
		}
		<?php
		for($i=1; $i <= $surat->halaman;$i++){
		?>
		#page-{{$i}}{
			background: url('<?php echo url('watch/'.$surat->judul.'?un='.$surat->id_surat.'&ct=surat&src=page-'.$i.'.png') ?>');
			background-size: 100% 100%;
		}
		<?php
		}
		?>
		.page-ttd{
			border: 2px dashed #f3f3f7;
			width: 195px;
		}
		.kt-avatar__upload{
			position: absolute;
			top: -20px;
			right: -20px;
			border: 1px solid #000000;
			padding: 5px 9px 5px 9px;
			border-radius: 136px;
			background: #ffffff;
		}
		.buttons:not(:last-child) {
		    -webkit-transition-delay: 50ms;
		    transition-delay: 20ms;
		    /* background-image: url('https://cbwconline.com/IMG/Facebook-Flat.png'); */
		    background-size: contain;
		    background-color: #4eb96e !important;
		}

	</style>
</head>
<body> 
	<div class="row no-print">
		<div class="col-lg-12">
			<nav class="container"> 
				<a href="javascript:window.print();" class="buttons" tooltip="Cetak Dokumen"><i class="fa fa-print float_content_child"></i></a>
				<a href="javascript:;" class="buttons" tooltip="Tambah Tanda Tangan" onclick="tambahTtd()"><i class="fa fa-plus float_content_child"></i></a>
				<a href="javascript:;" class="buttons" tooltip="Simpan" onclick="simpan()"><i class="fa fa-save float_content_child"></i></a>
				<a href="{{url('surat')}}" class="buttons" tooltip="Kembali"><i class="fa fa-redo-alt float_content_child"></i></a>
				<a class="buttons" href="javascript:;"><i class="fa fa-ellipsis-h float_content_satu"></i></a>
			</nav>
		</div>
	</div>	

	<input type="hidden" name="id_surat" id="id_surat" value="{{$surat->id_surat}}">
	<?php
	for($i=1; $i <= $surat->halaman;$i++){
	?>
	<div class="content" id="page-<?php echo $i;?>"></div>
	<?php
	}
	?>

	<div class="se-pre-con" style="display: none;"></div>

	<script type="text/javascript" src="{{aset_extends('js/page/surat-ttd.js')}}"></script>
</body>
</html>