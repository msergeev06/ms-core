<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity;
use MSergeev\Core\Exception;

class DataManager {

	const EVENT_ON_BEFORE_ADD = "OnBeforeAdd";
	const EVENT_ON_ADD = "OnAdd";
	const EVENT_ON_AFTER_ADD = "OnAfterAdd";
	const EVENT_ON_BEFORE_UPDATE = "OnBeforeUpdate";
	const EVENT_ON_UPDATE = "OnUpdate";
	const EVENT_ON_AFTER_UPDATE = "OnAfterUpdate";
	const EVENT_ON_BEFORE_DELETE = "OnBeforeDelete";
	const EVENT_ON_DELETE = "OnDelete";
	const EVENT_ON_AFTER_DELETE = "OnAfterDelete";

	public static function getClassName ()
	{
		return __CLASS__;
	}

	/**
	 * getTableName
	 *
	 * @return null|string
	 */
	public static function getTableName()
	{
		return null;
	}

	/**
	 * getTableTitle
	 *
	 * @return null|string
	 */
	public static function getTableTitle()
	{
		return null;
	}

	/**
	 * getMap
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array();
	}

    public static function getMapArray()
    {
        $arMap = static::getMap();
        $arMapArray = array();
        foreach ($arMap as $id=>$field)
        {
            $name = $field->getColumnName();
            $arMapArray[$name] = $field;
        }

        return $arMapArray;
    }

	/**
	 * getArrayDefaultValues
	 *
	 * @return array
	 */
	public static function getArrayDefaultValues () {
		return array();
	}

	public static function getTableLinks () {
		return array();
	}

	public static function add ($parameters)
	{
		$query = static::query("insert");
		$query->setInsertParams(
			$parameters["VALUES"],
			static::getTableName(),
			static::getMapArray()
		);
		$res = $query->exec();

		return $res;
	}

	public static function update ($primary, $parameters)
	{
		$query = static::query("update");
		$query->setUpdateParams(
			$parameters["VALUES"],
			$primary,
			static::getTableName(),
			static::getMapArray()
		);
		$query->exec();

	}

	public static function delete ($primary,$confirm=false)
	{
		$query = static::query("delete");
		$query->setDeleteParams(
			$primary,
			$confirm,
			static::getTableName(),
			static::getMapArray(),
			static::getTableLinks()
		);
		$query->exec();
	}

	public static function getByPrimary ($primary, array $parameters = array())
	{
		static::normalizePrimary($primary);

	}

	public static function getById($id)
	{
		return static::getByPrimary($id);
	}

	public static function getPrimaryField ()
	{
		$arMap = static::getMap();
		foreach ($arMap as $field)
		{
			$columnName = $field->getColumnName();
			if ($field->isPrimary()) {
				return $columnName;
			}
		}
	}

	public static function getTableFields()
	{
		$arMap = static::getMap();
		$arTableFields = array();
		foreach ($arMap as $field)
		{
			$arTableFields[] = $field->getColumnName();
		}
		return $arTableFields;
	}

	public static function getListFunc ()
	{
		try
		{
			if (func_num_args() <= 0)
			{
				throw new Exception\ArgumentNullException('params');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}
		$params = func_get_arg(0);

		return static::getList($params[0]);
	}

	public static function getList ($arParams,$showSql=false)
	{
		$query = new Entity\Query("select");

		$arMap = static::getMapArray();
		$query->setTableMap($arMap);

		$tableName = static::getTableName();
		$query->setTableName($tableName);

		$primaryField = static::getPrimaryField();
		$query->setPrimaryKey($primaryField);

		foreach ($arParams as $field=>$values)
		{
			switch ($field)
			{
				case 'select':
					$query->setSelect($values);
					break;
				case 'filter':
					$query->setFilter($values);
					$query->setWhere();
					break;
				case 'group':
					$query->setGroup($values);
					break;
				case 'order':
					$query->setOrder($values);
					break;
				case 'limit':
					$query->setLimit($values);
					break;
				case 'offset':
					$query->setOffset($values);
					break;
				case 'runtime':
					$query->setRuntime($values);
					break;
			}
		}
		$arOrder = $query->getOrder();
		if (empty($arOrder))
		{
			$arOrder = array($primaryField => "ASC");
			$query->setOrder($arOrder);
		}

		$res = $query->exec();
		$arResult = array();
		while ($ar_res = $res->fetch())
		{
			$arResult[] = $ar_res;
		}

		if (!empty($arResult))
		{
			$tmpResult = $arResult;
			$arResult = array();
			for ($i=0; $i<count($tmpResult); $i++)
			{
				foreach ($tmpResult[$i] as $field=>$value)
				{
					if(!is_numeric($field))
					{
						$arResult[$i][$field] = $value;
					}
				}
			}

			if (!empty($arResult))
			{
				if ($showSql)
				{
					$arResult['SQL'] = $res->getSql();
				}
				return $arResult;
			}
			else
				return false;
		}
		else
		{
			if ($showSql)
			{
				$arResult['SQL'] = $res->getSql();
				return $arResult;
			}
			else
			{
				return false;
			}
		}


	}

	public static function query ($type)
	{
		return new Entity\Query($type);
	}

	public static function insertDefaultRows ()
	{
		$arDefaultValues = static::getArrayDefaultValues();
		if (count($arDefaultValues)>0)
		{
			$query = new Entity\Query("insert");
			$query->setInsertParams(
				$arDefaultValues,
				static::getTableName(),
				static::getMapArray()
			);
			$res = $query->exec();

			return $res;
		}
		else {
			return false;
		}
	}

	public static function createTable ()
	{
		$AUTO_INCREMENT = count(static::getArrayDefaultValues())+1;
		$query = static::query("create");
		$query->setCreateParams(
			$AUTO_INCREMENT,
			static::getTableName(),
			static::getMapArray()
		);
		$res = $query->exec();

		return $res;
	}

	/*
	public static function installTable ()
	{
		$resCreate = static::createTable();
		$resInsert = static::insertDefaultRows();

		return ($resCreate && $resInsert);
	}
	*/

	protected static function normalizePrimary (&$primary)
	{
		$prim = '';
		if (!is_array($primary)) {
			$arMap = static::getMap();
			foreach ($arMap as $field=>$array) {
				if (isset($array["primary"])) {
					$prim = $field;
				}
			}
			if ($prim == '') $prim = 'ID';
			$primary = array('='.$prim => $primary);
		}
	}

	public static function checkTableLinks()
	{
		$bLinks = false;

		$helper = new SqlHelper();
		$arLinks = static::getTableLinks();
		$tableName = static::getTableName();
		foreach ($arLinks as $field=>$arLink)
		{
			$sql = "SELECT\n\t".'t.'.$helper->wrapQuotes($field)."\n";
			$sql .= "FROM\n\t".$helper->wrapQuotes($tableName)." t";
			$where = "WHERE\n\t";

			$t=0;
			$bFirst = true;
			foreach ($arLink as $tableName=>$fieldName)
			{
				$t++;
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$where .= " AND\n\t";
				}
				$sql .= ",\n\t";
				$sql .= $helper->wrapQuotes($tableName)." t".$t;
				$where .= "t".$t
					.".".$helper->wrapQuotes($fieldName)
					." = t.".$helper->wrapQuotes($field);
			}
			$sql .= "\n".$where;

			$query = new Entity\Query("select");
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			if ($ar_res = $res->fetch())
			{
				$bLinks = true;
			}
		}

		return $bLinks;
	}

}