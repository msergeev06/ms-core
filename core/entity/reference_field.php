<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class ReferenceField extends Field
{
	protected $refEntity;

	protected $refEntityName;

	protected $reference;

	protected $join_type = 'LEFT';


	/**
	 * @param string       $name
	 * @param string|Base  $refEntity
	 * @param array        $reference
	 * @param array        $parameters
	 *
	 * @throws ArgumentException
	 */
	public function __construct($name, $refEntity, $reference, $parameters = array())
	{
		parent::__construct($name);

		/*
		if ($refEntity instanceof Base)
		{
			$this->refEntity = $refEntity;
			$this->refEntityName = $refEntity->getFullName();
		}
		else
		{
			// this one could be without leading backslash and/or with Table-postfix
			$this->refEntityName = Base::normalizeName($refEntity);
		}

		if (empty($reference))
		{
			throw new ArgumentException('Reference for `'.$name.'` shouldn\'t be empty');
		}
		*/
		$this->reference = $reference;

		if (isset($parameters['join_type']))
		{
			$join_type = strtoupper($parameters['join_type']);

			if (in_array($join_type, array('LEFT', 'INNER', 'RIGHT'), true))
			{
				$this->join_type = $join_type;
			}
		}
	}
	/*
	public function validateValue($value, $row, Result $result)
	{
		throw new SystemException('Reference field doesn\'t support value set up and validation.');
	}
	*/

	/**
	 * @return string
	 */
	public function getRefEntityName()
	{
		return $this->refEntityName;
	}

	public function getDataType()
	{
		return $this->refEntityName;
	}

	/**
	 * @return \Bitrix\Main\Entity\Base
	 */
	/*
	public function getRefEntity()
	{
		if ($this->refEntity === null)
		{
			$this->refEntity = Base::getInstance($this->refEntityName);
		}

		return $this->refEntity;
	}
	*/

	public function getReference()
	{
		return $this->reference;
	}

	public function getJoinType()
	{
		return $this->join_type;
	}
}


