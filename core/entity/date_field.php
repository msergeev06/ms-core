<?php
/**
 * MSergeev\Core\Entity\DateField
 * Сущность поля базы данных, содержащего дату
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

use MSergeev\Core\Exception\ArgumentNullException;
use MSergeev\Core\Exception\ArgumentOutOfRangeException;

/**
 * Class DateField
 * @package MSergeev\Core\Entity
 * @extends ScalarField
 *
 * @var bool                $is_primary             Поле является PRIMARY
 * @var bool                $is_unique              Значение в поле должно быть уникальным
 * @var bool                $is_required            Для поля обязательно передавать значение
 * @var bool                $is_autocomplete        Для поля используется auto increment
 * @var string              $column_name            Название поля в базе данных
 * @var array               $arRun                  Массив исполняемых функций
 * @var null|callable|mixed $default_value          Значение поля по-умолчанию
 *
 * @var string              $name                   Название поля в API
 * @var string              $dataType               Тип поля в базе данных
 * @var string              $fieldType              Тип поля в API
 * @var array               $initialParameters      Параметры инициализации
 * @var string              $title                  Описание поля
 * @var bool                $isSerialized           Является ли значение поля сериализованным массивом
 * @var Field               $parentField            Родительское поле
 * @var null|callback       $fetchDataModification  Функция обработки полученных значений из базы
 * @var null|callback       $saveDataModification   Функция обработки перед записью значений в базу
 * @var null|string         $link                   Связь поля таблицы
 *
 * @method ScalarField isPrimary()                  Возвращает флаг того, является ли поле PRIMARY
 * @method ScalarField isRequired()                 Возвращает флаг того, является ли поле обязательным
 * @method ScalarField isUnique()                   Возвращает флаг того, являются ли значения поля уникальными
 * @method ScalarField isAutocomplete()             Возвращает флаг того, используется ли для поля auto increment
 * @method ScalarField getColumnName()              Возвращает название поля в базе данных
 * @method ScalarField getRun()                     Возвращает массив исполняемых функций
 * @method ScalarField setColumnName($column_name)  Задает название поля в базе данных
 * @method ScalarField getDefaultValue()            Возвращает значение поля по-умолчанию
 *
 * @method Field    getName()                   Возвращает название поля в API
 * @method Field    getTitle()                  Возвращает описание поля
 * @method Field    getDataType()               Возвращает тип поля в базы данных
 * @method Field    getFieldType()              Возвращает тип поля в API
 * @method Field    getParentField()            Возвращает объект родительского поля
 * @method Field    getLink()                   Возвращает строку - связь поля с другим полем
 * @method Field    serialize($value)           Сериализует массив
 * @method Field    unserialize($value)         Десериализирует массив
 * @method Field    isSerialized()              Возвращает флаг, обозначающий факт того, является ли значение данного
 *                                              поля сериализованным массивом
 * @method Field    getFetchDataModification()  Возвращает название функции для обработки значений полученных из
 *                                              базы данных
 * @method Field    getSaveDataModification()   Возвращает название функции для обработки значений перед сохранением
 *                                              в базу данных
 */
class DateField extends ScalarField
{
	/**
	 * Конструктор
	 *
	 * @param string $name
	 * @param array  $parameters
	 */
	public function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'date';
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @param $value
	 * @param object|null $obj
	 *
	 * @return bool|mixed|string|void
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		$value = self::validate($value, $obj);
		list($arDate['day'],$arDate['month'],$arDate['year']) = explode(".",$value);
		$time = mktime(0,0,0,intval($arDate['month']),intval($arDate['day']),intval($arDate['year']));
		$value = date('Y-m-d',$time);

		$value = parent::saveDataModification($value, $obj);
		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param $value
	 * @param object|null $obj
	 *
	 * @return array|bool|mixed|string
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		list($arDate['year'],$arDate['month'],$arDate['day']) = explode("-",$value);
		$time = mktime(0,0,0,intval($arDate['month']),intval($arDate['day']),intval($arDate['year']));
		$value = date('d.m.Y',$time);

		$value = parent::fetchDataModification ($value, $obj);
		return $value;
	}

	/**
	 * Валидирует значение поля
	 *
	 * @api
	 *
	 * @param null|mixed $value
	 * @param object|null $obj
	 *
	 * @return mixed|void
	 */
	public static function validate ($value=null, $obj=null)
	{
		try
		{
			if(is_null($value))
			{
				throw new ArgumentNullException('date');
			}

			if (strlen($value)<7 || strlen($value)>10)
			{
				throw new ArgumentOutOfRangeException('date');
			}


			$arData = array();
			if (strpos($value,'.') !== false)
			{
				$arData = explode('.',$value);
			}
			elseif (strpos($value,'-') !== false)
			{
				$arData = explode('-',$value);
			}
			else
			{
				throw new ArgumentOutOfRangeException('data');
			}

			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				$value = '';
				if (intval($arData[0]) >= 1 && intval($arData[0])<=9)
				{
					$value.='0'.intval($arData[0]).'.';
				}
				else
				{
					$value.= intval($arData[0]).'.';
				}
				if (intval($arData[1]) >= 1 && intval($arData[1])<=9)
				{
					$value.='0'.intval($arData[1]).'.';
				}
				else
				{
					$value.= intval($arData[1]).'.';
				}
				$value.=intval($arData[2]);

				return $value;
			}
			else
			{
				throw new ArgumentOutOfRangeException('data','01.01.1970','31.12.9999');
			}
		}
		catch (ArgumentNullException $e)
		{
			$e->showException();
		}
		catch (ArgumentOutOfRangeException $e2)
		{
			$e2->showException();
		}
	}

	/**
	 * Возвращает имя класса объекта
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getClassName ()
	{
		return __CLASS__;
	}

	/*
	public function getValidators()
	{
		$validators = parent::getValidators();

		if ($this->validation === null)
		{
			$validators[] = new Validator\Date;
		}

		return $validators;
	}

	public function assureValueObject($value)
	{
		if ($value instanceof Type\DateTime)
		{
			// oracle sql helper returns datetime instead of date - it doesn't see the difference
			$value = new Type\Date(
				$value->format(Main\UserFieldTable::MULTIPLE_DATE_FORMAT),
				Main\UserFieldTable::MULTIPLE_DATE_FORMAT
			);
		}

		return $value;
	}
	*/

}