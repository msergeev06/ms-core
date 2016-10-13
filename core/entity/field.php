<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

abstract class Field {
	/** @var string */
	protected $name;

	/** @var string */
	protected $dataType;

	/** @var string */
	protected $fieldType;

	/** @var array */
	protected $initialParameters;

	/** @var string */
	protected $title=null;

    /** @var bool */
    protected $isSerialized = false;

    /** @var Field */
    protected $parentField;

    /** @var null|callback */
    protected $fetchDataModification = null;

    /** @var null|callback */
    protected $saveDataModification = null;


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

	/*
	 * @deprecated
	 * @var array
	 *
    /*
	protected static $oldDataTypes = array(
		'float' => 'Bitrix\Main\Entity\FloatField',
		'string' => 'Bitrix\Main\Entity\StringField',
		'text' => 'Bitrix\Main\Entity\TextField',
		'datetime' => 'Bitrix\Main\Entity\DatetimeField',
		'date' => 'Bitrix\Main\Entity\DateField',
		'integer' => 'Bitrix\Main\Entity\IntegerField',
		'enum' => 'Bitrix\Main\Entity\EnumField',
		'boolean' => 'Bitrix\Main\Entity\BooleanField'
	);
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

	public function getName()
	{
		return $this->name;
	}

	public function getTitle() {

        return $this->title;
	}

	public function getDataType() {
		return $this->dataType;
	}

	public function getFieldType() {
		return $this->fieldType;
	}

	public function getParentField()
	{
		return $this->parentField;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function serialize($value)
	{
		if (!is_string($value))
		{
			$value = serialize($value);
		}

		return $value;
	}

	public function unserialize($value)
	{
		return unserialize($value);
	}

    public function isSerialized ()
    {
        return $this->isSerialized;
    }

    public function getFetchDataModification ()
    {
        return $this->fetchDataModification;
    }

    public function getSaveDataModification ()
    {
        return $this->saveDataModification;
    }
}