<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
	<meta name="description" content="{{app_info('description')}}" />
	<meta name="author" content="{{app_info('vendor.company')}}" />
	<meta name="base_url" content="{{base_url()}}">

	<link rel="icon" href="{{ app_info('icon') }}">
	<title>404 | Page Not Found</title>
	<script src="{{ asset('/') }}assets/extends/404/js/jquery-1.11.3.min.js"></script>
	<script src="{{aset_extends('plugins/detect-js/2.2.2/detect.min.js')}}"></script>
</head>

<body>
	<script type="text/javascript">
		$(document).ready(function() {
			detectBrowser();
		});

		function detectBrowser() {
			var result = detect.parse(navigator.userAgent);

			let type = result.device.type.toLowerCase();

			let source = ($('meta[name="base_url"]').attr('content')) + 'errors/'+type+'/404';

			if(type == 'desktop'){
				$('body').addClass('page-body');
				$('body').attr('data-url', 'http://neon.dev');
	
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/font-icons/entypo/css/entypo.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/bootstrap.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/neon-core.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/neon-theme.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/neon-forms.css">');
				$('head').append('<link class="aset aset-desktop" rel="stylesheet" href="{{ base_url() }}assets/extends/404/css/custom.css">');
				
				$('head').append('<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600" rel="stylesheet">');
				$('head').append('<link href="https://fonts.googleapis.com/css?family=Montserrat:500,600" rel="stylesheet">');

			}else if(type == 'mobile'){
				$('head').append('<link class="aset aset-mobile" rel="stylesheet" href="{{base_url() }}assets/extends/404-mobile/materialize404-mobile.css">');
				$('head').append('	<link class="aset aset-mobile" rel="stylesheet" href="{{base_url() }}assets/extends/404-mobile/custom.css">');
				$('head').append('<link href="https://fonts.googleapis.com/css?family=Questrial&display=swap" rel="stylesheet">');
				$('body').css('font-family', "'Questrial', 'sans-serif'");
			}

			$('body').load(source);
		}
	</script>

</body>
</html>