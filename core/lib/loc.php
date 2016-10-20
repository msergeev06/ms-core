<?php
/**
 * MSergeev\Core\Lib\Loc
 * Локализация ядра и пакетов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Loc
{
	/**
	 * @var array Массив сообщений
	 */
	protected static $arMessage = array();

	/**
	 * Функция загружает все сообщения для текущего языка для указанного пакета
	 * Вызывается при подключении пакета в Loader::IncludePackage, дополнительного
	 * вызова не требует
	 *
	 * @api
	 *
	 * @param string $packageName Имя пакета (по-умолчанию '' или 'core')
	 * @param string $prefix      Префикс (по-умолчанию 'ms_')
	 */
	public static function setPackageMessages ($packageName='',$prefix='ms_')
	{
		$prefix = strtolower($prefix);
		$packageName = strtolower($packageName);

		$lang = Config::getConfig('LANG');

		if ($packageName == '' || $packageName == 'core')
		{
			$root = Config::getConfig('CORE_ROOT');
			$prefix .= 'core_';
		}
		else
		{
			$root = Config::getConfig('PACKAGES_ROOT').$packageName.'/';
			$prefix .= $packageName.'_';
		}

		$dir = $root.'loc/'.$lang.'/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..')
					{
						$arMessages = array();
						require_once ($dir . $file);
						foreach ($arMessages as $field=>$text)
						{
							static::$arMessage[strtoupper($prefix).strtoupper($field)] = $text;
						}
					}
				}
				closedir($dh);
			}
		}
	}

	/**
	 * Устаревшая обертка для setPackageMessages
	 *
	 * @deprecated
	 * @see Loc::setPackageMessages
	 * @ignore
	 *
	 * @param string $moduleName
	 * @param string $prefix
	 */
	public static function setModuleMessages ($moduleName='',$prefix='ms_')
	{
		self::setPackageMessages($moduleName, $prefix);
	}

	/**
	 * Возвращает локализованный текст, заменяя теги указанными значениями
	 *
	 * @api
	 *
	 * @param string $name      Код сообщения
	 * @param array  $arReplace Массив замен вида код_тега=>замена
	 *
	 * @return mixed
	 */
	public static function getMessage ($name,$arReplace=array())
	{
		$message = static::$arMessage[strtoupper($name)];
		if (!empty($arReplace))
		{
			foreach ($arReplace as $field=>$value)
			{
				$message = str_replace('#'.$field.'#',$value,$message);
			}
		}

		return $message;
	}

	/**
	 * Возвращает массив, содержащий все локализованнты тексты указанного пакета
	 *
	 * @api
	 *
	 * @param string $name   Имя пакета или '' == 'core'
	 * @param string $prefix Префикс (по-умолчанию 'ms_')
	 *
	 * @return array
	 */
	public static function showAllMessagesModule ($name='',$prefix='ms_')
	{
		if ($name=='') $name='core';
		$prefix .= $name.'_';
		$prefix = strtoupper($prefix);

		$arMessages = array();
		$arMess = static::$arMessage;
		foreach ($arMess as $field=>$value)
		{
			if (strstr($field,$prefix) !== false)
			{
				$arMessages[$field]=$value;
			}
		}

		return $arMessages;
	}
}