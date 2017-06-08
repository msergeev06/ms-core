<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

//echo __FILE__."<br>";

include_once(\MSergeev\Core\Lib\Config::getConfig('CORE_ROOT').'tools.php');
__include_once(\MSergeev\Core\Lib\Config::getConfig('CORE_ROOT').'core.php');
if (file_exists(\MSergeev\Core\Lib\Config::getConfig("MSERGEEV_ROOT").'init.php'))
{
	try
	{
		include_once (\MSergeev\Core\Lib\Config::getConfig("MSERGEEV_ROOT").'init.php');
	}
	catch (Exception $e)
	{

	}
}

//Вместо автоматической подгрузки всех пакетов, пакеты подгружаются классом Loader там, где необходимо
//__include_once(\MSergeev\Core\Lib\Config::getConfig('PACKAGES_ROOT').'packages.php');

