<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

class FileOpenException extends IoException
{
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Cannot open the file '%s'.", $path);
		parent::__construct($message, $path, $previous);
	}
}
