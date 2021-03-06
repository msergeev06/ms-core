<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

$DOCUMENT_ROOT = (($_SERVER["DOCUMENT_ROOT"]=='')?'/var/www':$_SERVER["DOCUMENT_ROOT"]);

$arConfig = array(
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'db_name',
	'DB_USER' => 'root',
	'DB_PASS' => '',
	'DOCUMENT_ROOT' => $DOCUMENT_ROOT."/",
	'SITE_ROOT' => $DOCUMENT_ROOT,
	'MSERGEEV_ROOT' => $DOCUMENT_ROOT.'/msergeev/',
	'CORE_ROOT' => $DOCUMENT_ROOT.'/msergeev/core/',
	'PACKAGES_ROOT' => $DOCUMENT_ROOT.'/msergeev/packages/',
	'LANG' => 'ru'
);
include_once($arConfig["CORE_ROOT"].'lib/config.php');
\MSergeev\Core\Lib\Config::init($arConfig);

include_once(\MSergeev\Core\Lib\Config::getConfig("MSERGEEV_ROOT").'include.php');