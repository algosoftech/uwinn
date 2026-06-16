<!-- Favicon icon -->
<link rel="icon" href="{ASSET_INCLUDE_URL}image/favicon.png" type="image/x-icon">
<!-- vendor css -->
<link rel="stylesheet" href="{ASSET_INCLUDE_URL}css/style.css">
<link href="{ASSET_INCLUDE_URL}css/manoj.css" id="theme" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- data tables css -->
<link rel="stylesheet" href="{ASSET_INCLUDE_URL}css/plugins/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{ASSET_INCLUDE_URL}css/chosen.min.css">
<script type="text/ecmascript">
var BASEURL 			=	'{BASE_URL}';
var FULLSITEURL 		=	'{FULL_SITE_URL}';
var ASSETURL 			=	'{ASSET_URL}';
var ASSETINCLUDEURL 	=	'{ASSET_INCLUDE_URL}';
var CURRENTCLASS 		=	'{CURRENT_CLASS}';
var CURRENTMETHOD 		=	'{CURRENT_METHOD}';
var csrf_api_key		=	'<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_api_value 		=	'<?php echo $this->security->get_csrf_hash(); ?>'; 
</script>
<?php if ($_SERVER['SERVER_NAME'] == 'localhost'): ?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<?php else: ?>
<script src="{ASSET_INCLUDE_URL}js/vendor-all.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/plugins/bootstrap.min.js"></script>
<?php endif; ?>