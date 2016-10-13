<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class ScalarField extends Field
{

	protected $is_primary = false;

	protected $is_unique = false;

	protected $is_required = false;

	protected $is_autocomplete = false;

	protected $column_name = '';

	protected $arRun = null;

	protected $values = null;

	/** @var null|callable|mixed  */
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

	public function isPrimary()
	{
		return $this->is_primary;
	}

	public function isRequired()
	{
		return $this->is_required;
	}

	public function isUnique()
	{
		return $this->is_unique;
	}

	public function isAutocomplete()
	{
		return $this->is_autocomplete;
	}

	public function getColumnName()
	{
		return $this->column_name;
	}

	public function getRun ()
	{
		return $this->arRun;
	}


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
	public function saveDataModification ($value)
	{
		$additionalSaveDataModification = parent::getSaveDataModification();
		if (!is_null($additionalSaveDataModification) && is_callable($additionalSaveDataModification))
		{
			$value = call_user_func($additionalSaveDataModification,$value);
		}
		if (static::isSerialized())
		{
			$value = static::serialize($value);
		}

		return $value;
	}

	public function fetchDataModification ($value)
	{
		$additionalFetchDataModification = parent::getFetchDataModification();
		if (!is_null($additionalFetchDataModification) && is_callable($additionalFetchDataModification))
		{
			$value = call_user_func($additionalFetchDataModification,$value);
		}
		if (static::isSerialized())
		{
			$value = static::unserialize($value);
		}

		return $value;
	}

	public function validate ($value)
	{
		return $value;
	}
}