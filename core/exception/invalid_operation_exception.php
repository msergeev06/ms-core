<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

/**
 * Exception is thrown when a method call is invalid for current state of object.
 */
class InvalidOperationException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 160, '', 0, $previous);
	}
}
