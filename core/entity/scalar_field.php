<?php
/**
 * MSergeev\Core\Entity\ScalarField
 * Сущность поля базы данных, содержащее скалярные данные
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

/**
 * Class ScalarField
 * @package MSergeev\Core\Entity
 * @extends Field
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
 * @method Field    getFetchDataModification()  Возвращает название функции для обработки значений полученных из базы
 *                                              данных
 * @method Field    getSaveDataModification()   Возвращает название функции для обработки значений перед сохранением в
 *                                              базу данных
 */
class ScalarField extends Field
{
	/**
	 * @var bool Поле является PRIMARY
	 */
	protected $is_primary = false;

	/**
	 * @var bool Значение в поле должно быть уникальным
	 */
	protected $is_unique = false;

	/**
	 * @var bool Для поля обязательно передавать значение
	 */
	protected $is_required = false;

	/**
	 * @var bool Для поля используется auto increment
	 */
	protected $is_autocomplete = false;

	/**
	 * @var string Название поля в базе данных
	 */
	protected $column_name = '';

	/**
	 * @var array Массив исполняемых функций
	 */
	protected $arRun = null;

	/**
	 * @var array Варианты значений поля
	 */
	protected $values = null;

	/**
	 * @var null|callable|mixed Значение поля по-умолчанию
	 */
	protected $default_value = null;

	public function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->is_primary = (isset($parameters['primary']) && $parameters['primary']);
		$this->is_unique = (isset($parameters['unique']) && $parameters['unique']);
		$this->is_required = (isset($parameters['required']) && $parameters['required']);
		$this->is_autocomplete = (isset($parameters['autocomplete']) && $parameters['autocomplete']);

		$this->values = isset($parameters['values']) ? $parameters['values'] : null;
		$this->column_name = isset($parameters['column_name']) ? $parameters['column_name'] : $this->name;
		$this->default_value = isset($parameters['default_value']) ? $parameters['default_value'] : null;
		if (isset($parameters["run"]))
		{
			$this->arRun = $parameters["run"];
		}
	}

	/**
	 * Возвращает флаг того, является ли поле PRIMARY
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isPrimary()
	{
		return $this->is_primary;
	}

	/**
	 * Возвращает флаг того, является ли поле обязательным
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->is_required;
	}

	/**
	 * Возвращает флаг того, являются ли значения поля уникальными
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isUnique()
	{
		return $this->is_unique;
	}

	/**
	 * Возвращает флаг того, используется ли для поля auto increment
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isAutocomplete()
	{
		return $this->is_autocomplete;
	}

	/**
	 * Возвращает название поля в базе данных
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getColumnName()
	{
		return $this->column_name;
	}

	/**
	 * Возвращает массив исполняемых функций
	 *
	 * @ignore
	 *
	 * @return array
	 */
	public function getRun ()
	{
		return $this->arRun;
	}

	/**
	 * Задает название поля в базе данных
	 *
	 * @api
	 *
	 * @param $column_name
	 */
	public function setColumnName($column_name)
	{
		$this->column_name = $column_name;
	}

	/*
	abstract function isValueEmpty($value);
	{
		/*
		if ($value instanceof SqlExpression)
		{
			$value = $value->compile();
		}
		*

		return (strval($value) === '');
	}
	*/

	/**
	 * Возвращает значение поля по-умолчанию
	 *
	 * @api
	 *
	 * @return callable|mixed|null
	 */
	public function getDefaultValue()
	{
		if (is_callable($this->default_value))
		{
			return call_user_func($this->default_value);
		}
		else
		{
			return $this->default_value;
		}
	}

	/*
	public function getArray()
	{
		$arData = array();
		$arData['columnName'] = self::getColumnName();
		$arData['required'] = self::isRequired();
		$arData['primary'] = self::isPrimary();
		$arData['autocomplete'] = self::isAutocomplete();
		$arData['unique'] = self::isUnique();
		$arData['run'] = self::getRun();
		$arData['default_value'] = self::getDefaultValue();

		return $arData;
	}
	*/

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @param $value
	 * @param object|null $obj
	 *
	 * @return mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$additionalSaveDataModification = $obj->getSaveDataModification();
			if (!is_null($additionalSaveDataModification) && is_callable($additionalSaveDataModification))
			{
				$value = call_user_func($additionalSaveDataModification,$value);
			}
			if ($obj->isSerialized())
			{
				$value = $obj->serialize($value);
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения значения из базы данных
	 *
	 * @param $value
	 * @param object|null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$additionalFetchDataModification = $obj->getFetchDataModification();
			if (!is_null($additionalFetchDataModification) && is_callable($additionalFetchDataModification))
			{
				$value = call_user_func($additionalFetchDataModification,$value);
			}
			if ($obj->isSerialized())
			{
				$value = $obj->unserialize($value);
			}
		}

		return $value;
	}

	/**
	 * Осуществляет валидацию данных
	 *
	 * @api
	 *
	 * @param mixed $value Данные для валидации
	 * @param object|null $obj
	 *
	 * @return mixed
	 */
	public static function validate ($value, $obj=null)
	{
		return $value;
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