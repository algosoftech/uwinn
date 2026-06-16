<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_system'] = array(
	'class'    => '',
	'function' => 'uwinn_register_fatal_handler',
	'filename' => 'php8_compat.php',
	'filepath' => 'hooks'
);
$hook['pre_controller'] = array(
	'class'    => '',
	'function' => 'suppress_php8_deprecation_notices',
	'filename' => 'php8_compat.php',
	'filepath' => 'hooks'
);
$hook['post_controller_constructor'] = array(
	'class'    => '',
	'function' => 'suppress_php8_deprecation_notices',
	'filename' => 'php8_compat.php',
	'filepath' => 'hooks'
);
