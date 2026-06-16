<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function uwinn_is_localhost()
{
	if (php_sapi_name() === 'cli') {
		return false;
	}
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	return (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
}

function suppress_php8_deprecation_notices()
{
	if (!uwinn_is_localhost()) {
		return;
	}
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_WARNING);
	ini_set('display_errors', '0');
	static $handlerRegistered = false;
	if ($handlerRegistered) {
		return;
	}
	$handlerRegistered = true;
	set_error_handler(function ($errno, $errstr, $errfile, $errline) {
		if (in_array($errno, array(E_WARNING, E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED, E_USER_WARNING), true)) {
			return true;
		}
		return false;
	}, E_ALL);
}

function uwinn_register_fatal_handler()
{
	if (!uwinn_is_localhost()) {
		return;
	}
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_WARNING);
	ini_set('display_errors', '1');
	suppress_php8_deprecation_notices();
	register_shutdown_function(function () {
		$err = error_get_last();
		if (!$err || !in_array($err['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
			return;
		}
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		echo '<pre style="padding:20px;background:#fff;color:#c00;font:14px monospace;">';
		echo 'FATAL: ' . htmlspecialchars($err['message']) . "\n";
		echo htmlspecialchars($err['file']) . ':' . (int) $err['line'];
		echo '</pre>';
	});
}
