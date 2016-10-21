<?php
/**
 * MSergeev\Core\Entity\TimeField
 * Сущность поля базы данных, содержащего время
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
 * Class TimeField
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
class TimeField extends ScalarField
{
	/**
	 * Конструктор
	 *
	 * @param string $name
	 * @param array  $parameters
	 */
	public function __construct ($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'time';
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @param      $value
	 * @param null $obj
	 *
	 * @return bool|mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		$value = self::validate($value, $obj);

		$value = parent::saveDataModification($value);
		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param      $value
	 * @param null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		$value = parent::fetchDataModification ($value, $obj);
		return $value;
	}

	/**
	 * Валидирует значение поля
	 *
	 * @param null $value
	 * @param null $obj
	 *
	 * @return bool|mixed
	 */
	public static function validate ($value=null, $obj=null)
	{
		try
		{
			if(is_null($value))
			{
				throw new ArgumentNullException('date');
			}


			if (strpos($value,':') !== false)
			{
				$arTime = explode(':',$value);
				for ($i=0; $i<3; $i++)
				{
					$arTime[$i] = intval($arTime[$i]);
				}

				if (
					($arTime[0]>=0 && $arTime[0]<=23)
					&& ($arTime[1]>=0 && $arTime[1]<=59)
					&& ($arTime[2]>=0 && $arTime[2]<=59)
				)
				{
					$value = '';
					$bFirst = true;
					for ($i=0; $i<3; $i++)
					{
						if ($bFirst)
						{
							$bFirst = false;
						}
						else
						{
							$value .= ":";
						}

						if (intval($arTime[$i])>=0 && intval($arTime[$i])<=9)
						{
							$value .= '0'.intval($arTime[$i]);
						}
						else
						{
							$value .= intval($arTime[$i]);
						}
					}

					return $value;
				}
				else
				{
					throw new ArgumentOutOfRangeException('time','00:00:00', '23:59:59');
				}

			}
			else
			{
				throw new ArgumentOutOfRangeException('time');
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

		//До сюда не дойдет
		return false;
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

}