<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;
use MSergeev\Core\Exception;
use \MSergeev\Core\Lib;

class Query
{
	protected
		$type=null; //Тип Query (select|create|insert|update|delete|drop)

	protected
		$select = array(),
		$group = array(),
		$order = array(),
		$limit = null,
		$offset = null,
		$count_total = null,
		$runtime = null;

	protected
		$filter = array(),
		$where = array(),
		$having = array();

	protected
		$insertArray = array(),
		$updateArray = array(),
		$updatePrimary = null;

	protected
		$deletePrimary = null,
		$deleteConfirm = false;

	protected
		$tableLinks = array();

	protected
		$autoIncrement = 1;

	protected
		$filter_logic = "AND";

	protected $query_build_parts="";

	protected $table_name=null;
	protected $table_alias_postfix = '';
	protected $table_map=array();

	protected $primary_key=null;

	protected
		$join_map = array();

	/** @var array list of used joins */
	protected $join_registry;

	/** @var string Last executed SQL query */
	protected static $last_query;

	/** @var array Replaced field aliases */
	protected $replaced_aliases;

	/** @var array Replaced table aliases */
	protected $replaced_taliases;

	protected
		$sqlSelect = '',
		$sqlFrom = '',
		$sqlWhere = '',
		$sqlOrder = '',
		$sqlLimit = '';

	protected
		$arSqlSelect=array(),
		$arSqlFrom=array(),
		$arSqlWhere=array(),
		$arSqlOrder=array();

	protected $arFieldsEntity = array();

	public function __construct($type)
	{
		$this->setType($type);
	}

	public function getFieldsEntity ()
	{
		return $this->arFieldsEntity;
	}

	/**
	 * Устанавливет тип Query
	 *
	 * @param string $type
	 */
	private function setType ($type)
	{
		$this->type = $type;
	}

	/**
	 * Возвращает тип Query
	 *
	 * @return null|string $this->type
	 */
	public function getType ()
	{
		return $this->type;
	}

	/**
	 * Устанавливает значения SELECT
	 *
	 * @param string|array $select
	 */
	public function setSelect ($select)
	{
		if (!is_array($select))
			$this->select = array($select);
		else
			$this->select = $select;
	}

	/**
	 * Возвращает значение SELECT
	 *
	 * @return array $this->select
	 */
	private function getSelect ()
	{
		return $this->select;
	}

	/**
	 * Устанавливает название таблицы DB
	 *
	 * @param string $tableName
	 */
	public function setTableName ($tableName)
	{
		$this->table_name = $tableName;
	}

	/**
	 * Возвращает название таблицы DB
	 *
	 * @return null|string $this->table_name
	 */
	private function getTableName ()
	{
		return $this->table_name;
	}

	/**
	 * Устанавливает поле PRIMARY для таблицы
	 *
	 * @param string $key
	 */
	public function setPrimaryKey ($key)
	{
		$this->primary_key = $key;
	}

	/**
	 * Возвращает поле PRIMARY для таблицы
	 *
	 * @return null|string $this->primary_key
	 */
	private function getPrimaryKey ()
	{
		return $this->primary_key;
	}

	/**
	 * Устанавливет карту таблицы DB
	 *
	 * @param array $arMap
	 */
	public function setTableMap ($arMap)
	{
		$this->table_map = $arMap;
	}

	/**
	 * Возвращает карту таблицы DB
	 *
	 * @return array $this->table_map
	 */
	public function getTableMap ()
	{
		return $this->table_map;
	}

	public function setFilter ($filter)
	{
		if (!is_array($filter))
			$this->filter = array($filter);
		else
			$this->filter = $filter;
	}

	private function getFilter ()
	{
		return $this->filter;
	}

	public function setFilterLogic ($logic="AND")
	{
		if ($logic != "AND" && $logic != "OR") $logic="AND";
		$this->filter_logic = $logic;
	}

	private function getFilterLogic ()
	{
		return $this->filter_logic;
	}

	public function setWhere ($where=array())
	{
		if (empty($where))
			$this->where = $this->filter;
		else
			$this->where = $where;
	}

	private function getWhere ()
	{
		return $this->where;
	}

	public function setGroup ($group)
	{
		if (!is_array($group))
			$this->group = array($group);
		else
			$this->group = $group;
	}

	private function getGroup ()
	{
		return $this->group;
	}

	public function setOrder ($order, $by = "ASC")
	{
		if (!is_array($order))
		{
			$this->order = array($order => $by);
		}
		else
		{
			$arOrder = array();
			foreach ($order as $k => $v)
			{
				if (is_numeric($k)) {
					$arOrder[$v] = $by;
				}
				else
				{
					$arOrder[$k] = $v;
				}
			}
			$this->order = $arOrder;
		}
	}

	public function getOrder ()
	{
		return $this->order;
	}

	public function setLimit ($limit)
	{
		$this->limit = $limit;
	}

	private function getLimit ()
	{
		return $this->limit;
	}

	public function setOffset ($offset)
	{
		$this->offset = $offset;
	}

	private function getOffset ()
	{
		return $this->offset;
	}

	public function setRuntime ($runtime)
	{
		$this->runtime = $runtime;
	}

	private function getRuntime ()
	{
		return $this->runtime;
	}

	public function setQueryBuildParts ($sql)
	{
		$this->query_build_parts = $sql;
	}

