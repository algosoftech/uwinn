<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

	public function show_exception($exception)
	{
		if (ENVIRONMENT === 'development' && isset($_SERVER['HTTP_HOST'])
			&& (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
			header('Content-Type: text/html; charset=utf-8', true, 500);
			echo '<pre style="padding:20px;background:#fff;color:#c00;font:13px monospace;">';
			echo 'EXCEPTION: ' . htmlspecialchars($exception->getMessage()) . "\n";
			echo htmlspecialchars($exception->getFile()) . ':' . (int) $exception->getLine() . "\n\n";
			echo htmlspecialchars($exception->getTraceAsString());
			echo '</pre>';
			exit(1);
		}
		return parent::show_exception($exception);
	}
}
