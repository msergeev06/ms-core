<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Loc
{
	protected static $arMessage = array();

	public static function setModuleMessages ($moduleName='',$prefix='ms_')
	{
		$prefix = strtolower($prefix);
		$moduleName = strtolower($moduleName);

		$lang = Config::getConfig('LANG');

		if ($moduleName == '' || $moduleName == 'core')
		{
			$root = Config::getConfig('CORE_ROOT');
			$prefix .= 'core_';
		}
		else
		{
			$root = Config::getConfig('PACKAGES_ROOT').$moduleName.'/';
			$prefix .= $moduleName.'_';
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