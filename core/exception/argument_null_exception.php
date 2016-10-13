<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Exception;

/**
 * Exception is thrown when "empty" value is passed to a function that does not accept it as a valid argument.
 */
class ArgumentNullException extends ArgumentException
{
	public function __construct($parameter, \Exception $previous = null)
	{
		$message = sprintf("Argument '%s' is null or empty", $parameter);
		parent::__construct($message, $parameter, $previous);
	}

	public function showException()
	{
		$html = '<pre><b><i>ArgumentNullException:</i></b> "'.$this->getMessage().'"'."\n";
		$html .= "<b>Stack trace:</b>\n".$this->getTraceAsString()."\n";
		$html .= "<b>".$this->getFile()." ".$this->getLine()."</b>";
		$html .= "</pre>";

		return $html;
	}

}