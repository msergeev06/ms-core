<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib;
use MSergeev\Core\Entity;

class SectionsTable extends Lib\DataManager
{
	public static function getTableName ()
	{
		return 'ms_core_sections';
	}
	public static function getTableTitle()
	{
		return 'Разделы';
	}
	public static function getTableLinks()
	{
		return array(
			'ID' => array(
				'ms_core_sections' => 'PARENT_SECTION_ID'
			)
		);
	}
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID',array(
				'primary' => true,
				'autocomplete' => true,
				'title' => 'ID раздела'
			)),
			Lib\TableHelper::activeField(),
			Lib\TableHelper::sortField(),
			new Entity\StringField('NAME',array(
				'required' => true,
				'title' => 'Название раздела'
			)),
			new Entity\IntegerField('LEFT_MARGIN',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Левая граница'
			)),
			new Entity\IntegerField('RIGHT_MARGIN',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Правая граница'
			)),
			new Entity\IntegerField('DEPTH_LEVEL',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Уровень вложенности'
			)),
			new Entity\IntegerField('PARENT_SECTION_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_core_sections.ID',
				'title' => 'Родительский раздел'
			))
		);
	}

	/**
	 * Добавляет индекс в таблицу
	 * Функция запускается автоматически после создания таблицы.
	 *
	 * @return bool|void
	 */
	public static function OnAfterCreateTable()
	{
		$sqlHelper = new Lib\SqlHelper(self::getTableName());
		$sql = "CREATE INDEX "
			.$sqlHelper->wrapQuotes('LEFT_MARGIN')." ON "
			.$sqlHelper->wrapTableQuotes()." ("
			.$sqlHelper->wrapQuotes('LEFT_MARGIN').", "
			.$sqlHelper->wrapQuotes('RIGHT_MARGIN').", "
			.$sqlHelper->wrapQuotes('DEPTH_LEVEL').")";
		$query = new Entity\Query('create');
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}