<?php

namespace MSergeev\Core\Tables;

use MSergeev\Core\Entity;
use MSergeev\Core\Lib;

class FileTable extends Lib\DataManager
{
	public static function getTableName ()
	{
		return 'ms_core_file';
	}

	public static function getTableTitle ()
	{
		return 'Загруженные файлы';
	}

	public static function getMap ()
	{
		return array(
			new Entity\IntegerField('ID',array(
				'primary' => true,
				'autocomplete' => true,
				'size' => 18,
				'title' => 'ID файла'
			)),
			new Entity\StringField('PACKAGE',array(
				'size' => 50,
				'title' => 'Имя пакета, чей файл'
			)),
			new Entity\IntegerField('HEIGHT',array(
				'size' => 18,
				'title' => 'Высота изображения'
			)),
			new Entity\IntegerField('WIDTH',array(
				'size' => 18,
				'title' => 'Ширина изображения'
			)),
			new Entity\BigIntField('FILE_SIZE',array(
				'title' => 'Размер файла в байтах'
			)),
			new Entity\StringField('CONTENT_TYPE',array(
				'default_value' => 'IMAGE',
				'title' => 'Тип файла'
			)),
			new Entity\StringField('SUBDIR',array(
				'title' => 'Поддиректория'
			)),
			new Entity\StringField('FILE_NAME',array(
				'required' => true,
				'title' => 'Имя файла'
			)),
			new Entity\StringField('ORIGINAL_NAME',array(
				'title' => 'Оригинальное имя файла'
			)),
			new Entity\StringField('DESCRIPTION',array(
				'title' => 'Описание файла'
			)),
			new Entity\StringField('HANDLER_ID',array(
				'title' => 'Обработчик'
			)),
			new Entity\StringField('EXTERNAL_ID',array(
				'title' => 'Внешний код'
			))
		);
	}
}