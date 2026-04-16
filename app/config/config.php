<?php
require_once __DIR__ . '/../../config/config.php';

if (!defined('APPROOT')) {
	define('APPROOT', dirname(dirname(__FILE__)));
}

if (!defined('URLROOT')) {
	define('URLROOT', BASE_URL);
}

if (!defined('SITENAME')) {
	define('SITENAME', SITE_NAME);
}
