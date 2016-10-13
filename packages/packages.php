<?php

/*
 *
use \MSergeev\Core\Lib\Config;

$dir = Config::getConfig('PACKAGES_ROOT');
if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = @readdir($dh)) !== false) {
			if ($file != "." && $file != ".." && $file != "packages.php") {
				__include_once($dir . $file ."/include.php");

			}
		}
		@closedir($dh);
	}
}
*/
