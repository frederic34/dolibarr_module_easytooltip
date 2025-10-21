<?php
define('NOLOGIN', 1);

if (!defined('NOREQUIREUSER')) {
	define('NOREQUIREUSER', 1);
}
if (!defined('NOREQUIREDB')) {
	define('NOREQUIREDB', 1);
}
if (!defined('NOREQUIRETRAN')) {
	define('NOREQUIRETRAN', 1);
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}

require_once '../../../config.php';
dol_include_once('/easytooltip/vendor/autoload.php');

// Load the IconCaptcha options.
$options = require dol_buildpath('/easytooltip/captcha-config.php', 0);

// Create an instance of IconCaptcha.
use IconCaptcha\IconCaptcha;

try {

	// Start a session.
	// * Only required when using any 'session' driver in the configuration.
	session_start();

	// Create an instance of IconCaptcha.
	$captcha = new IconCaptcha($options);

	// Handle the CORS preflight request.
	// * If you have disabled CORS in the configuration, you may remove this line.
	$captcha->handleCors();

	// Process the request.
	$captcha->request()->process();

	// Request was not supported/recognized.
	http_response_code(400);
} catch (Throwable $exception) {

	http_response_code(500);

	// Add your custom error logging handling here.

}
