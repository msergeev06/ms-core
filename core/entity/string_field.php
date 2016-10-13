<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class StringField extends ScalarField
{
	/*
	 * Shortcut for Regexp validator
	 * @var null|string
	 *
	protected $format = null;
	*/

	/** @var int|null  */
	protected $size = 255;

	function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = 'varchar';
		$this->fieldType = 'string';

        /*
		if (!empty($parameters['format']))
		{
			$this->format = $parameters['format'];
		}
        */
		if(isset($parameters['size']) && intval($parameters['size']) > 0)
		{
			$this->size = intval($parameters['size']);
		}
	}

	/*
	 * Shortcut for Regexp validator
	 * @return null|string
	 *
	public function getFormat()
	{
		return $this->format;
	}
    */
	/*
	public function getValidators()
	{
		$validators = parent::getValidators();

		if ($this->format !== null)
		{
			$validators[] = new Validator\RegExp($this->format);
		}

		return $validators;
	}
	*/

	/**
	 * Returns the size of the field in a database (in characters).
	 * @return int|null
	 */
	public function getSize()
	{
		return $this->size;
	}
}