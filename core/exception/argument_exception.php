<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

/**
 * Exception is thrown when function argument is not valid.
 */
class ArgumentException extends SystemException
{
	protected $parameter;

	public function __construct($message = "", $parameter = "", \Exception $previous = null)
	{
		parent::__construct($message, 100, '', 0, $previous);
		$this->parameter = $parameter;
	}

	public function getParameter()
	{
		return $this->parameter;
	}
}