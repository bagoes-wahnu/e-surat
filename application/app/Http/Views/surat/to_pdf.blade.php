<!DOCTYPE html>
<html>
<head>
	<title>DomPDF</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<script src="{{aset_tema()}}assets/jquery-1.12.4.js"></script>

	<style type="text/css">
		html, body{
			margin: 0;
		}

		.page-wrapper{
			border: 2px solid;
			width: 550px;
			background-color: #cdcde0; 
		}

		.page-ttd{
			width: 195px;
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

		.content {
		  	width: 210mm;
			height: 351mm;
			border: 1px solid #000000;
			margin: auto;
			margin-bottom: 10px;
			position: absolute;
		}
	</style>
</head>
<body>
	<div class="page-content">
		<?php
		foreach ($surat_ttd as $key => $value) {
		
			echo '<img id="ttd-'. $value->id_detail . '" style="position:absolute;left:'. $value->left . 'px;top:'. $value->top . 'px" class="page-ttd" src="' . base_url() . 'watch/'. $value->nama_ttd . '?un='. $value->id_ttd . '&ct=tanda_tangan&src='. $value->path_file . '">';
		}
		?>


		<?php
		for($i=1; $i <= $surat->halaman;$i++){
			?>
			<div class="page-pdf-wrapper" id="page-<?php echo $i;?>">
				<img class="page-pdf-img" src="{{url('watch/'.$surat->judul.'?un='.$surat->id_surat.'&ct=surat&src=page-'.$i.'.png')}}"/>
			</div>
			<?php
		}
		?>

		
	</div>
</body>
</html>