<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class SqlHelper
{
	const QUOTES = '`';

	public function __construct ()
	{

	}

	public function helperDate ()
	{
		return new SqlHelperDate();
	}

	public function helperMath ()
	{
		return new SqlHelperMath();
	}

	public function helperStr()
	{
		return new SqlHelperStr();
	}

	public function wrapQuotes ($str)
	{
		return self::QUOTES.$str.self::QUOTES;
	}
	public function getQuote ()
	{
		return self::QUOTES;
	}

	public function getCountFunction ($params="*", $newColumn=null)
	{
		if (is_null($newColumn))
		{
			if ($params=="*")
			{
				$newColumn = 'COUNT';
			}
			else
			{
				$newColumn = 'COUNT_'.$params;
			}
		}

		return 'COUNT('.$params.') '.$this->wrapQuotes($newColumn);
	}

	public function getMaxFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'MAX_'.$column;
		}

		return 'MAX('.$this->wrapQuotes($column).') '.$this->wrapQuotes($newColumn);
	}

	public function getMinFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'MIN_'.$column;
		}

		return 'MIN('.$this->wrapQuotes($column).') '.$this->wrapQuotes($newColumn);
	}

	public function getSumFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'SUM_'.$column;
		}

		return 'SUM('.$this->wrapQuotes($column).') '.$this->wrapQuotes($newColumn);
	}

}