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
	<meta name="base_url" content="{{base_url()}}">

	<link rel="shortcut icon" href="{{aset_extends()}}img/logo/favicon-esurat.png" />

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
		@media print {
			html, body{
				margin: 0;
			}

			.content {
				padding: 1px;
				border: none !important;
				margin: auto;
				margin-bottom: 15px;
			}

			<?php
			$urlPage = myStorage('surat/'.$surat2->id_surat.'/page-1.png');
			if(file_exists($urlPage) && !is_dir($urlPage)){
				list($width, $height) = getimagesize(myBasePath().$urlPage);
				if ($width > $height) {
					?>
					@page {size: legal landscape;margin: 0;}
					<?php
				}else{
					?>
					@page {size: legal portrait; margin: 0; }
					<?php
				}
			}else{
				?>
				@page {size: legal portrait; margin: 0; }
				<?php
			}
			?>

			.potrait {
				width: 210mm;
				height: 330mm;
			}

			.landscape {
				width: 330mm !important;
				height: 210mm !important;
			}

			.page-ttd{
				padding: 2px;
				border: none !important;
				width: 195px;
			}

			.page-qrcode{
				border: none !important;
				width: 100px;
				height: 100px;
			}

			.page-stempel{
				border: none !important;
				width: 100px;
				height: 100px;
			}

			* {-webkit-print-color-adjust:exact;}
			.no-print{
				display: none;
			}
		}

		.content {
			border: 1px solid #000000;
			margin: auto;
			margin-bottom: 10px;
		}

		.potrait {
			width: 210mm;
			height: 330mm;
		}

		.landscape {
			width: 330mm !important;
			height: 210mm !important;
		}
		<?php
		$nama_file = str_replace('/', '-', $surat->judul);
		for($i=1; $i <= $surat->halaman;$i++){
			?>
			#page-{{$i}}{
				background: url('<?php echo url('watch/'.$nama_file.'?un='.$surat->id_surat.'&ct=surat&src=page-'.$i.'.png') ?>');
				background-size: 100% 100%;
			}
			<?php
		}
		?>
		.page-ttd{
			width: 195px;
			cursor: move;
			min-width: 100px;
			min-height: 50px;
			max-width: 400px;
			max-height: 208px;
		}

		.page-qrcode{
			width: 100px;
			height: 100px;
			cursor: move;
		}

		.page-stempel{
			width: 220px;
			height: 220px;
			min-width: 100px;
			min-height: 100px;
			max-width: 400px;
			max-height: 400px;
			cursor: move;
			z-index:  9 !important;
		}
	</style>
</head>
<body>
	<input type="hidden" name="id_surat" id="id_surat" value="{{$surat->id_surat}}">
	<?php
	for($i=1; $i <= $surat2->halaman;$i++){
		$orientation = 'potrait';
		$surat = $surat2;
		$width=0;
		$height=0;
		$urlPage = myStorage('surat/'.$surat->id_surat.'/page-'.$i.'.png');

		list($width, $height) = getimagesize(myBasePath().$urlPage);
		if ($width > $height) {
			$orientation = 'landscape';
		}
		?>
		<div class="content <?php echo $orientation; ?>" id="page-<?php echo $i;?>">
			<?php
			if($i == 1){
				foreach ($surat_ttd as $key => $value) {
					?>
					<div id="ttd-<?php echo $value->id_detail;?>" style="left:<?php echo $value->left;?>px;top:<?php echo $value->top;?>px;width:<?php echo $value->width;?>px;height:<?php echo $value->height;?>px;position:relative !important;" class="page-ttd"><img style="width: 100%;" src="{{url('watch/'.$value->nama_ttd.'?un='.$value->id_ttd.'&ct=tanda_tangan&src='.$value->path_file)}}"></div>
					<?php
				}

				foreach ($surat_stempel as $key => $value) {
					?>
					<div id="stempel-<?php echo $value->id_detail;?>" style="left:<?php echo $value->left;?>px;top:<?php echo $value->top;?>px;width:<?php echo $value->width;?>px;height:<?php echo $value->height;?>px;position:relative !important;" class="page-stempel"><img style="width: 100%;" src="{{url('watch/'.$value->nama_stempel.'?un='.$value->id_stempel.'&ct=stempel&src='.$value->path_file)}}"></div>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
	?>
</body>
</html>