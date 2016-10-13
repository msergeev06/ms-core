<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Loader {

	private static $arPackage;
	private static $packagesRoot;
	private static $uploadRoot;
	private static $arIncludedPackages;

	public static function init () {
		self::$packagesRoot = Config::getConfig("PACKAGES_ROOT");
		self::$uploadRoot = Config::getConfig("MSERGEEV_ROOT")."upload/";
		if (is_dir(self::$packagesRoot))
		{
			if ($dh = opendir(self::$packagesRoot))
			{
				while (($file = @readdir($dh)) !== false)
				{
					if ($file != "." && $file != ".." && $file != "packages.php")
					{
						self::$arPackage[$file] = array();
					}
				}
				@closedir($dh);
			}
		}
	}

	public static function IncludePackage ($namePackage=null)
	{
		//Если имя пакета задано, пакет установлен и не был загружен ранее
		if (!is_null($namePackage) && isset(self::$arPackage[$namePackage]) && !isset(self::$arIncludedPackages[$namePackage]))
		{
			//Существует ли файл зависимостей пакетов
			if (file_exists(self::$packagesRoot.$namePackage."/required.php"))
			{
				//Подключаем файл зависимостей пакетов
				__include_once(self::$packagesRoot.$namePackage."/required.php");
				//Если массив обязательных пакетов не пуст
				if (!empty($arRequiredPackages))
				{
					//Смотрим все описанные пакеты
					foreach ($arRequiredPackages as $required)
					{
						//Если требуемый пакет установлен
						if (isset(self::$arPackage[$required]))
						{
							//Если требуемый пакет не был еще подключен
							if (!isset(self::$arIncludedPackages[$required]))
							{
								//Запускаем сами себя для подключения требуемого пакета
								self::IncludePackage($required);
							}
						}
						//Если пакет не установлен - умираем с ошибкой.
						else
						{
							die("ERROR-[".$namePackage."]: Необходимо установить обязательный пакет [".$required."]");
						}
					}
				}
				//Если массив дополнительных пакетов не пуст
				if (!empty($arAdditionalPackages))
				{
					//Смотрим все описанные пакеты
					foreach ($arAdditionalPackages as $additional)
					{
						//Если требуемый пакет не установлен и не был еще подключен
						if (isset(self::$arPackage[$additional]) && !isset(self::$arIncludedPackages[$additional]))
						{
							//Запускаем сами себя для подключения требуемого пакета
							self::IncludePackage($additional);
						}
					}
				}
			}
			//Подключаем основной файл пакета
			__include_once(self::$packagesRoot.$namePackage."/include.php");
			$defTempl = false;
			//Если у пакета есть файл Опций по-умолчанию
			if (file_exists(self::$packagesRoot.$namePackage."/default_options.php"))
			{
				//Сохраняем их
				$arPackageDefaultOptions = array();
				__include_once(self::$packagesRoot.$namePackage."/default_options.php");
				if (isset($arPackageDefaultOptions) && !empty($arPackageDefaultOptions))
				{
					foreach ($arPackageDefaultOptions as $optionName=>$optionValue)
					{
						if ($optionName=="TEMPLATE")
						{
							$defTempl = $optionValue;
						}
						Options::setPackageDefaultOption($optionName,$optionValue);
					}
				}
			}
			//Если код шаблона не был установлен, устанавливаем шаблон по-умолчанию .default
			if (!$defTempl)
			{
				$defTempl = '.default';
			}
			//Загружаем языковые файлы для пакета
			Loc::setModuleMessages($namePackage);
			//Путь к публичной директории
			self::$arPackage[$namePackage]["PUBLIC"] = self::$packagesRoot.$namePackage."/public/";
			//Относительный путь к публичной директории
			self::$arPackage[$namePackage]["SITE_PUBLIC"] = str_replace(Config::getConfig('SITE_ROOT'),"",self::$arPackage[$namePackage]["PUBLIC"]);
			self::$arPackage[$namePackage]["SITE_PUBLIC"] = str_replace('\\',"/",self::$arPackage[$namePackage]["SITE_PUBLIC"]);
			//Путь к загружаемым файлам пакета
			self::$arPackage[$namePackage]["UPLOAD"] = static::$uploadRoot.$namePackage."/";
			//Путь к действующему шаблону пакета
			static::$arPackage[$namePackage]["TEMPLATE"] = static::$packagesRoot.$namePackage."/templates/".$defTempl."/";
			//Относительный путь к действующему шаблону пакета
			static::$arPackage[$namePackage]["SITE_TEMPLATE"] = str_replace(Config::getConfig("SITE_ROOT"),"",static::$arPackage[$namePackage]["TEMPLATE"]);
			//Устанавливаем флаг успешной загрузки пакета
			self::$arIncludedPackages[$namePackage] = true;
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function issetPackage ($namePackage=null) {
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getPublic ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["PUBLIC"];
		}
		else
		{
			return false;
		}
	}

	public static function getSitePublic ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["SITE_PUBLIC"];
		}
		else
		{
			return false;
		}
	}

	public static function getTemplate ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["TEMPLATE"];
		}
		else
		{
			return false;
		}
	}

	public static function getSiteTemplate ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["SITE_TEMPLATE"];
		}
		else
		{
			return false;
		}
	}

	public static function getUpload ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["UPLOAD"];
		}
		else
		{
			return false;
		}
	}

	public static function includeFiles ($dir, $firstLoad=array(), $noLoad=array())
	{
		//$dir = Config::getConfig('CORE_ROOT')."lib/";
		if (empty($noLoad))
		{
			$noLoad = array(".","..");
		}
		else
		{
			if (!in_array(".",$noLoad))
			{
				$noLoad[] = ".";
			}
			if (!in_array("..",$noLoad))
			{
				$noLoad[] = "..";
			}
			if (!in_array(".readme",$noLoad))
			{
				$noLoad[] = ".readme";
			}
		}

		if (!empty($firstLoad))
		{
			foreach ($firstLoad as $files)
			{
				__include_once($dir.$files);
				$noLoad[] = $files;
			}
		}
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (!in_array($file,$noLoad))
					{
						__include_once($dir . $file);
					}
				}
				closedir($dh);
			}
		}

	}
}