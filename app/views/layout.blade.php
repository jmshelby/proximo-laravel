<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>Proximo</title>
    <?php /* {{ HTML::style('/css/style.css') }} */ ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);
 
		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}
 
		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}
 
		a, a:visited {
			text-decoration:none;
		}
 
		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>




</head>
<body>
        <!-- check for flash notification message -->
        @if(Session::has('flash_notice'))
            <div id="flash_notice">{{ Session::get('flash_notice') }}</div>
        @endif

        @yield('content')
    </div><!-- end container -->
</body>
</html>
