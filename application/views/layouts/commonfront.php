<!DOCTYPE html>
<html lang="en">
<head>
    <title>{title} | UWINN </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="icon" href="<?=base_url('assets/img/favicon.png')?>" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{description}" />
    <meta name="keywords" content="{keyword}">
    <meta name="author" content="Uwinn" />
    {head}
    {header}
</head>
<body class="">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<div class="main_wrapper">
		<!-- [ Pre-loader ] End -->
		<!-- [ Header ] start -->
		{navigation}
		<!-- [ Header ] end -->
		<!-- [ Main Content ] start -->
        <div class="container main-section-conatiner">  
        	<div class="banner-section">
				{banner}
        	</div>
        	<div class="contant-section">
	        	<div class="row">
					{content}
				</div>
			</div>
        </div>
		<!-- Button trigger modal -->
		<!-- Footer Js start -->
		{footer}
	</div>
	<!-- Footer Js end -->
	<!-- Required Js start -->
	{footer_js}
	<!-- Required Js end -->
</body>
</html>