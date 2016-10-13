<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

use \MSergeev\Core\Lib;

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/data_base.php");
$DB = new Lib\DataBase();
$GLOBALS['DB'] = $DB;

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/options.php");
Lib\Options::init();

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/loader.php");
Lib\Loader::init();

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/users.php");
$USER = new Lib\Users();
$GLOBALS['USER'] = $USER;

//***** Exception ********
Lib\Loader::includeFiles(
	Lib\Config::getConfig('CORE_ROOT')."exception/",
	array( // Сначала загружаем эти в этом порядке
		"system_exception.php",
		"argument_exception.php",
		"db_exception.php",
		"sql_exception.php",
		"io_exception.php"
	)
);

//***** Lib *********
Lib\Loader::includeFiles(
	Lib\Config::getConfig('CORE_ROOT')."lib/",
	array(),
	array( // Не загружаем, так как были загружены ранее
		"data_base.php",
		"options.php",
		"config.php",
		"loader.php",
		"users.php"
	)
);

Lib\Loc::setModuleMessages();

//***** Entity ********
Lib\Loader::includeFiles(
	Lib\Config::getConfig('CORE_ROOT')."entity/",
	array( // Сначала загружаем эти в этом порядке
		"field.php",
		"scalar_field.php",
		"date_field.php"
	)
);

//***** Tables ********
Lib\Loader::includeFiles(Lib\Config::getConfig('CORE_ROOT')."tables/");

