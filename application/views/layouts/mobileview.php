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
</head>
<body>
 	<div class="mobile_warpper">
		{header}
		<!-- [ Main Content ] start -->
		<div>
			{content}
		</div>
		<!-- [ Main Content ] end -->
		<!-- Button trigger modal -->
		<div class="footer_nav">
			{footer_nav}
		</div>
	</div>
	 
	<!-- Required Js start -->
	{footer_js}
	<!-- Required Js end -->
</body>
</html>