<?php 
require('../../connectors/php/config.php') ;
if(isset($_REQUEST['fileUrl']))
{
	$filename		=	$_REQUEST['fileName'];
	$fileurl		=	str_replace('/'.$_REQUEST['fileName'],'',$_REQUEST['fileUrl']);
	$fileurlarray	=	explode('/',$fileurl);
	if(end($fileurlarray) == 'file')
	{
		@unlink($Config['FileTypesAbsolutePath']['File'].$filename);
	}
	else if(end($fileurlarray) == 'image')
	{  echo $Config['FileTypesAbsolutePath']['Image'].$filename;
		unlink($Config['FileTypesAbsolutePath']['Image'].$filename);
	}
	else if(end($fileurlarray) == 'flash')
	{
		@unlink($Config['FileTypesAbsolutePath']['Flash'].$filename);
	}
	else if(end($fileurlarray) == 'media')
	{
		@unlink($Config['FileTypesAbsolutePath']['Media'].$filename);
	}
	echo $filename; 
}
die;

?>