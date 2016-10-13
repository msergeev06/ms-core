<?php

//echo "Open public directory: /msergeev/[package]/public/";

include_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev_config.php");
header('Content-type: text/html; charset=utf-8');
//MSergeev\Core\Lib\Loader::IncludePackage("dates");
//MSergeev\Core\Lib\Loader::IncludePackage("icar");
//MSergeev\Core\Lib\Loader::IncludePackage("apihelp");
//MSergeev\Core\Lib\Loader::IncludePackage("products");
//MSergeev\Core\Lib\Loader::IncludePackage("tasks");
//MSergeev\Core\Lib\Loader::IncludePackage("finances");
//MSergeev\Core\Lib\Loader::IncludePackage("calendar");
//MSergeev\Core\Lib\Loader::IncludePackage("owm");
//MSergeev\Core\Lib\Loader::IncludePackage("counters");


use MSergeev\Packages\Dates\Tables;
use MSergeev\Packages\ICar\Tables\CarGearboxTable;
use MSergeev\Core\Lib\Tools;
use MSergeev\Core\Lib\Installer;
use MSergeev\Core\Lib\Buffer;
use MSergeev\Core\Lib\Config;
use \MSergeev\Packages\Tasks\Lib as TaskLib;
Buffer::start("page");
Buffer::addJS(Config::getConfig("CORE_ROOT")."js/jquery-1.11.3.js");
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=Buffer::showTitle("Главная");?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=Buffer::showCSS()?>
	<?=Buffer::showJS()?>
</head>
<body>
<?


?>

</body></html>

