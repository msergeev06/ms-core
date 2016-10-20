<?php
/**
 * MSergeev\Core\Lib\SqlHelper
 * Помощник обработки SQL запросов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class SqlHelper
{
	/**
	 * Константа, содержащая кавычку, используемую в SQL запросах
	 */
	const QUOTES = '`';

	/**
	 * Конструктор. Пустой
	 */
	public function __construct ()
	{

	}

	/**
	 * Возвращает объект класса SqlHelperDate, для получения доступа к его функциям
	 *
	 * @method convertDateFromDB
	 * @method convertDateToDB
	 *
	 * @return SqlHelperDate
	 */
	public function helperDate ()
	{
		return new SqlHelperDate();
	}

	/**
	 * Возвращает объект класса SqlHelperMath, для получения доступа к его функциям
	 *
	 *
	 * @return SqlHelperMath
	 */
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