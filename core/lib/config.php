<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Config {
	protected static $arConfig;

	public static function init ($arConfig = array()) {
		if (!empty($arConfig)) {
			static::$arConfig = $arConfig;
		}
	}

	public static function addConfig ($name, $value) {
		$name = strtoupper($name);
		static::$arConfig[$name] = $value;
	}

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