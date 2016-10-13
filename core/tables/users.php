<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;

class UsersTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_core_users';
	}

	public static function getTableTitle ()
	{
		return 'Пользователи';
	}

	public static function getMap ()
	{
		return array(
			new Entity\IntegerField('ID',array(
				'primary' => true,
				'autocomplete' => true,
				'title' => 'ID пользователя'
			)),
			new Entity\StringField('LOGIN',array(
				'required' => true,
				'title' => 'Логин'
			)),
			new Entity\StringField('PASSWORD',array(
				'required' => true,
				'title' => 'Пароль'
			)),
			new Entity\StringField('EMAIL',array(
				'required' => true,
				'title' => 'Email'
			)),
			new Entity\StringField('MOBILE',array(
				'title' => 'Номер мобильного'
			)),
			new Entity\StringField('NAME',array(
				'title' => 'Краткое имя (прозвище)'
			)),
			new Entity\StringField('FIO_F',array(
				'title' => 'Фамилия'
			)),
			new Entity\StringField('FIO_I',array(
				'title' => 'Имя (полное)'
			)),
			new Entity\StringField('FIO_O',array(
				'title' => 'Отчество'
			))
		);
	}

	public static function getArrayDefaultValues ()
	{
		return array(
			0 => array(
				"ID" => 1,
				"LOGIN" => "admin",
				"PASSWORD" => "123456",
				"EMAIL" => "admin@example.com",
				"NAME" => "Admin"
			),
			1 => array(
				"ID" => 2,
				"LOGIN" => "guest",
				"PASSWORD" => "guest",
				"EMAIL" => "mail@example.com",
				"NAME" => "Гость"
			)
		);
	}
}