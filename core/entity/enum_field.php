<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class EnumField extends ScalarField {
	protected $values;

	function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'enum';

		/*
		if (empty($parameters['values']))
		{
			throw new SystemException(sprintf(
				                          'Required parameter "values" for %s field in %s entity not found',
				                          $this->name, $this->entity->getNamespace().$this->entity->getName()
			                          ));
		}

		if (!is_array($parameters['values']))
		{
			throw new SystemException(sprintf(
				                          'Parameter "values" for %s field in %s entity should be an array',
				                          $this->name, $this->entity->getNamespace().$this->entity->getName()
			                          ));
		}
		*/


		$this->values = $parameters['values'];
	}
	/*
	public function getValidators()
	{

		$validators = parent::getValidators();

		if ($this->validation === null)
		{
			$validators[] = new Validator\Enum;
		}

		return $validators;

	}
	*/

	public function getValues()
	{
		return $this->values;
	}

}