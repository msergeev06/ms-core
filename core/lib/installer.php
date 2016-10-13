<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception\ObjectException;
use MSergeev\Core\Lib\Loader;

class Installer
{
	public static function createPackageTables ($strPackageName)
	{
		$strPackageName = strtolower($strPackageName);
		Loader::IncludePackage($strPackageName);
		$strBigPackageName = strtoupper($strPackageName);
		$strFirstBigPackageName = Tools::setFirstCharToBig ($strPackageName);
		$strPackageRoot = Config::getConfig($strBigPackageName."_ROOT");
		//msDebug($strPackageRoot);
		$strPackageTablesNamespace = "MSergeev\\Packages\\".$strFirstBigPackageName."\\Tables\\";
		$dir = $strPackageRoot."tables/";

		$arTables = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != "." && $file != ".." && $file != ".readme")
					{
						$arTables[] = str_replace(".php","",$file);
					}
				}
				closedir($dh);
			}
		}

		foreach ($arTables as $fileTable)
		{
			if ($arClass = explode("_",$fileTable))
			{
				$className = "";
				foreach ($arClass as $strName)
				{
					$className .= Tools::setFirstCharToBig($strName);
				}
			}
			else
			{
				$className = Tools::setFirstCharToBig($arClass);
			}
			$className .= "Table";

			//call_user_func(array($strPackageTablesNamespace.$className,"installTable()"));
			$runClass = $strPackageTablesNamespace.$className;
			$runClass::createTable();
			$runClass::insertDefaultRows();
			//forward_static_call(array($strPackageTablesNamespace.$className,"installTable()"));
			//msDebug($strPackageTablesNamespace.$className."::installTable()");
		}

	}
}