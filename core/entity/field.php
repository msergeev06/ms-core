<?php
/**
 * MSergeev\Core\Entity\Field
 * Сущность поля базы данных
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

/**
 * Class Field
 * @package MSergeev\Core\Entity

 * @var string                      $name                   Название поля в API
 * @var string                      $dataType               Тип поля в базе данных
 * @var string                      $fieldType              Тип поля в API
 * @var array                       $initialParameters      Параметры инициализации
 * @var string                      $title                  Описание поля
 * @var bool                        $isSerialized           Является ли значение поля сериализованным массивом
 * @var Field  $parentField            Родительское поле
 * @var null|callback               $fetchDataModification  Функция обработки полученных значений из базы
 * @var null|callback               $saveDataModification   Функция обработки перед записью значений в базу
 * @var null|string                 $link                   Связь поля таблицы
 */
abstract class Field {
	/**
	 * @var string Название поля в API
	 */
	protected $name;

	/**
	 * @var string Тип поля в базе данных
	 */
	protected $dataType;

	/**
	 * @var string Тип поля в API
	 */
	protected $fieldType;

	/**
	 * @var array Параметры инициализации
	 */
	protected $initialParameters;

	/**
	 * @var string Описание поля
	 */
	protected $title=null;

	/**
	 * @var bool Является ли значение поля сериализованным массивом
	 */
	protected $isSerialized = false;

	/**
	 * @var Field Родительское поле
	 */
	protected $parentField;

	/**
	 * @var null|callback Функция обработки полученных значений из базы
	 */
	protected $fetchDataModification = null;

	/**
	 * @var null|callback Функция обработки перед записью значений в базу
	 */
	protected $saveDataModification = null;

	/**
	 * @var null|string Связь поля таблицы
	 */
	protected $link=null;

/*
    /* @var null|callback *
	//protected $validation = null;

	/* @var null|callback[] *
	//protected $validators = null;

	/* @var array|callback[] *
	//protected $additionalValidators = array();

	/* @var null|callback *
	//protected $fetchDataModification = null;

	/* @var null|callback[] *
	//protected $additionalFetchDataModifiers = array();

	/* @var null|callback[] *
	//protected $saveDataModifiers;

	/* @var null|callback[] *
	//protected $additionalSaveDataModifiers = array();


	/* @var Base *

	//protected $entity;

*/

	/**
	 * Конструктор. Обрабатывает начальные параметры поля
	 *
	 * @param string $name       Название поля в API
	 * @param array  $parameters Параметры поля
	 */
	public function __construct($name, $parameters = array())
	{
		if (!strlen($name))
		{
			//throw new SystemException('Field name required');
		}

		$this->name = $name;
		$this->initialParameters = $parameters;

		if (isset($parameters['title']))
		{
			$this->title = $parameters['title'];
		}

		if (isset($parameters['link']))
		{
			$this->link = $parameters['link'];
		}

		// fetch data modifiers
		if (isset($parameters['fetch_data_modification']))
		{
			$this->fetchDataModification = $parameters['fetch_data_modification'];
		}

		// save data modifiers
		if (isset($parameters['save_data_modification']))
		{
			$this->saveDataModification = $parameters['save_data_modification'];
		}

		if (isset($parameters['serialized']) && $parameters['serialized'])
		{
			$this->isSerialized = $parameters['serialized'];
		}

		if (isset($parameters['parent']))
		{
			$this->parentField = $parameters['parent'];
		}
	}

	/**
	 * Возвращает название поля в API
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Возвращает описание поля
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает тип поля в базы данных
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getDataType()
	{
		return $this->dataType;
	}

	/**
	 * Возвращает тип поля в API
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getFieldType()
	{
		return $this->fieldType;
	}

	/**
	 * Возвращает объект родительского поля
	 *
	 * @api
	 *
	 * @return Field
	 */
	public function getParentField()
	{
		return $this->parentField;
	}

	/**
	 * Возвращает строку - связь поля с другим полем
	 *
	 * @api
	 *
	 * @return null|string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Сериализует массив
	 *
	 * @api
	 *
	 * @param array|string $value Массив
	 *
	 * @return string
	 */
	public function serialize($value)
	{
		if (!is_string($value))
		{
			$value = serialize($value);
		}

		return $value;
	}

	/**
	 * Десериализирует массив
	 *
	 * @api
	 *
	 * @param string $value Сериализованный массиы
	 *
	 * @return array
	 */
	public function unserialize($value)
	{
		return unserialize($value);
	}

	/**
	 * Возвращает флаг, обозначающий факт того,
	 * является ли значение данного поля сериализованным массивом
	 *
	 * @return bool
	 */
	public function isSerialized ()
	{
		return $this->isSerialized;
	}

	/**
	 * Возвращает название функции для обработки значений полученных из базы данных
	 *
	 * @api
	 *
	 * @return callable|null
	 */
	public function getFetchDataModification ()
	{
		return $this->fetchDataModification;
	}

	/**
	 * Возвращает название функции для обработки значений перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @return callable|null
	 */
	public function getSaveDataModification ()
	{
		return $this->saveDataModification;
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