<?php
/**
 * MSergeev\Core\Lib\Loader
 * Отвечает за загрузку пакетов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Loader {

	/**
	 * @var array Список установленных пакетов и их параметров
	 */
	private static $arPackage;

	/**
	 * @var string Полный путь к установленным пакетам
	 */
	private static $packagesRoot;

	/**
	 * @var string Полный путь к директории загрузки пользовательских файлов
	 */
	private static $uploadRoot;

	/**
	 * @var array Список уже загруженных пакетов
	 */
	private static $arIncludedPackages;

	/**
	 * Инициализация пакетов. Создает список установленных пакетов
	 *
	 * @api
	 */
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
						if (file_exists(self::$packagesRoot.$file.'/version.php'))
						{
							self::$arPackage[$file]['INSTALLED_VERSION'] = include(self::$packagesRoot.$file.'/version.php');
						}
					}
				}
				@closedir($dh);
			}
		}
	}

	/**
	 * Возвращает номер версии пакета, если она задана, либо false
	 *
	 * @param string $packageName Имя пакета
	 *
	 * @return string|bool Строковое значение версии, либо false
	 */
	public static function getPackageVersion ($packageName="")
	{
		if ($packageName!="" && isset(self::$arPackage[$packageName]['INSTALLED_VERSION']['VERSION']))
		{
			return self::$arPackage[$packageName]['INSTALLED_VERSION']['VERSION'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает дату версии пакета, если она задана, либо false
	 *
	 * @param string $packageName Имя пакета
	 *
	 * @return string|bool Строковое представление даты версии пакета, либо false
	 */
	public static function getPackageVersionDate ($packageName="")
	{
		if ($packageName!="" && isset(self::$arPackage[$packageName]['INSTALLED_VERSION']['VERSION_DATE']))
		{
			return self::$arPackage[$packageName]['INSTALLED_VERSION']['VERSION_DATE'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает данные о версии всех установленных пакетов, либо false
	 *
	 * @return array|bool
	 */
	public static function getArrayPackagesVersions ()
	{
		$arVersions = array();
		foreach (self::$arPackage as $package=>$arData)
		{
			if (isset($arData['INSTALLED_VERSION']))
			{
				$arVersions[$package] = $arData['INSTALLED_VERSION'];
			}
		}

		if (!empty($arVersions))
		{
			return $arVersions;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Подключает классы указанного пакета
	 *
	 * @api
	 *
	 * @param string $namePackage Имя пакета
	 *
	 * @return bool true - если пакет подключен или уже был подключен, иначе false
	 */
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
				//$arPackageDefaultOptions = array();
				$arPackageDefaultOptions = __include_once(self::$packagesRoot.$namePackage."/default_options.php");
				if (isset($arPackageDefaultOptions) && !empty($arPackageDefaultOptions))
				{
					foreach ($arPackageDefaultOptions as $optionName=>$optionValue)
					{
						if ($optionName=="TEMPLATE")
						{
							$defTempl = $optionValue;
						}
						Options::setDefaultOption($optionName,$optionValue);
					}
				}
			}
			//Если код шаблона не был установлен, устанавливаем шаблон по-умолчанию .default
			if (!$defTempl)
			{
				$defTempl = '.default';
			}
			//Загружаем языковые файлы для пакета
			Loc::setPackageMessages($namePackage);
			//Путь к публичной директории
			self::$arPackage[$namePackage]["PUBLIC"] = self::$packagesRoot.$namePackage."/public/";
			//Путь к директории с описанием таблиц пакета
			self::$arPackage[$namePackage]["TABLES"] = self::$packagesRoot.$namePackage."/tables/";
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
		elseif (isset(self::$arIncludedPackages[$namePackage]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @deprecated
	 * @see Loader::IncludePackage
	 */
	public static function IncludeModule ($nameModule=null)
	{
		return self::IncludePackage($nameModule);
	}

	/**
	 * Проверяет, установлен ли указанный пакет
	 *
	 * @api
	 *
	 * @param string $namePackage Имя пакета
	 *
	 * @return bool TRUE - установлен, FALSE в противном случае
	 */
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

	/**
	 * Возвращает полный путь к публичной директории пакета
	 *
	 * @api
	 *
	 * @param string $namePackage Имя пакета
	 *
	 * @return string|bool  Строка - путь, либо false
	 */
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

	/**
	 * Возвращает путь от корня сайта к публичной директории пакета
	 *
	 * @api
	 *
	 * @param string $namePackage   Имя пакета
	 *
	 * @return string|bool Путь или false
	 */
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

	/**
	 * Возвращает полный путь к директории с описанием таблиц пакета
	 *
	 * @api
	 *
	 * @param string $namePackage Имя пакета
	 *
	 * @return string|bool  Строка - путь, либо false
	 */
	public static function getTables ($namePackage=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]))
		{
			return static::$arPackage[$namePackage]["TABLES"];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает полный путь к текущему шаблону пакета
	 *
	 * @api
	 *
	 * @param string $namePackage   Имя пакета
	 *
	 * @return string|bool  Путь, илбо false
	 */
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

	public static function setTemplate ($namePackage=null, $templateName=null)
	{
		if (!is_null($namePackage) && isset(static::$arPackage[$namePackage]) && !is_null($templateName))
		{
			//Путь к действующему шаблону пакета
			static::$arPackage[$namePackage]["TEMPLATE"] = static::$packagesRoot.$namePackage."/templates/".$templateName."/";
			//Относительный путь к действующему шаблону пакета
			static::$arPackage[$namePackage]["SITE_TEMPLATE"] = str_replace(Config::getConfig("SITE_ROOT"),"",static::$arPackage[$namePackage]["TEMPLATE"]);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает путь от корня сайта к текущему шаблону пакета
	 *
	 * @api
	 *
	 * @param string $namePackage   Имя пакета
	 *
	 * @return string|bool  Путь, либо false
	 */
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

	/**
	 * Возвращает полный путь к директории загрузки пользовательских файлов пакета
	 *
	 * @api
	 *
	 * @param string $namePackage   Имя пакета
	 *
	 * @return string|bool  Путь, либо false
	 */
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

	/**
	 * Подключает файлы из указанной директории
	 *
	 * @api
	 *
	 * @param string $dir        Директория из которой подключаются файлы
	 * @param array  $firstLoad  Массив пустой, либо содержащий список файлов, которые необходимо
	 *                           грузить первыми, причем в указанном порядке
	 * @param array  $noLoad     Массив пустой, либо содержащий список файлов, которые не нужно
	 *                           загружать. Если пустой, автоматически игнорируются:
	 *                           '.', '..', '.readme'
	 */
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
				\__include_once($dir.$files);
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
						$file = str_replace($dir,'',$file);
						\__include_once($dir . $file);
					}
				}
				closedir($dh);
			}
		}

	}
}