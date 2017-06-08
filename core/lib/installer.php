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

		$arTables = self::getPackageTables($strPackageName);
		if (empty($arTables))
		{
			return;
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

	public static function getPackageTables ($strPackageName)
	{
		Loader::IncludePackage($strPackageName);
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

		return $arTables;
	}

	public static function getPackageTableNames ($strPackageName)
	{
		$arTables = self::getPackageTables($strPackageName);
		foreach ($arTables as &$tableName)
		{
			$tableName = 'ms_'.strtolower($strPackageName).'_'.$tableName;
		}
		unset($tableName);

		return $arTables;
	}

	public static function createBackupDbForPackage ($strPackageName)
	{
		global $DB;

		$arTables = self::getPackageTableNames($strPackageName);
		exec($DB->getDumpCommand(Config::getConfig('DIR_BACKUP_DB'),false,$strPackageName,$arTables));
	}

	public static function restoreFromDump ()
	{
		//mysql -uUSER -pPASS DATABASE < /path/to/dump.sql //Проверенно, работает
		//gunzip < /path/to/outputfile.sql.gz | mysql -u USER -pPASSWORD DATABASE //Не проверял
	}

	/**
	 * Создает таблицы ядра
	 *
	 * @api
	 *
	 */
	public static function createCoreTables ()
	{
		$strTablesNamespace = "MSergeev\\Core\\Tables\\";
		$dir = Config::getConfig("CORE_ROOT").'tables/';
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

			$runClass = $strTablesNamespace.$className;
			$runClass::createTable();
			$runClass::insertDefaultRows();
		}
	}

	/**
	 * Returns TRUE if version1 >= version2
	 *
	 * @param string $version1 Версия пакета. Формат "XX.XX.XX"
	 * @param string $version2 Версия пакета. Формат "XX.XX.XX"
	 *
	 * @return bool
	 */
	public static function checkVersion($version1, $version2)
	{
		$arr1 = explode(".",$version1);
		$arr2 = explode(".",$version2);
		if (intval($arr2[0])>intval($arr1[0])) return false;
		elseif (intval($arr2[0])<intval($arr1[0])) return true;
		else
		{
			if (intval($arr2[1])>intval($arr1[1])) return false;
			elseif (intval($arr2[1])<intval($arr1[1])) return true;
			else
			{
				if (intval($arr2[2])>intval($arr1[2])) return false;
				elseif (intval($arr2[2])<intval($arr1[2])) return true;
				else return true;
			}
		}
	}
}