	public function getQueryBuildParts ()
	{
		return $this->query_build_parts;
	}

	private function setInsertArray ($array)
	{
		$this->insertArray = $array;
	}

	private function getInsertArray ()
	{
		return $this->insertArray;
	}

	private function setAutoIncrement ($autoI)
	{
		$this->autoIncrement = $autoI;
	}

	private function getAutoIncrement ()
	{
		return $this->autoIncrement;
	}

	public function setTableAliasPostfix ($alias)
	{
		$this->table_alias_postfix = $alias;
	}

	private function getTableAliasPostfix ()
	{
		return $this->table_alias_postfix;
	}

	private function setUpdateArray($array)
	{
		$this->updateArray = $array;
	}

	private function getUpdateArray()
	{
		return $this->updateArray;
	}

	private function setUpdatePrimary ($primary)
	{
		$this->updatePrimary = $primary;
	}

	private function getUpdatePrimary ()
	{
		return $this->updatePrimary;
	}

	private function setDeletePrimary ($primary)
	{
		$this->deletePrimary = $primary;
	}

	private function getDeletePrimary ()
	{
		return $this->deletePrimary;
	}

	private function setDeleteConfirm ($confirm=false)
	{
		$this->deleteConfirm = $confirm;
	}

	private function getDeleteConfirm ()
	{
		return $this->deleteConfirm;
	}

	private function setTableLinks ($arLinks)
	{
		$this->tableLinks = $arLinks;
	}

	private function getTableLinks ()
	{
		return $this->tableLinks;
	}

	public function setInsertParams($insertArray=null,$tableName=null,$tableMapArray=null)
	{
		if (!is_null($insertArray))
		{
			if (isset($insertArray[0]))
			{
				$this->setInsertArray($insertArray);
			}
			else
			{
				$this->setInsertArray(array($insertArray));
			}
		}
		if (!is_null($tableName))
		{
			$this->setTableName($tableName);
		}
		if (!is_null($tableMapArray))
		{
			$this->setTableMap($tableMapArray);
		}
	}

	public function setUpdateParams ($updateArray=null,$updatePrimary=null,$tableName=null,$tableMapArray=null)
	{
		if (!is_null($updateArray))
		{
			$this->setUpdateArray($updateArray);
		}
		if (!is_null($updatePrimary))
		{
			$this->setUpdatePrimary($updatePrimary);
		}
		if (!is_null($tableName))
		{
			$this->setTableName($tableName);
		}
		if (!is_null($tableMapArray))
		{
			$this->setTableMap($tableMapArray);
		}
	}

	public function setDeleteParams ($deletePrimary=null,$deleteConfirm=null,$tableName=null,$tableMapArray=null,$tableLinks=null)
	{
		if (!is_null($deletePrimary))
		{
			$this->setDeletePrimary($deletePrimary);
		}
		if (!is_null($deleteConfirm))
		{
			$this->setDeleteConfirm($deleteConfirm);
		}
		if (!is_null($tableName))
		{
			$this->setTableName($tableName);
		}
		if (!is_null($tableMapArray))
		{
			$this->setTableMap($tableMapArray);
		}
		if (!is_null($tableLinks))
		{
			$this->setTableLinks($tableLinks);
		}
	}

	public function setCreateParams ($autoIncrement=null,$tableName=null,$arMapArray=null)
	{
		if (!is_null($autoIncrement))
		{
			$this->setAutoIncrement($autoIncrement);
		}
		if (!is_null($tableName))
		{
			$this->setTableName($tableName);
		}
		if (!is_null($arMapArray))
		{
			$this->setTableMap($arMapArray);
		}
	}

	/**
	 * Выполняет SQL запрос Query
	 *
	 * @throw Exception\SqlQueryException
	 *
	 * @return Lib\DBResult $res
	 */
	public function exec ($debug=false)
	{
		if ($this->getQueryBuildParts() == '')
		{
			$this->setQueryBuildParts(static::BuildQuery());
		}
		if ($debug)
		{
			return $this->getQueryBuildParts();
		}
		else
		{
			$DB = $GLOBALS['DB'];
			try
			{
				$res = $DB->query ($this);
				if (!$res->getResult ())
				{
					throw new Exception\SqlQueryException("Error ".$this->getType ()." query", $res->getResultErrorText (),
												$this->getQueryBuildParts ());
				}
				return $res;
			}
			catch (Exception\SqlQueryException $e)
			{
				echo $e->showException();
			}
		}
	}

	private function BuildQuery ()
	{
		try
		{
			if ($this->getType() == "select")
			{
				$sql = static::BuildQuerySelect();
			}
			elseif ($this->getType() == "create")
			{
				$sql = static::BuildQueryCreate ();
			}
			elseif ($this->getType() == "insert")
			{
				$sql = static::BuildQueryInsert();
			}
			elseif ($this->getType() == "update")
			{
				$sql = static::BuildQueryUpdate();
			}
			elseif ($this->getType() == "delete")
			{
				$sql = static::BuildQueryDelete();
			}
			else
			{
				throw new Exception\ArgumentOutOfRangeException('queryType');
			}
		}
		catch (Exception\ArgumentOutOfRangeException $e)
		{
			die($e->showException());
		}
		return $sql;
	}

