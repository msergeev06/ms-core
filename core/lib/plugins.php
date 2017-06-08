<?php

namespace MSergeev\Core\Lib;

class Plugins
{
	private static $arIncluded = array();

	public static function includeMaskedInput()
	{
		//https://itchief.ru/lessons/javascript/input-mask-for-html-input-element
		if (!isset(self::$arIncluded['masked-input']))
		{
			self::$arIncluded['masked-input'] = true;
			Buffer::addJS(Config::getConfig("CORE_ROOT")."plugins/jquery.maskedinput/jquery.maskedinput.min.js");
		}
	}

	public static function includeInputCalendar ()
	{
		if (!isset(self::$arIncluded['input-calendar']))
		{
			self::$arIncluded['input-calendar'] = true;
			Buffer::addJS (Config::getConfig ("CORE_ROOT")."js/calendar.js");
		}
	}

	public static function includeBootstrapCss ($minimize=true)
	{
		if (!isset(self::$arIncluded['bootstrap-css']))
		{
			self::$arIncluded['bootstrap-css'] = true;
			if ($minimize===true)
			{
				Buffer::addCSS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/css/bootstrap.min.css");
			}
			else
			{
				Buffer::addCSS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/css/bootstrap.css");
			}
		}
	}

	public static function includeBootstrapThemeCss ($minimize=true)
	{
		if (!isset(self::$arIncluded['bootstrap-theme-css']))
		{
			self::$arIncluded['bootstrap-theme-css'] = true;
			if ($minimize===true)
			{
				Buffer::addCSS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/css/bootstrap-theme.min.css");
			}
			else
			{
				Buffer::addCSS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/css/bootstrap-theme.css");
			}
		}
	}

	public static function includeBootstrapJs ($minimize=true)
	{
		if (!isset(self::$arIncluded['bootstrap-js']))
		{
			self::$arIncluded['bootstrap-js'] = true;
			if ($minimize===true)
			{
				Buffer::addJS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/js/bootstrap.min.js");
			}
			else
			{
				Buffer::addJS(Config::getConfig("CORE_ROOT")."plugins/bootstrap/js/bootstrap.js");
			}
		}
	}

	public static function includeMagnificPopup($minimize=true)
	{
		if (!isset(self::$arIncluded['magnific-popup']))
		{
			self::$arIncluded['magnific-popup'] = true;
			if ($minimize)
			{
				Buffer::addJS(Config::getConfig("CORE_ROOT")."plugins/magnific.popup/jquery.magnific-popup.min.js");
			}
			else
			{
				Buffer::addJS(Config::getConfig("CORE_ROOT")."plugins/magnific.popup/jquery.magnific-popup.js");
			}
			Buffer::addCSS(Config::getConfig("CORE_ROOT")."plugins/magnific.popup/magnific-popup.css");
		}
	}
}