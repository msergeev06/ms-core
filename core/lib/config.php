<?php
/**
 * MSergeev\Core\Lib\Config
 * Главный конфигурационный класс. Позволяет загружать, добавлять, получать конфигурационные
 * параметры ядра и дополнительных пакетов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Config {

	/**
	 * @var array Массив конфигурационных параметров
	 */
	protected static $arConfig;

	/**
	 * Инициализирующая функция. Принимает массив первоначальных параметров
	 *
	 * @api
	 *
	 * @param array $arConfig Массив первоначальных параметров
	 */
	public static function init ($arConfig = array()) {
		if (!empty($arConfig)) {
			static::$arConfig = $arConfig;
		}
	}

	/**
	 * Добавляет новый параметер и его значение к массиву параметров для данной сессии
	 *
	 * @api
	 *
	 * @param string $name Название параметра. Преобразуется к верхнему регистру
	 * @param string $value Значение параметра
	 */
	public static function addConfig ($name, $value) {
		$name = strtoupper($name);
		static::$arConfig[$name] = $value;
	}

	/**
	 * Возвращает значение ранее добавленного параметра
	 *
	 * @api
	 *
	 * @param string $name Название параметра. Преобразуется к верхнему регистру
	 *
	 * @return string|bool Значение параметра, либо false, если параметера не существует
	 */
	public static function getConfig ($name) {
		$name = strtoupper($name);
		if (isset(static::$arConfig[$name])) {
			return static::$arConfig[$name];
		}
		else {
			return false;
		}
	}
}