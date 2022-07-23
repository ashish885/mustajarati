<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ transLang('admin_app_name') }} - {{ transLang('404_page') }}</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="{{ URL::to('backend/bootstrap/css/bootstrap.min.css') }}">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ URL::to('backend/dist/css/AdminLTE.min.css') }}">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
	 folder instead of downloading all of them to reduce the load. -->

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="hold-transition skin-blue layout-top-nav">
	<div class="wrapper">
		<div class="content-wrapper">
			<section class="content">
				<div class="error-page">
					<h2 class="headline text-yellow">404</h2>
					<div class="error-content">
						<h3><i class="fa fa-warning text-yellow"></i> {{ transLang('oops_page_not_found') }}.</h3>
						<p> {{ transLang('404_label') }}
							<a href="javascript:history.back();"> {{ transLang('return_to_back_page') }} </a>
						</p>
					</div>
				</div>
			</section>
		</div>
		<footer class="main-footer">
			<strong>{{ transLang('copyright') }} &copy; {{ date('Y') }} 
			<a>{{ transLang('company') }}</a>.
		</strong> {{ transLang('all_rights_reserved') }}.
		</footer>
	</div>

	<!-- jQuery 2.2.3 -->
	<script src="{{ URL::to('backend/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="{{ URL::to('backend/bootstrap/js/bootstrap.min.js') }}"></script>
	<!-- SlimScroll -->
	<!-- FastClick -->
	<!-- AdminLTE App -->
	<script src="{{ URL::to('backend/dist/js/app.min.js') }}"></script>
	<!-- AdminLTE for demo purposes -->
</body>

</html>