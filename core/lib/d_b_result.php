<?php
/**
 * MSergeev\Core\Lib\DBResult
 * Осуществляет обработку результата запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;

class DBResult
{
	/**
	 * @var resource Результат mysql запроса
	 */
	protected $result;

	/**
	 * @var number|null Номер ошибки mysql запроса
	 */
	protected $result_error_number=null;

	/**
	 * @var string|null Текст ошибки mysql запроса
	 */
	protected $result_error_text=null;

	/**
	 * @var array Массив с описанием полей таблицы
	 */
	protected $table_map;

	/**
	 * @var array Массив последнего разобранного результата mysql запроса
	 */
	protected $last_res;

	/**
	 * @var array Массив последнего разобранного и обработанного mysql запроса
	 */
	protected $last_result;

	/**
	 * @var string Тип query запроса ("select", "insert", "delete", "create")
	 */
	protected $query_type;

	/**
	 * @var null|number Количество затронутых строк, при "select" запросе
	 */
	protected $mysql_affected_rows=null;

	/**
	 * @var null|int ID добавленной записи при "insert" запросе
	 */
	protected $mysql_insert_id=null;

	/**
	 * @var int|null Количество полей в результате mysql запроса
	 */
	protected $mysql_num_fields=null;

	/**
	 * @var int|null Количество строк в результате mysql запроса
	 */
	protected $mysql_num_rows=null;

	/**
	 * @var array Массив сущностей полей таблицы
	 */
	protected $arFieldsEntity;

	/**
	 * @var string Текст SQL запроса
	 */
	protected $sql;

	/**
	 * Конструктор. Вызывается при получении результата mysql запроса
	 *
	 * @api
	 *
	 * @param resource $res Результат mysql запроса
	 * @param Query $obQuery Объект Query, содержащий mysql запрос
	 */
	function __construct($res, Query $obQuery)
	{
		$this->result = $res;
		$this->table_map = $obQuery->getTableMap();
		$this->query_type = $obQuery->getType();
		$this->arFieldsEntity = $obQuery->getFieldsEntity();
		$this->sql = $obQuery->getQueryBuildParts();
		if ($res)
		{
			if ($this->query_type == "select"){
				$this->mysql_num_fields = mysql_num_fields($res);
				$this->mysql_num_rows = mysql_num_rows($res);
			}
		}
	}

	/**
	 * Возвращает время исполнения SQL запроса
	 *
	 * @return float
	 */
	public function getQueryTime ()
	{
		$DB = $GLOBALS['DB'];
		return floatval($DB->getLastQueryTime());
	}

	/**
	 * Возвращает время исполнения всех SQL запросов
	 *
	 * @return float
	 */
	public function getAllQueryTime ()
	{
		$DB = $GLOBALS['DB'];
		return floatval($DB->getAllQueryTime());
	}

	/**
	 * Возвращает количество SQL запросов
	 *
	 * @return int
	 */
	public function getQueryCount ()
	{
		$DB = $GLOBALS['DB'];
		return intval($DB->getCountQuery());
	}

	/**
	 * Возвращает текст SQL запроса
	 *
	 * @api
	 *
	 * @return string Текст SQL запроса
	 */
	public function getSql ()
	{
		return $this->sql;
	}

	/**
	 * Разбирает результат mysql запроса и возвращает массив обработанных значений
	 *
	 * @api
	 *
	 * @return array Массив обработанных значений
	 */
	public function fetch ()
	{
		if ($this->query_type == "select")
		{
			$ar_res = mysql_fetch_array($this->result);
			$this->last_res = $ar_res;
			$arResult = array();
			if (is_array($ar_res))
			{
				foreach ($ar_res as $k => $v)
				{
					if (is_string($k))
					{
						if (isset($this->arFieldsEntity[$k]))
						{
							$fieldClassName = $this->arFieldsEntity[$k]->getClassName();
							//$v = $this->arFieldsEntity[$k]->fetchDataModification($v);
							$v = $fieldClassName::fetchDataModification($v,$this->arFieldsEntity[$k]);
						}
					}
					$arResult[$k] = $v;
				}
			}
			else
			{
				$arResult = $ar_res;
			}
		}
		else
		{
			$arResult = $this->result;
		}
		$this->last_result = $arResult;

		return $arResult;
	}

	/**
	 * Возвращает количество строк в результате
	 *
	 * @api
	 *
	 * @return int Количество строк в результате
	 */
	public function getNumRows ()
	{
		return $this->mysql_num_rows;
	}

	/**
	 * Устанавливает количество полей в результате
	 *
	 * @api
	 *
	 * @param number $data Количество полей в результате
	 */
	public function setNumFields ($data)
	{
		$this->mysql_num_fields = $data;
	}

	/**
	 * Возвращает количество полей в результате
	 *
	 * @api
	 *
	 * @return int Количество полей в результате
	 */
	public function getNumFields ()
	{
		return $this->mysql_num_fields;
	}

	/**
	 * Возвращает массив последнего разобранного результата mysql запроса
	 *
	 * @api
	 *
	 * @return array Массив последнего разобранного результата mysql запроса
	 */
	public function getLastRes ()
	{
		return $this->last_res;
	}

	/**
	 * Возвращает массив последнего разобранного и обработанного mysql запроса
	 *
	 * @api
	 *
	 * @return array Массив последнего разобранного и обработанного mysql запроса
	 */
	public function getLastResult ()
	{
		return $this->last_result;
	}

	/**
	 * Возвращает результат mysql запроса
	 *
	 * @api
	 *
	 * @return resource Результат mysql запроса
	 */
	public function getResult ()
	{
		return $this->result;
	}

	/**
	 * Устанавливает номер ошибки mysql запроса
	 *
	 * @api
	 *
	 * @param number $number Номер ошибки mysql запроса
	 */
	public function setResultErrorNumber($number)
	{
		$this->result_error_number = $number;
	}

	/**
	 * Возвращает номер ошибки mysql запроса
	 *
	 * @api
	 *
	 * @return number номер ошибки mysql запроса
	 */
	public function getResultErrorNumber()
	{
		return $this->result_error_number;
	}

	/**
	 * Устанавливает текст ошибки mysql запроса
	 *
	 * @api
	 *
	 * @param string $text текст ошибки mysql запроса
	 */
	public function setResultErrorText ($text)
	{
		$this->result_error_text = $text;
	}

	/**
	 * Возвращает текст ошибки mysql запроса
	 *
	 * @api
	 *
	 * @return string текст ошибки mysql запроса
	 */
	public function getResultErrorText()
	{
		return $this->result_error_text;
	}

	/**
	 * Устанавливает количество затронутых строк, при "select" запросе
	 *
	 * @api
	 *
	 * @param number $data Количество затронутых строк, при "select" запросе
	 */
	public function setAffectedRows ($data)
	{
		$this->mysql_affected_rows = $data;
	}

	/**
	 * Возвращает количество затронутых строк, при "select" запросе
	 *
	 * @api
	 *
	 * @return number Количество затронутых строк, при "select" запросе
	 */
	public function getAffectedRows ()
	{
		return $this->mysql_affected_rows;
	}

	/**
	 * Устанавливает ID добавленной записи при "insert" запросе
	 *
	 * @api
	 *
	 * @param int $data ID добавленной записи при "insert" запросе
	 */
	public function setInsertId ($data)
	{
		$this->mysql_insert_id = $data;
	}

	/**
	 * Возвращает ID добавленной записи при "insert" запросе
	 *
	 * @api
	 *
	 * @return int ID добавленной записи при "insert" запросе
	 */
	public function getInsertId ()
	{
		return $this->mysql_insert_id;
	}
}