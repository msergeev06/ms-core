<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;

class DBResult
{
	protected $result;
	protected $result_error_number=null;
	protected $result_error_text=null;
	protected $table_map;
	protected $last_res;
	protected $last_result;
	protected $query_type;
	protected $mysql_affected_rows=null;
	protected $mysql_insert_id=null;
	protected $mysql_num_fields=null;
	protected $mysql_num_rows=null;
	protected $arFieldsEntity;
	protected $sql;

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

	public function getSql ()
	{
		return $this->sql;
	}

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
							$v = $this->arFieldsEntity[$k]->fetchDataModification($v);
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

	public function getNumRows ()
	{
		return $this->mysql_num_rows;
	}

	public function getNumFields ()
	{
		return $this->mysql_num_fields;
	}

	public function getLastRes ()
	{
		return $this->last_res;
	}

	public function getLastResult ()
	{
		return $this->last_result;
	}

	public function getResult ()
	{
		return $this->result;
	}

	public function setResultErrorNumber($number)
	{
		$this->result_error_number = $number;
	}

	public function getResultErrorNumber()
	{
		return $this->result_error_number;
	}

	public function setResultErrorText ($text)
	{
		return $this->result_error_text = $text;
	}

	public function getResultErrorText()
	{
		return $this->result_error_text;
	}

	public function setAffectedRows ($data)
	{
		$this->mysql_affected_rows = $data;
	}

	public function getAffectedRows ()
	{
		return $this->mysql_affected_rows;
	}

	public function setInsertId ($data)
	{
		$this->mysql_insert_id = $data;
	}

	public function getInsertId ()
	{
		return $this->mysql_insert_id;
	}

	public function setNumFields ($data)
	{
		$this->mysql_num_fields = $data;
	}

}