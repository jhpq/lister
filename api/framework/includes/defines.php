<?php
/*
 *
 *
 */
$dir_parts = explode(DS, PATH_BASE);
array_push($dir_parts, 'framework');


/*
 *
 *
 */
define('PATH_ROOT',         implode(DS, $dir_parts));
define('PATH_FRAMEWORK',    PATH_ROOT . '/tsv');
define('PATH_LIBRARIES',    PATH_ROOT . '/lib');


/*
 *
 *
 */
array_pop($dir_parts);
define('PATH_API', implode(DS, $dir_parts) . '/resources');