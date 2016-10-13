<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

/**
 * This exception is thrown when an I/O error occurs.
 */
class IoException extends SystemException
{
	protected $path;

	/**
	 * Creates new exception object.
	 *
	 * @param string $message Exception message
	 * @param string $path Path that generated exception.
	 * @param \Exception $previous
	 */
	public function __construct($message = "", $path = "", \Exception $previous = null)
	{
		parent::__construct($message, 120, '', 0, $previous);
		$this->path = $path;
	}

	/**
	 * Path that generated exception.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
}
