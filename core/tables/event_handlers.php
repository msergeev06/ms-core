<?php

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class EventHandlersTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_core_event_handlers';
	}

	public static function getTableTitle ()
	{
		return 'Обработчики событий';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			TableHelper::sortField(array('default_value'=>100)),
			new Entity\StringField('FROM_PACKAGE',array(
				'required' => true,
				'title' => 'Идентификатор пакета инициирующий событие'
			)),
			new Entity\StringField('EVENT_ID',array(
				'required' => true,
				'title' => 'Идентификатор события'
			)),
			new Entity\StringField('TO_PACKAGE_ID',array(
				'title' => 'Идентификатор пакета содержащий функцию-обработчик события'
			)),
			new Entity\StringField('TO_CLASS',array(
				'title' => 'Класс принадлежащий пакету TO_PACKAGE_ID'
			)),
			new Entity\StringField('TO_METHOD',array(
				'title' => 'Метод класса $toClass являющийся функцией-обработчиком события'
			)),
			new Entity\StringField('TO_PATH',array(
				'title' => 'Относительный путь к исполняемому файлу'
			)),
			new Entity\StringField('FULL_PATH',array(
				'title' => 'Полный путь к исполняемому файлу'
			)),
			new Entity\TextField('TO_METHOD_ARG',array(
				'serialized' => true,
				'title' => 'Массив аргументов для функции-обработчика событий'
			))
		);
	}
}