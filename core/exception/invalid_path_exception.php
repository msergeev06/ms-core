<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

class InvalidPathException extends IoException
{
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Path '%s' is invalid.", $path);
		parent::__construct($message, $path, $previous);
	}
}
