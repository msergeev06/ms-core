<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

$arConfig = array(
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'db_name',
	'DB_USER' => 'root',
	'DB_PASS' => '',
	'DOCUMENT_ROOT' => $_SERVER["DOCUMENT_ROOT"]."/",
	'SITE_ROOT' => $_SERVER["DOCUMENT_ROOT"],
	'MSERGEEV_ROOT' => $_SERVER["DOCUMENT_ROOT"].'/msergeev/',
	'CORE_ROOT' => $_SERVER["DOCUMENT_ROOT"].'/msergeev/core/',
	'PACKAGES_ROOT' => $_SERVER["DOCUMENT_ROOT"].'/msergeev/packages/',
	'LANG' => 'ru'
);
include_once($arConfig["CORE_ROOT"].'lib/config.php');
\MSergeev\Core\Lib\Config::init($arConfig);

include_once(\MSergeev\Core\Lib\Config::getConfig("MSERGEEV_ROOT").'include.php');