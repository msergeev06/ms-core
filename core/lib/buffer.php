<?php
/**
 * MSergeev\Core\Lib\Buffer
 * Буферизация вывода в браузер. Позволяет использовать отложенные функции
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class Buffer {

	/**
	 * @var string Title страницы
	 */
	protected static $pageTitle;

	/**
	 * @var string Подключенные на странице файлы CSS
	 */
	protected static $includedCSS;

	/**
	 * @var string Подключенные на странице файлы JS
	 */
	protected static $includedJS;

	/**
	 * @var array Массив подключаемых на странице файлов CSS
	 */
	protected static $arIncludedCSS=array();

	/**
	 * @var array Массив подключаемых на странице файлов JS
	 */
	protected static $arIncludedJS=array();

	/**
	 * @var array Массив подключаемых на странице файлов JS, библиотеки Webix
	 */
	protected static $arWebixJs=array();

	/**
	 * Инициализирует вывод в буфер
	 *
	 * @api
	 *
	 * @param string $name Для какого объекта используется буферизация
	 */
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

	/**
	 * Выводит собранный буфер на страницу
	 *
	 * @api
	 *
	 */
	public static function end() {
		ob_end_flush();
	}

	/**
	 * Обработчик вывода буфера на страницу
	 *
	 * @api
	 *
	 * @param string $buffer Строка содержащая буфер вывода
	 *
	 * @return string Возвращается обработанный буфер
	 */
	public static function getPage ($buffer) {
		$buffer = str_replace("#PAGE_TITLE#", static::$pageTitle, $buffer);
		$buffer = str_replace("#INCLUDED_CSS#", static::$includedCSS, $buffer);
		$buffer = str_replace("#INCLUDED_JS#", static::$includedJS, $buffer);
		$buffer = str_replace("#INCLUDED_WEBIX_JS#", static::generateWebixJs(), $buffer);

		return $buffer;
	}

	/**
	 * Устанавливает title страницы. Будет установлен вместо тега #PAGE_TITLE# на странице при вызове
	 * обработчика буферизации
	 *
	 * @api
	 *
	 * @param string $title Заголовок страницы
	 */
	public static function setTitle ($title) {
		static::$pageTitle = $title;
	}

	/**
	 * Устанавливает title страницы, если указан title и не был установлен ранее
	 * и добавляет тег #PAGE_TITLE# на страницу.
	 *
	 * @api
	 *
	 * @param string|null $title Заголовок станицы или ничего
	 *
	 * @return string тег #PAGE_TITLE#
	 */
	public static function showTitle ($title=null) {
		if (!is_null($title) && static::$pageTitle == "") {
			static::setTitle($title);
		}

		return '#PAGE_TITLE#';
	}

	/**
	 * Возвращает установленный title страницы
	 *
	 * @api
	 *
	 * @return string Title станицы
	 */
	public static function getTitle () {
		return static::$pageTitle;
	}

	/**
	 * Добавляет указанный путь к загружаемым файлам CSS на странице. Самостоятельно генерирует код,
	 * добавлемый в head страницы
	 *
	 * @api
	 *
	 * @param string $path Путь к файлу CSS
	 */
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

	/**
	 * Если параметер указан, он добавляется к списку подключаемых файлов CSS.
	 * Также возвращает тег #INCLUDED_CSS# который будет заменен на код подключаемых
	 * файлов CSS
	 *
	 * @api
	 *
	 * @param string|null $css Подключаемый файл CSS, либо ничего
	 *
	 * @return string Тег #INCLUDED_CSS#
	 */
	public static function showCSS ($css=null) {
		if (!is_null($css)) {
			static::addCSS($css);
		}
		return '#INCLUDED_CSS#';
	}

	/**
	 * Добавляет указанный путь к загружаемым файлам JS на странице. Самостоятельно генерирует код,
	 * добавлемый в head страницы
	 *
	 * @api
	 *
	 * @param string $path Путь к файлу JS
	 */
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

	/**
	 * Если параметер указан, он добавляется к списку подключаемых файлов JS.
	 * Также возвращает тег #INCLUDED_JS# который будет заменен на код подключаемых
	 * файлов JS
	 *
	 * @api
	 *
	 * @param string|null $js Подключаемый файл JS, либо ничего
	 *
	 * @return string Тег #INCLUDED_JS#
	 */
	public static function showJS ($js=null) {
		if (!is_null($js)) {
			static::addJS($js);
		}

		return '#INCLUDED_JS#';
	}

	/**
	 * Также возвращает тег #INCLUDED_WEBIX_JS# который будет заменен на код подключаемых
	 * файлов JS библиотеки Webix
	 *
	 * @api
	 *
	 * @return string Тег #INCLUDED_WEBIX_JS#
	 */
	public static function showWebixJS () {

		return '#INCLUDED_WEBIX_JS#';
	}

	/**
	 * Добавляет JS файл к списку подключаемых файлов библиотеки Webix
	 *
	 * @api
	 *
	 * @param string $js Путь к JS файлу
	 * @param string|null $key Идентификатор подключаемого файла, для избежания повторного подключения
	 *
	 * @return bool true - удалось добавить JS файл, false - в противном случае
	 */
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

	/**
	 * Генерирует код подключения JS библиотеки Webix
	 *
	 * @return string Код подключения библиотеки Webix
	 */
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