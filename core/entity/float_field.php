<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class FloatField extends ScalarField
{
	/** @var int|null */
	protected $scale=2;

	public function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'float';

		if(isset($parameters['scale']))
		{
			$this->scale = intval($parameters['scale']);
		}
	}

	/**
	 * @return int|null
	 */
	public function getScale()
	{
		return $this->scale;
	}
}