	private function CreateSqlSelect ()
	{
		$helper = new Lib\SqlHelper();
		$sqlSelect = "SELECT\n\t";
		$tableName = $this->getTableName();
		$arMap = $this->getTableMap();
		$arSelect = $this->getSelect();
//		msDebug($arSelect);

		if (!isset($this->arSqlFrom[$tableName]))
		{
			$this->arSqlFrom[$tableName] = 1;
		}
//		msDebug($this->arSqlFrom);
		if (!empty($arSelect)) {
			$bSelectFirst = true;
			foreach ($arSelect as $key=>$value)
			{
				if ($bSelectFirst)
				{
					$bSelectFirst = false;
				}
				else
				{
					$sqlSelect .= ",\n\t";
				}
				if (is_numeric($key))
				{
					if (!strpos($value,'.'))
					{
						try
						{
							if (!isset($arMap[$value]))
							{
								throw new Exception\ArgumentOutOfRangeException($tableName.'.'.$value);
							}
							else
							{
								$sqlSelect .= $helper->wrapQuotes($tableName)."."
									.$helper->wrapQuotes($value);
								if (!isset($this->arFieldsEntity[$value]))
								{
									$this->arFieldsEntity[$value] = $arMap[$value];
								}
							}
						}
						catch (Exception\ArgumentOutOfRangeException $e)
						{
							$e->showException();
						}
					}
					else
					{
						$aliasField = str_replace('.','_',$value);
						$arFields = explode('.',$value);
						foreach ($arFields as $k=>$field)
						{
							if (isset($arFields[$k+1]))
							{
								try
								{
									if (isset($this->arFieldsEntity[$field]))
									{
										$fieldMap = $this->arFieldsEntity[$field];
									}
									elseif (isset($arMap[$field]))
									{
										$fieldMap = $arMap[$field];
									}
									elseif (isset($linkedMap) && isset($linkedMap[$field]))
									{
										$fieldMap = $linkedMap[$field];
									}
									else
									{
										throw new Exception\ArgumentOutOfRangeException($field);
									}
								}
								catch (Exception\ArgumentOutOfRangeException $e)
								{
									die($e->showException());
								}
								$linked = $fieldMap->getLink();
								list($linkedTable,$linkedField) = explode('.',$linked);
								$linkedSql = $helper->wrapQuotes($linkedTable).'.'
									.$helper->wrapQuotes($linkedField);
								$linkedClass = Lib\Tools::getClassNameByTableName($linkedTable);
								$linkedMap = $linkedClass::getMapArray();
							}
							if ($k==0)
							{
								$arFieldTable[] = $tableName;
								$arFieldTable[] = $linkedTable;
							}
							else
							{
								$arFieldTable[] = $linkedTable;
							}
							$lastField = $arFields[$k-1];
							$selectField = $field;
						}

						$lastFieldTable = $arFieldTable[count($arFieldTable)-3];
						$sqlSelect.= $helper->wrapQuotes($linkedTable)."."
							.$helper->wrapQuotes($selectField)." AS "
							.$helper->wrapQuotes($aliasField);
						if (!isset($this->arFieldsEntity[$aliasField]))
						{
							$this->arFieldsEntity[$aliasField] = $linkedMap[$selectField];
						}
						if (!isset($this->arSqlFrom[$linkedTable]))
						{
							$this->arSqlFrom[$linkedTable] = 1;
						}
						if (!isset($this->arSqlWhere[$linkedSql]))
						{
							$this->arSqlWhere[$linkedSql] = $helper->wrapQuotes($lastFieldTable)
								.'.'.$helper->wrapQuotes($lastField);
						}
					}
				}
				else
				{
					//msDebug($key); msDebug($value);
					if (!strpos($key,'.'))
					{
						try
						{
							if (!isset($arMap[$key]))
							{
								throw new Exception\ArgumentOutOfRangeException($tableName.'.'.$key);
							}
							else
							{
								$sqlSelect .= $helper->wrapQuotes($tableName)."."
									.$helper->wrapQuotes($key)." AS ".$helper->wrapQuotes($value);
								if (!isset($this->arFieldsEntity[$value]))
								{
									$this->arFieldsEntity[$value] = $arMap[$key];
								}
							}
						}
						catch (Exception\ArgumentOutOfRangeException $e)
						{
							$e->showException();
						}
					}
					else
					{
						$aliasField = $value;
						$arFieldTable = array();
						$arFields = explode('.',$key);
						//msDebug($arFields);
						foreach ($arFields as $k=>$field)
						{
							//msDebug(isset($arFields[$k+1]));
							if (isset($arFields[$k+1]))
							{
								try
								{
									//msDebug($field);
									//msDebug($this->arFieldsEntity);
									//msDebug($arMap);
									//msDebug($linkedMap);
									if (isset($this->arFieldsEntity[$field]))
									{
										$fieldMap = $this->arFieldsEntity[$field];
									}
									elseif (isset($arMap[$field]))
									{
										$fieldMap = $arMap[$field];
									}
									elseif (isset($linkedMap) && isset($linkedMap[$field]))
									{
										$fieldMap = $linkedMap[$field];
									}
									else
									{
										throw new Exception\ArgumentOutOfRangeException($field);
									}
								}
								catch (Exception\ArgumentOutOfRangeException $e)
								{
									die($e->showException());
								}
								$linked = $fieldMap->getLink();
								list($linkedTable,$linkedField) = explode('.',$linked);
								$linkedSql = $helper->wrapQuotes($linkedTable).'.'
									.$helper->wrapQuotes($linkedField);
								$linkedClass = Lib\Tools::getClassNameByTableName($linkedTable);
								$linkedMap = $linkedClass::getMapArray();
							}
							if ($k==0)
							{
								$arFieldTable[] = $tableName;
								$arFieldTable[] = $linkedTable;
							}
							else
							{
								$arFieldTable[] = $linkedTable;
							}
							$lastField = $arFields[$k-1];
							$selectField = $field;
						}

						$lastFieldTable = $arFieldTable[count($arFieldTable)-3];
						$sqlSelect.= $helper->wrapQuotes($linkedTable)."."
							.$helper->wrapQuotes($selectField)." AS "
							.$helper->wrapQuotes($aliasField);
						if (!isset($this->arFieldsEntity[$aliasField]))
						{
							$this->arFieldsEntity[$aliasField] = $linkedMap[$selectField];
						}
						if (!isset($this->arSqlFrom[$linkedTable]))
						{
							$this->arSqlFrom[$linkedTable] = 1;
						}
						if (!isset($this->arSqlWhere[$linkedSql]))
						{
							$this->arSqlWhere[$linkedSql] = $helper->wrapQuotes($lastFieldTable)
								.'.'.$helper->wrapQuotes($lastField);
						}
					}
				}
			}
			//$sql .= $quote.join ($quote.",\n\t".$quote,$arSelect).$quote;
		}
		else
		{
			$sqlSelect .= "*";
		}

		return $sqlSelect."\n";
	}

