<?php

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib;
use MSergeev\Core\Entity;

class UsersPropertiesTable extends Lib\DataManager
{
	public static function getTableName ()
	{
		return 'ms_core_users_properties';
	}

	public static function getTableTitle ()
	{
		return 'Свойства пользователей';
	}

	public static function getMap ()
	{
		return array(
			Lib\TableHelper::primaryField(),
			new Entity\IntegerField('USER_ID',array(
				'required' => true,
				'link' => 'ms_core_users.ID',
				'title' => 'ID пользователя'
			)),
			new Entity\StringField('PROPERTY_NAME',array(
				'required' => true,
				'title' => 'Код свойства'
			)),
			new Entity\TextField('PROPERTY_VALUE',array(
				'title' => 'Значение свойства'
			))
		);
	}
}