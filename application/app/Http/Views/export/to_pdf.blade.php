<!DOCTYPE html>
<html>
<head>
	<title>DomPDF</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<script src="{{aset_tema()}}assets/jquery-1.12.4.js"></script>

	<style type="text/css">
		.page-wrapper{
			border: 2px solid;
			width: 550px;
			background-color: #cdcde0; 
		}

		.page-ttd{
			width: 250px;
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
	<div class="page-content">
		<?php 
		// echo htmlspecialchars($content);
		// echo $content;
		?>

		<!-- <img class="page-ttd ui-draggable-handle" id="ttd" src="http://localhost/file-signature/file/ttd3.png" style="left: 360px; top: 440px;">
		<div class="page-pdf-wrapper" id="page-3">
			<img class="page-pdf-img" id="img-3" src="http://localhost/file-signature/file/pdf2-page-3.jpg">
		</div> -->
		<img src="{{base_url().myStorage('edited-page-1.png')}}">
		<img src="{{base_url().myStorage('edited-page-2.png')}}">



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

				// for (var j = 0; j < explode.length; j++) {
				// 	let currentStyle = explode[j].replace(/ /g, '');
				// 	let temp = currentStyle.split(':');

				// 	if(temp[0] == 'left'){
				// 		left = temp[1].replace(/\D/g, "");
				// 	}

				// 	if(temp[0] == 'top'){
				// 		top = temp[1].replace(/\D/g, "");
				// 	}
				// }

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