<?php
/*
 *
 * Rock API Main Index File
 *
 */
ini_set('display_errors', 'On'); error_reporting(E_ALL); //E_ERROR, E_ALL

// Session_save_path('framework/tmp');
ini_set('session.gc_probability', 1);

define('DS', DIRECTORY_SEPARATOR);
define('PATH_BASE', dirname(__FILE__));

require_once('framework/includes/defines.php');
require_once(PATH_FRAMEWORK . '/application.php');

/* Route & display Rock Application */
$rock_app = new RApp();
$rock_app->route()->display(/*true*/);