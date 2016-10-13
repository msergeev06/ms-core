<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class Buffer {

	protected static $pageTitle;
	protected static $includedCSS;
	protected static $includedJS;
	protected static $arIncludedCSS=array();
	protected static $arIncludedJS=array();
	protected static $arWebixJs=array();

	public static function start($name) {
		if ($name == "page") {
			ob_start('MSergeev\Core\Lib\Buffer::getPage');
		}
		else {
			ob_start();
		}
		static::$pageTitle="";
		static::$includedCSS="";
		static::$includedJS="";
	}

	public static function end() {
		ob_end_flush();
	}

	public static function getPage ($buffer) {
		$buffer = str_replace("#PAGE_TITLE#", static::$pageTitle, $buffer);
		$buffer = str_replace("#INCLUDED_CSS#", static::$includedCSS, $buffer);
		$buffer = str_replace("#INCLUDED_JS#", static::$includedJS, $buffer);
		$buffer = str_replace("#INCLUDED_WEBIX_JS#", static::generateWebixJs(), $buffer);

		return $buffer;
	}

	public static function setTitle ($title) {
		static::$pageTitle = $title;
	}

	public static function showTitle ($title=null) {
		if (!is_null($title) && static::$pageTitle == "") {
			static::setTitle($title);
		}

		return '#PAGE_TITLE#';
	}

	public static function getTitle () {
		return static::$pageTitle;
	}

	public static function addCSS ($path) {
		if (file_exists($path)) {
			$path = Tools::getSitePath($path);
			if (!in_array($path,static::$arIncludedCSS))
			{
				static::$arIncludedCSS[] = $path;
				if (static::$includedCSS != "")
				{
					static::$includedCSS .= "\t\t";
				}
				static::$includedCSS .= '<link href="'.$path.'" type="text/css"  rel="stylesheet" />'."\n";
			}
		}
	}

	public static function showCSS ($css=null) {
		if (!is_null($css)) {
			static::addCSS($css);
		}
		return '#INCLUDED_CSS#';
	}

	public static function addJS ($path) {
		if (file_exists($path)) {
			$path = Tools::getSitePath($path);
			if (!in_array($path,static::$arIncludedJS))
			{
				static::$arIncludedJS[] = $path;
				if (static::$includedJS != "")
				{
					static::$includedJS .= "\t\t";
				}
				static::$includedJS .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
			}
		}
	}

	public static function showJS ($js=null) {
		if (!is_null($js)) {
			static::addJS($js);
		}

		return '#INCLUDED_JS#';
	}

	public static function showWebixJS () {

		return '#INCLUDED_WEBIX_JS#';
	}

	public static function addWebixJs ($js=null, $key=null)
	{
		try
		{
			if(is_null($js))
			{
				throw new Exception\ArgumentNullException ('js');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		if (!is_null($key))
		{
			if (!isset(static::$arWebixJs[$key]))
			{
				static::$arWebixJs[$key] = $js;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			static::$arWebixJs[] = $js;
			return true;
		}
	}

	protected static function generateWebixJs ()
	{
		if (!empty(static::$arWebixJs))
		{
			$webixJS = '<script type="text/javascript" charset="utf-8">'."\n"
				."webix.ready(function(){\n"
				."webix.i18n.setLocale('ru-RU');\n";

			foreach (static::$arWebixJs as $key=>$js)
			{
				$webixJS .= $js;
			}

			$webixJS.= "});\n</script>";

			return $webixJS;
		}
		else
		{
			return '';
		}
	}
}