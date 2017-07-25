<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

use \MSergeev\Core\Lib;
use MSergeev\Core\Entity;

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/data_base.php");
$DB = new Lib\DataBase();
$GLOBALS['DB'] = $DB;

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/options.php");
Lib\Options::init();

__include_once(Lib\Config::getConfig('CORE_ROOT')."lib/loader.php");
Lib\Loader::init();

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
	array( // Сначала загружаем эти в этом порядке
		"sql_helper.php"
	),
	array( // Не загружаем, так как были загружены ранее
		"data_base.php",
		"options.php",
		"config.php",
		"loader.php"
	)
);

//***** Loc *********
Lib\Loc::setPackageMessages();

//***** Entity ********
Lib\Loader::includeFiles(
	Lib\Config::getConfig('CORE_ROOT')."entity/",
	array( // Сначала загружаем эти в этом порядке
		"field.php",
		"scalar_field.php",
		"date_field.php",
		"string_field.php"
	),
	array( //Не загружаем, так как были загружены ранее
		"user.php"
	)
);

//***** Tables ********
Lib\Loader::includeFiles(Lib\Config::getConfig('CORE_ROOT')."tables/");

__include_once(Lib\Config::getConfig('CORE_ROOT')."entity/user.php");
$USER = new Entity\User();
$GLOBALS['USER'] = $USER;


//Проверяем используется ли сборка на MajorDoMo. Если да и пакет Majordomo установлен, подключаем его
if (Lib\Config::getConfig('USE_MAJORDOMO') && Lib\Loader::issetPackage('majordomo'))
{
	Lib\Loader::IncludePackage('majordomo');

	if (!Lib\Config::getConfig('MAJORDOMO_CYCLES'))
	{
		//Проверяем необходимость http-авторизации и при необходимости предлагаем авторизоваться
		\MSergeev\Packages\Majordomo\Lib\Http::checkAutorize();
	}
}

//Проверяем используется ли сборка KuzmaHome. Если да и пакет KuzmaHome установлен, подключаем его
if (Lib\Config::getConfig('USE_KUZMAHOME') && Lib\Loader::issetPackage('kuzmahome'))
{
	Lib\Loader::IncludePackage('kuzmahome');

	if (Lib\Config::getConfig('HTTP_AUTH'))
	{
		//Проверяем необходимость http-авторизации и при необходимости предлагаем авторизоваться
		\MSergeev\Packages\KuzmaHome\Lib\Http::checkAutorize();
	}
}

