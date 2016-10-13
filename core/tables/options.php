<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Tables;

use MSergeev\Core\Entity;
use MSergeev\Core\Lib\DataManager;

class OptionsTable extends DataManager {

	public static function getTableName () {
		return "ms_core_options";
	}

	public static function getTableTitle() {
		return "Таблица настроек";
	}

	public static function getMap () {
		return array(
			new Entity\IntegerField ('ID', array(
				"primary" => true,
				"autocomplete" => true,
				"title" => 'ID настройки'
			)),
			new Entity\StringField ('NAME', array(
				"title" => 'Имя настройки'
			)),
			new Entity\StringField ('VALUE', array(
				"title" => "Значение настройки"
			))
		);
	}

}