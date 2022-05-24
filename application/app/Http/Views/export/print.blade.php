<!DOCTYPE html>
<html>
<head>
	<title>Print</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<script src="{{aset_tema()}}assets/jquery-1.12.4.js"></script>
	<link href="{{asset('assets/extends/css/floating_print.css')}}" rel="stylesheet" type="text/css">
	<link href="{{aset_tema()}}assets/vendors/custom/vendors/fontawesome5/css/all.min.css" rel="stylesheet" type="text/css"/>
	<!--end::Global Theme Bundle -->

	<style type="text/css">
		.page-wrapper{
			border: 2px solid;
			width: 550px;
			background-color: #cdcde0; 
		}

		.page-ttd{
			width: 195px;
			/*width: 250px;*/
			position: absolute;
		}

		.page-content{
			/*margin:25px;*/
			/*width: 21cm;*/
			/*height: auto;*/
		}

		.page-pdf-wrapper{
			display: block;
			width: 100%;
			margin-top: 10px;
			margin-bottom: 10px;
		}

		.page-pdf-img{
			width: 100%;
			height: auto;
		}
	</style>
</head>
<body>
	<div class="row">
		<div class="col-lg-12">
			<nav class="container"> 
				<a href="#" class="buttons" tooltip="Tambah"><i class="fa fa-plus float_content_child"></i></a>
				<a href="#" class="buttons" tooltip="Simpan"><i class="fa fa-save float_content_child"></i></a>
				<a href="#" class="buttons" tooltip="Kembali"><i class="fa fa-redo-alt float_content_child"></i></a>
				<a class="buttons" href="#"><i class="fa fa-angle-double-up float_content_satu"></i></a>
			</nav>
		</div>
	</div>
	<div class="page-content">
		<?php
		// page 1
		// $left = ($surat_ttd->left - 153) * 1.5;
		// $left = ($surat_ttd->left - 216) * 2.2;
		$left = ($surat_ttd->left - 136);
		$top = ($surat_ttd->top + 537);
		?>

		<img class="page-ttd" id="ttd" src="{{url('watch/sample?ct=tanda_tangan&src='.$ttd->path_file)}}" style=" <?php echo (!empty($surat_ttd))? 'left:'.$left.'px;top:'.$top.'px' : ''; ?> ">
		<?php
		for($i=1; $i <= $surat->halaman;$i++){
			?>
			<div class="page-pdf-wrapper" id="page-3">
				<img class="page-pdf-img" src="{{url('watch/sample?ct=surat&src=page-'.$i.'.png')}}"/>
			</div>
			<?php
		}
		?>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			// reAssignTTD();
		});

		function reAssignTTD() {
			var $ttd = $('.page-ttd');

			for (var i = 0; i < $ttd.length; i++) {
				var style = $($ttd[i]).eq(0).attr('style');

				let explode = style.split(';');

				let left = $($ttd[i]).css('left').replace(/\D/g, "");
				let top = $($ttd[i]).css('top').replace(/\D/g, "");

				console.log('left : '+left);
				console.log('top : '+top);
				console.log(style);
				// left = (parseFloat(left) + 180);
				// top = (parseFloat(top) + 195);
				left = (parseFloat(left) + 495);
				top = (parseFloat(top) + 734);

				left = left.toString()+'px';
				top = top.toString()+'px';

				// setTimeout(function () {
					$($ttd[i]).css({'left':left, 'top':top});

					let left2 = $($ttd[i]).css('left');
					let top2 = $($ttd[i]).css('top');

					console.log('left-2 : '+ left2);
					console.log('top-2 : '+ top2);
				// }, 300);
			}
		}
	</script>
</body>
</html>