	private function CreateSqlFrom ()
	{
		$sqlFrom = "FROM\n\t";
		$helper = new Lib\SqlHelper();
		$tableName = $this->getTableName();
		$tableAlias = $this->getTableAliasPostfix();
		$bTableAlias = ($tableAlias != '');
		$bFirst = true;
		//msDebug($this->arSqlFrom);
		foreach ($this->arSqlFrom as $table=>$value)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$sqlFrom.= " ,\n\t";
			}
			if (($table == $tableName) && $bTableAlias)
			{
				$sqlFrom.= $helper->wrapQuotes($tableName)." "
					.$helper->wrapQuotes($tableAlias);
			}
			elseif (!is_numeric($value))
			{
				$sqlFrom.= $helper->wrapQuotes($table)." "
					.$helper->wrapQuotes($value);
			}
			else
			{
				$sqlFrom.= $helper->wrapQuotes($table);
			}
		}

		return $sqlFrom."\n";
	}

	private function CreateSqlWhere ()
	{
		$sqlWhere = "WHERE\n\t";

		$helper = new Lib\SqlHelper();
		$tableName = $this->getTableName();
		$arWhere = $this->getWhere();
		//msDebug($arWhere);

		if (!empty($arWhere))
		{
			$arMap = $this->getTableMap();
			$bFirst = true;
			foreach ($arWhere as $field=>$value)
			{
				$oldField = $field;
				if ($arMask = $this->maskField($field))
				{
					$field = $arMask['field'];
					if (isset($arMask['mask']))
					{
						$mask = $arMask['mask'];
					}
				}
				try
				{
					if (!isset($arMap[$field]))
					{
						throw new Exception\ArgumentOutOfRangeException('arMap['.$field.']');
					}
					else
					{
						if (!is_array($value))
						{
							$value = $arMap[$field]->saveDataModification($value);
						}
						$bEquating_str = false;

						if ($arMap[$field] instanceof IntegerField)
						{
							if (isset($mask))
							{
								if (!is_array($value))
								{
									$equating = ' '.$mask.' ';
								}
								elseif ($mask == "!")
								{
									$equating = ' NOT IN ';
								}
								else
								{
									throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
								}
							}
							else
							{
								if (!is_array($value))
								{
									$equating = " = ";
								}
								else
								{
									$equating = ' IN ';
								}
							}
						}
						elseif ($arMap[$field] instanceof BooleanField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " LIKE ";
							}
							$bEquating_str = true;
						}
						elseif ($arMap[$field] instanceof DateField)
						{
							if (isset($mask))
							{
								if (!is_array($value))
								{
									$equating = ' '.$mask.' ';
								}
								elseif ($mask == "!")
								{
									$equating = ' NOT IN ';
								}
								else
								{
									throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
								}
							}
							else
							{
								if (!is_array($value))
								{
									$equating = " = ";
								}
								else
								{
									$equating = ' IN ';
								}
							}
							$bEquating_str = true;
						}
						elseif ($arMap[$field] instanceof DatetimeField)
						{
							if (isset($mask))
							{
								if (!is_array($value))
								{
									$equating = ' '.$mask.' ';
								}
								elseif ($mask == "!")
								{
									$equating = ' NOT IN ';
								}
								else
								{
									throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
								}
							}
							else
							{
								if (!is_array($value))
								{
									$equating = " = ";
								}
								else
								{
									$equating = ' IN ';
								}
							}
							$bEquating_str = true;
						}
						elseif ($arMap[$field] instanceof EnumField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " = ";
							}
							//TODO: Доделать
						}
						elseif ($arMap[$field] instanceof ExpressionField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " = ";
							}
							//TODO: Доделать
						}
						elseif ($arMap[$field] instanceof FloatField)
						{
							if (isset($mask))
							{
								if (!is_array($value))
								{
									$equating = ' '.$mask.' ';
								}
								elseif ($mask == "!")
								{
									$equating = ' NOT IN ';
								}
								else
								{
									throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
								}
							}
							else
							{
								if (!is_array($value))
								{
									$equating = " = ";
								}
								else
								{
									$equating = ' IN ';
								}
							}
						}
						elseif ($arMap[$field] instanceof ReferenceField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " = ";
							}
							//TODO: Доделать
						}
						elseif ($arMap[$field] instanceof StringField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " LIKE ";
							}
							$bEquating_str = true;
						}
						elseif ($arMap[$field] instanceof TextField)
						{
							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = " LIKE ";
							}
							$bEquating_str = true;
						}

						if (!is_array($value))
						{
							if ($bFirst)
							{
								$sqlWhere .= $helper->wrapQuotes($tableName).'.'
									.$helper->wrapQuotes($field).$equating;
								if ($bEquating_str)
									$sqlWhere .= "'".$value."'";
								else
									$sqlWhere .= $value;
								$bFirst = false;
							}
							else
							{
								$sqlWhere .= ' '.$this->getFilterLogic()."\n\t"
									.$helper->wrapQuotes($tableName).'.'
									.$helper->wrapQuotes($field).$equating;
								if ($bEquating_str)
									$sqlWhere .= "'".$value."'";
								else
									$sqlWhere .= $value;
							}
						}
						else
						{
							if ($bFirst)
							{
								$sqlWhere .= $helper->wrapQuotes($tableName).'.'
									.$helper->wrapQuotes($field).$equating;
								$sqlWhere .= '(';
								$bFFirst = true;
								for ($i=0; $i<count($value); $i++)
								{
									if ($bFFirst)
									{
										$bFFirst = false;
									}
									else
									{
										$sqlWhere .= ', ';
									}
									if ($bEquating_str)
										$sqlWhere .= "'".$value[$i]."'";
									else
										$sqlWhere .= $value[$i];
								}
								$sqlWhere .= ')';
								$bFirst = false;
							}
							else
							{
								$sqlWhere .= ' '.$this->getFilterLogic()."\n\t"
									.$helper->wrapQuotes($tableName).'.'
									.$helper->wrapQuotes($field).$equating;
								$sqlWhere .= '(';
								$bFFirst = true;
								for ($i=0; $i<count($value); $i++)
								{
									if ($bFFirst)
									{
										$bFFirst = false;
									}
									else
									{
										$sqlWhere .= ', ';
									}
									if ($bEquating_str)
										$sqlWhere .= "'".$value[$i]."'";
									else
										$sqlWhere .= $value[$i];
								}
								$sqlWhere .= ')';
							}
						}
					}
				}
				catch (Exception\ArgumentOutOfRangeException $e)
				{
					$e->showException();
				}
			}
		}

		$arSqlWhere = $this->arSqlWhere;
		if (!empty($arSqlWhere))
		{
			if ($sqlWhere != "" && $sqlWhere != "WHERE\n\t")
			{
				$sqlWhere.= " AND\n\t";
			}
			$bFirst = true;
			foreach ($arSqlWhere as $first=>$second)
			{
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$sqlWhere.= " AND\n\t";
				}
				$sqlWhere.= $first." = ".$second;
			}
		}

		if ($sqlWhere == "WHERE\n\t")
			$sqlWhere = "";

		if ($sqlWhere != "")
		{
			$sqlWhere.= "\n";
		}
		return $sqlWhere;
	}

	private function CreateSqlGroup ()
	{
		$sqlGroup = "";
		$helper = new Lib\SqlHelper();
		$arGroup = $this->getGroup();
		if (!empty($arGroup)) {
			//TODO: Доделать
			$sqlGroup .= "GROUP BY\n\t";
			$bFirst = true;
			foreach ($arGroup as $groupField=>$sort)
			{
				if($bFirst)
				{
					$bFirst = false;
					$sqlGroup .= $helper->wrapQuotes($groupField).' '.$sort;
				}
				else
				{
					$sqlGroup .= ",\n\t".$helper->wrapQuotes($groupField).' '.$sort;
				}
			}
			$sqlGroup.="\n";
		}

		return $sqlGroup;
	}

	private function CreateSqlOrder ()
	{
		$sqlOrder = "";
		$helper = new Lib\SqlHelper();
		$arOrder = $this->getOrder();
		$tableName = $this->getTableName();
		if (!empty($arOrder)) {
			$sqlOrder .= "ORDER BY\n\t";
			$bFirst = true;
			//msDebug($arOrder);
			foreach ($arOrder as $sort=>$by)
			{
				if ($bFirst)
				{
					if (!strpos($sort,'.'))
						$sqlOrder .= $helper->wrapQuotes($tableName).'.'.$helper->wrapQuotes($sort).' '.$by;
					else
						$sqlOrder .= $helper->wrapQuotes($sort).' '.$by;
					$bFirst = false;
				}
				else
				{
					if (!strpos($sort,'.'))
						$sqlOrder .= ",\n\t".$helper->wrapQuotes($tableName).'.'.$helper->wrapQuotes($sort).' '.$by;
					else
						$sqlOrder .= ",\n\t".$helper->wrapQuotes($sort).' '.$by;

				}
			}
			$sqlOrder.="\n";
		}

		return $sqlOrder;
	}

	private function CreateSqlLimit ()
	{
		$sqlLimit = "";
		if (!is_null($this->getLimit()))
		{
			$sqlLimit .= "LIMIT ";
			if (!is_null($this->getOffset()) && intval($this->getOffset())>0)
				$sqlLimit .= $this->getOffset();
			else
				$sqlLimit .= 0;
			$sqlLimit .= ', '.$this->getLimit();
			$sqlLimit.= "\n";
		}

		return $sqlLimit;
	}

	private function BuildQuerySelect ()
	{
		$sql = "";

		$sql.= $this->CreateSqlSelect();

		$sql.= $this->CreateSqlFrom();

		$sql.= $this->CreateSqlWhere();

		$sql.= $this->CreateSqlGroup();

		$sql.= $this->CreateSqlOrder();

		$sql.= $this->CreateSqlLimit();

		if (!is_null($this->getRuntime()))
		{
			//TODO: Доделать !is_null($this->getRuntime())
		}

		return $sql;
	}

	/**
	 *
	 * @throw Exception\ArgumentNullException
	 *
	 * @return string $sql
	 */
	private function BuildQueryCreate ()
	{
		$helper = new Lib\SqlHelper();
		$arMap = $this->getTableMap();

		$primaryField="";
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->getTableName()." (\n\t";
		$bFirst = true;
		foreach ($arMap as $fields=>$objData) {
			//var_dump ($objData);
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$sql .= ",\n\t";
			}
			if ($objData->isPrimary()) $primaryField = $objData->getColumnName();
			$field = $objData->getColumnName();
			$sql .= $helper->wrapQuotes($field)." ".$objData->getDataType();
			switch ($objData->getDataType()) {
				case "int":
					$sql .= "(".$objData->getSize().") ";
					break;
				case "varchar":
					$sql .= "(".$objData->getSize().") ";
					break;
				default:
					$sql .= " ";
					break;
			}
			if ($objData->isPrimary() || $objData->isRequired()) {
				$sql .= "NOT NULL ";
				if ($objData instanceof BooleanField) {
					try {
						if (!is_null($objData->getDefaultValueDB())) {
							$sql .= "DEFAULT '".$objData->getDefaultValueDB()."' ";
						}
						else
						{
							throw new Exception\ArgumentNullException($objData->getColumnName());
						}
					}
					catch (Exception\ArgumentNullException $e)
					{
						$e->showException();
					}
				}
				elseif ($objData instanceof DateField)
				{
					if (!is_null($objData->getDefaultValue())) {
						$sql .= "DEFAULT '".$objData->getDefaultValue()."' ";
					}
					else
					{
						//$sql .= "DEFAULT ".$helper->helperDate()->getGetDateFunction()." ";
						$sql .= "";
					}
				}
				else {
					try
					{
						if (!is_null($objData->getDefaultValue())) {
							$sql .= "DEFAULT '".$objData->getDefaultValue()."' ";
						}
						else
						{
							throw new Exception\ArgumentNullException($objData->getColumnName());
						}
					}
					catch (Exception\ArgumentNullException $e)
					{
						$e->showException();
					}
				}
			}
			else {
				if ($objData instanceof BooleanField) {
					if (!is_null($objData->getDefaultValueDB())) {
						$sql .= "DEFAULT '".$objData->getDefaultValueDB()."' ";
					}
					else {
						$sql .= "DEFAULT NULL ";
					}
				}
				else {
					if (!is_null($objData->getDefaultValue())) {
						$sql .= "DEFAULT '".$objData->getDefaultValue()."' ";
					}
					else {
						$sql .= "DEFAULT NULL ";
					}
				}
			}
			if ($objData->isAutocomplete()) {
				$sql .= "AUTO_INCREMENT ";
			}
			if (!is_null($objData->getTitle())) {
				$sql .= "COMMENT '".$objData->getTitle()."'";
			}
		}
		if ($primaryField != "") $sql .= ",\n\tPRIMARY KEY (".$helper->wrapQuotes($primaryField).")";
		$sql .= "\n\t) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=".$this->getAutoIncrement()." ;";

		return $sql;
	}

	private function BuildQueryInsert ()
	{
		$helper = new Lib\SqlHelper ();

		$arDefaultValues = $this->getInsertArray();
		$tableName = $this->getTableName();
		$arMapArray = $this->getTableMap();
		//msDebug($arDefaultValues);
		$sql = "";

		$bFFirts = true;

		$sql .= "INSERT INTO ".$helper->wrapQuotes($tableName)." ";
		foreach ($arDefaultValues as $arValue)
		{
			$sqlNames = "(";
			$sqlValues = "(";
			$bFirst = true;
			foreach ($arMapArray as $field => $obMap)
			{
				$columnName = $obMap->getColumnName();
				$fieldName = $obMap->getName();
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$sqlValues .= ", ";
					$sqlNames .= ", ";
				}
				if (isset($arValue[$fieldName]))
				{
					if (
						$obMap instanceof IntegerField
						|| $obMap instanceof FloatField
					)
					{
						$arValue[$fieldName] = $obMap->saveDataModification($arValue[$fieldName]);
						//$sqlValues .= $arValue[$fieldName];
						$sqlValues .= "'".$arValue[$fieldName]."'";
						$sqlNames .= $helper->wrapQuotes($columnName);
					}
					else
					{
						$arValue[$fieldName] = $obMap->saveDataModification($arValue[$fieldName]);
						$sqlValues .= "'".$arValue[$fieldName]."'";
						$sqlNames .= $helper->wrapQuotes($columnName);
					}
				}
				else
				{
					try
					{
						if ($obMap->isAutocomplete())
						{
							$sqlValues .= 'NULL';
							$sqlNames .= $helper->wrapQuotes($columnName);
						}
						elseif (!$obMap->isRequired())
						{
							$sqlValues .= 'NULL';
							$sqlNames .= $helper->wrapQuotes($columnName);
						}
						elseif (!is_null($obMap->getRun()))
						{
							$arRun = $obMap->getRun();
							if (!isset($arRun['function']))
							{
								return false;
							}
							if (isset($arRun['column']))
							{
								if (!isset($arValue[$arRun['column']]))
								{
									return false;
								}
								else
								{
									if (is_callable($arRun['function']))
									{
										$res = call_user_func($arRun['function'],$arValue[$arRun['column']]);
										if (
											$obMap instanceof IntegerField
											|| $obMap instanceof FloatField
										)
										{
											$sqlValues .= $res;
											$sqlNames .= $helper->wrapQuotes($columnName);
											//$sql .= $res;
										}
										else
										{
											$sqlValues .= "'".$res."'";
											$sqlNames .= $helper->wrapQuotes($columnName);
											//$sql .= "'".$res."'";
										}
									}
								}
							}
						}
						elseif (!is_null($obMap->getDefaultValue()))
						{
							$value = $obMap->getDefaultValue();
							$value = $obMap->saveDataModification($value);
							if (
								$obMap instanceof IntegerField
								|| $obMap instanceof FloatField
							)
							{
								//$sqlValues .= $value;
								$sqlValues .= "'".$value."'";
								$sqlNames .= $helper->wrapQuotes($columnName);
							}
							else
							{
								$sqlValues .= "'".$value."'";
								$sqlNames .= $helper->wrapQuotes($columnName);
							}
						}
						else
						{
							throw new Exception\ArgumentNullException('field['.$fieldName.']');
						}
					}
					catch (Exception\ArgumentNullException $e)
					{
						die($e->showException());
					}
				}
			}
			$sqlNames .= ")";
			$sqlValues .= ")";
			if ($bFFirts)
			{
				$bFFirts = false;
				$sql .= $sqlNames."\nVALUES\n ".$sqlValues;
			}
			else
			{
				$sql .= ",\n".$sqlValues;
			}
		}
		//msDebug($sql);

		return $sql;

	}

	private function BuildQueryUpdate ()
	{
		$helper = new Lib\SqlHelper();
		$arMap = $this->getTableMap();
		$arUpdate = $this->getUpdateArray();
		$primaryId = $this->getUpdatePrimary();

		$sql = "UPDATE \n\t".$helper->wrapQuotes($this->getTableName())."\nSET\n";
		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				try
				{
					if (is_null($primaryId) && intval($arUpdate[$primaryField]) > 0)
					{
						$primaryId = intval($arUpdate[$primaryField]);
					}
					elseif (is_null($primaryId))
					{
						throw new Exception\ArgumentNullException("primaryID");
					}
				}
				catch (Exception\ArgumentNullException $e)
				{
					die($e->showException());
				}
				break;
			}
		}
		$bFirst = true;
		foreach ($arUpdate as $field=>$value)
		{
			try
			{
				if (isset($arMap[$field]))
				{
					$columnName = $arMap[$field]->getColumnName();
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$sql .= ",\n";
					}
					$sql .= "\t".$helper->wrapQuotes($columnName)." = '";

					$value = $arMap[$field]->saveDataModification($value);
					$sql .= $value;

					$sql .= "'";
				}
				else
				{
					throw new Exception\ArgumentOutOfRangeException('arUpdate['.$field.']');
				}
			}
			catch (Exception\ArgumentOutOfRangeException $e_out)
			{
				$e_out->showException();
			}
		}
		$sql .= "\nWHERE\n\t".$helper->wrapQuotes($this->getTableName());
		$sql .= ".".$helper->wrapQuotes($primaryField)." =";
		if ($primaryObj instanceof IntegerField || $primaryObj instanceof FloatField)
		{
			$sql .= $primaryId;
		}
		else
		{
			$sql .= "'".$primaryId."'";
		}
		$sql .= "\nLIMIT 1 ;";


		return $sql;
	}

	private function BuildQueryDelete ()
	{
		$helper = new Lib\SqlHelper();
		$arMap = $this->getTableMap();
		$primaryId = $this->getDeletePrimary();
		$confirm = $this->getDeleteConfirm();
		$arTableLinks = $this->getTableLinks();

		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				break;
			}
		}
		$sql = "DELETE FROM ".$helper->wrapQuotes($this->getTableName());
		$sql .= " WHERE ".$helper->wrapQuotes($this->getTableName()).".";
		$sql .= $helper->wrapQuotes($primaryField)." = ";
		if ($primaryObj instanceof IntegerField || $primaryObj instanceof FloatField)
		{
			$sql .= $primaryId;
		}
		else
		{
			$sql .= "'".$primaryId."'";
		}
		$sql .= " LIMIT 1";

		if (empty($arTableLinks))
		{
			return $sql;
		}
		elseif ($confirm)
		{
			static::sqlMassDelete($this);
			return false;
		}
		else
		{
			$bCanDelete = static::checkCanDelete($this);

			if ($bCanDelete)
			{
				return $sql;
			}
			else
			{
				return false;
			}
		}
	}

	//TODO: Протестировать
	private function sqlMassDelete ($query=null)
	{
		try
		{
			if (is_null($query))
			{
				throw new Exception\ArgumentNullException('query');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		$helper = new Lib\SqlHelper();
		$arMap = $query->getTableMap();
		$primaryId = $query->getDeletePrimary();
		$arTableLinks = $query->getTableLinks();
		$tableName = $query->getTableName();

		foreach ($arTableLinks as $field=>$arLinked)
		{
			foreach ($arLinked as $linkTable=>$linkField)
			{
				if (is_array($linkField))
				{
					foreach ($linkField as $linkF)
					{
						$arRes = Lib\Tools::runTableClassFunction ($linkTable,'getListFunc',array(
							array(
								'select' => array('ID'),
								'filter' => array(
									$linkF => $primaryId
								)
							)
						));
						if ($arRes)
						{
							foreach ($arRes as $delID)
							{
								$deleteQuery = new Query('delete');
								$deleteQuery->setDeleteParams(
									$delID,
									true,
									null,
									Lib\Tools::runTableClassFunction ($linkTable,'getTableMap'),
									Lib\Tools::runTableClassFunction ($linkTable,'getTableLinks')
								);
								$deleteQuery->exec();
							}
						}
					}
				}
				else
				{
					$arRes = Lib\Tools::runTableClassFunction ($linkTable,'getListFunc',array(
						array(
							'select' => array('ID'),
							'filter' => array(
								$linkField => $primaryId
							)
						)
					));
					if ($arRes)
					{
						foreach ($arRes as $delID)
						{
							$deleteQuery = new Query('delete');
							$deleteQuery->setDeleteParams(
								$delID,
								true,
								Lib\Tools::runTableClassFunction ($linkTable,'getTableName'),
								Lib\Tools::runTableClassFunction ($linkTable,'getTableMap'),
								Lib\Tools::runTableClassFunction ($linkTable,'getTableLinks')
							);
							$deleteQuery->exec();
						}
					}
				}
			}
		}

		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				break;
			}
		}

		$sql = "DELETE FROM ".$helper->wrapQuotes($tableName);
		$sql .= " WHERE ".$helper->wrapQuotes($tableName).".";
		$sql .= $helper->wrapQuotes($primaryField)." = ";
		if ($primaryObj instanceof IntegerField || $primaryObj instanceof FloatField)
		{
			$sql .= $primaryId;
		}
		else
		{
			$sql .= "'".$primaryId."'";
		}
		$sql .= " LIMIT 1";

		$delQuery = new Query('delete');
		$delQuery->setQueryBuildParts($sql);
		$res = $delQuery->exec();
	}

	//TODO: Протестировать
	private function checkCanDelete($query=null)
	{
		try
		{
			if (is_null($query))
			{
				throw new Exception\ArgumentNullException('query');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		$primaryId = $query->getDeletePrimary();
		$arTableLinks = $query->getTableLinks();
		$bCanDelete = true;

		foreach ($arTableLinks as $field=>$arLinked)
		{
			foreach ($arLinked as $linkTable=>$linkField)
			{
				if (is_array($linkField))
				{
					foreach ($linkField as $linkF)
					{
						$arRes = Lib\Tools::runTableClassFunction ($linkTable,'getListFunc',array(
							array(
								'select' => array('ID'),
								'filter' => array(
									$linkF => $primaryId
								)
							)
						));
						if ($arRes)
						{
							$bCanDelete = false;
						}
					}
				}
				else
				{
					$arRes = Lib\Tools::runTableClassFunction ($linkTable,'getListFunc',array(
						array(
							'select' => array('ID'),
							'filter' => array(
								$linkField => $primaryId
							)
						)
					));
					if ($arRes)
					{
						$bCanDelete = false;
					}
				}
			}
		}

		return $bCanDelete;
	}

	private function maskField ($field=null)
	{
		static $triple_char = array(
			"!><" => "NB",  //not between
		);
		static $double_char = array(
			"!=" => "NI",   //not Identical
			"!%" => "NS",   //not substring
			"><" => "B",    //between
			">=" => "GE",   //greater or equal
			"<=" => "LE",   //less or equal
		);
		static $single_char = array(
			"=" => "I",     //Identical
			"%" => "S",     //substring
			"?" => "?",     //logical
			">" => "G",     //greater
			"<" => "L",     //less
			"!" => "N",     //not field LIKE val
		);

		try
		{
			if (is_null($field))
			{
				throw new Exception\ArgumentNullException('field');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		$op = substr($field,0,3);
		if ($op && isset($triple_char[$op]))
			return array("field"=>substr($field,3),"mask"=>$op,"operation"=>$triple_char[$op]);
		$op = substr($field,0,2);
		if ($op && isset($double_char[$op]))
			return array("field"=>substr($field,2),"mask"=>$op,"operation"=>$double_char[$op]);
		$op = substr($field,0,1);
		if ($op && isset($single_char[$op]))
			return array("field"=>substr($field,1),"mask"=>$op,"operation"=>$single_char[$op]);

		//return array("field"=>$field,"mask"=>$op,"operation"=>$single_char[$op]);
		return false;


	}
}