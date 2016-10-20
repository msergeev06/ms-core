<?php
/**
 * MSergeev\Core\Lib\Installer
 * Установщик модулей и их параметров
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Installer
{
	/**
	 * Создает таблицы указанного пакета
	 *
	 * @api
	 *
	 * @param string $strPackageName Имя пакета
	 */
	public static function createPackageTables ($strPackageName)
	{
		$strPackageName = strtolower($strPackageName);
		Loader::IncludePackage($strPackageName);
		$strFirstBigPackageName = Tools::setFirstCharToBig ($strPackageName);
		$strPackageTablesNamespace = "MSergeev\\Packages\\".$strFirstBigPackageName."\\Tables\\";
		$dir = Loader::getTables($strPackageName);

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

			$runClass = $strPackageTablesNamespace.$className;
			$runClass::createTable();
			$runClass::insertDefaultRows();
		}
	}
}