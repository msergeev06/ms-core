<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class BooleanField extends ScalarField {
	/**
	 * Value (false, true) equivalent map
	 * @var array
	 */
	protected $values;

	protected $size=1;

	function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = 'varchar';
		$this->fieldType = 'boolean';

		if (empty($parameters['values']))
		{
			$this->values = array(false, true);
		}
		else
		{
			$this->values = $parameters['values'];
		}
	}


	/**
	 * Convert true/false values to actual field values
	 * @param boolean|integer|string $value
	 * @return mixed
	 */
	public function normalizeValue($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
		{
			$value = 1;
		}
		elseif (is_string($value) && ($value == 'false' || $value== 'N'))
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			$value = $this->values[$value];
		}

		return $value;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function getSize() {
		return $this->size;
	}

	public function getDefaultValueDB() {
		$value = $this->getDefaultValue();
		if (!is_null($value))
		{
			if ($value === true) {
				return 'Y';
			}
			else {
				return 'N';
			}
		}
		else
		{
			return null;
		}
	}

    /*
	public function isValueEmpty($value)
	{
		return (strval($value) === '' && $value !== false);
	}
    */

    /*
    public function getArray() {
        $arData = parent::getArray();

        $arData['size'] = self::getSize();
        $arData['values'] = self::getValues();

        return $arData;
    }
    */
    public function saveDataModification ($value)
    {
        $value = self::normalizeValue($value);
        if ($value)
        {
            $value = 'Y';
        }
        else
        {
            $value = 'N';
        }
        $value = parent::saveDataModification($value);

        return $value;
    }

    public function fetchDataModification ($value)
    {
        $value = self::normalizeValue($value);
        $value = parent::fetchDataModification($value);

        return $value;
    }
}