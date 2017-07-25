<?php
/**
 * MSergeev\Core\Lib\DataManager
 * Используется для описания и обработки таблиц базы данных.
 * Наследуется в классах описания таблиц ядра и пакетов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity;
use MSergeev\Core\Exception;

class DataManager {

	/** Константы для обработки событий. Временно не используются */
	const EVENT_ON_BEFORE_ADD       = "OnBeforeAdd";
	const EVENT_ON_ADD              = "OnAdd";
	const EVENT_ON_AFTER_ADD        = "OnAfterAdd";
	const EVENT_ON_BEFORE_UPDATE    = "OnBeforeUpdate";
	const EVENT_ON_UPDATE           = "OnUpdate";
	const EVENT_ON_AFTER_UPDATE     = "OnAfterUpdate";
	const EVENT_ON_BEFORE_DELETE    = "OnBeforeDelete";
	const EVENT_ON_DELETE           = "OnDelete";
	const EVENT_ON_AFTER_DELETE     = "OnAfterDelete";

	/**
	 * Возвращает имя текущего класса
	 *
	 * @api
	 *
	 * @return string Имя класса
	 */
	public static function getClassName ()
	{
		return __CLASS__;
	}

	/**
	 * Возвращает название таблицы в базе
	 *
	 * @api
	 *
	 * @example 'ms_core_options'
	 *
	 * @return string название таблицы в базе
	 */
	public static function getTableName()
	{
		return null;
	}

	/**
	 * Возвращает описание таблицы
	 *
	 * @api
	 *
	 * @example 'Опции'
	 *
	 * @return string Текст описания таблицы
	 */
	public static function getTableTitle()
	{
		return null;
	}

	/**
	 * Возвращает массив сущностей полей таблицы базы данных.
	 * Не рекомендуется использовать в API. Используйте getMapArray
	 *
	 * @see static::getMapArray
	 *
	 * @return array Массив сущностей полей таблицы базы данных
	 */
	public static function getMap()
	{
		return array();
	}

	/**
	 * Возвращает обработанный массив сущностей полей таблицы базы данных.
	 * Обрабатывает массив, полученный функцией getMap
	 *
	 * @api
	 *
	 * @return array Обработанный массив сущностей полей таблицы базы данных
	 */
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
	 * Возвращает массив дефолтных значений таблицы,
	 * которые добавляются в таблицу при установке ядра или пакета
	 *
	 * @api
	 *
	 * @return array Массив дефолтных значений таблицы
	 */
	public static function getArrayDefaultValues () {
		return array();
	}

	/**
	 * Возвращает массив описывающий связанные с таблицей другие таблицы
	 * и объединяющие их поля
	 *
	 * @api
	 *
	 * @return array Массив связей таблиц
	 */
	public static function getTableLinks () {
		return array();
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после создания таблицы
	 *
	 * @return null|string
	 */
	public static function getAdditionalCreateSql ()
	{
		return null;
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после удаления таблицы
	 *
	 * @return null|string
	 */
	public static function getAdditionalDeleteSql ()
	{
		return null;
	}

	/**
	 * Добавляет значения в таблицу
	 *
	 * @ignore
	 *
	 * @param array $parameters Массив содержащий значения таблицы в поле 'VALUES'
	 *
	 * @return DBResult Результат mysql запроса
	 */
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

	/**
	 * Обновляет значения в таблице
	 *
	 * @ignore
	 *
	 * @param mixed $primary Поле PRIMARY таблицы
	 * @param array $parameters Массив значений таблицы в поле 'VALUES'
	 *
	 * @return DBResult Результат mysql запроса
	 */
	public static function update ($primary, $parameters)
	{
		$query = static::query("update");
		$query->setUpdateParams(
			$parameters["VALUES"],
			$primary,
			static::getTableName(),
			static::getMapArray()
		);
		$res = $query->exec();

		return $res;
	}

	/**
	 * Удаляет запись из таблицы
	 *
	 * @ignore
	 *
	 * @param mixed $primary Поле PRIMARY таблицы
	 * @param bool  $confirm Флаг, подтверждающий удаление всех связанных записей в других таблицах
	 *
	 * @return DBResult Результат mysql запроса
	 */
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
		$res = $query->exec();

		return $res;
	}

	/**
	 * Возвращает запись по ID
	 *
	 * @param       $primary
	 * @param array $arSelect
	 * @param bool  $showSql
	 *
	 * @return array
	 */
	public static function getByPrimary ($primary, array $arSelect = array(),$showSql=false)
	{
		//static::normalizePrimary($primary);
		$arList['filter'] = array('ID'=>$primary);
		$arList['limit'] = 1;
		if (!empty($arSelect))
		{
			$arList['select'] = $arSelect;
		}
		$arRes = static::getOne($arList,$showSql);

		return $arRes;
	}

	/**
	 * Возвращает запись по ID
	 *
	 * @param $id
	 * @param array $arSelect
	 * @param bool  $showSql
	 *
	 * @return array
	 */
	public static function getById($id, array $arSelect = array(), $showSql=false)
	{
		return static::getByPrimary($id, $arSelect, $showSql);
	}

	/**
	 * Возвращает поле PRIMARY таблицы
	 *
	 * @api
	 *
	 * @return string|bool Название поля, либо false
	 */
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

		return false;
	}

	/**
	 * Возвращает массив полей таблицы
	 *
	 * @api
	 *
	 * @return array Массив полей таблицы
	 */
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

	/**
	 * Обертка для вызова функции getList с произвольными параметрами
	 * @see static::getList
	 *
	 * @api
	 *
	 * @return array|bool
	 */
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

	public static function getTableMap ()
	{
		return static::getMap();
	}

	/**
	 * Осуществляет выборку из таблицы значений по указанным параметрам
	 *
	 * @api
	 *
	 * @param array $arParams Параметры запроса к базе данных
	 * @param bool  $showSql  Показать SQL запрос, вместо выборки (для отладки)
	 *
	 * @return array|bool Массив значений таблицы, массив с SQL запросом, либо в случае неудачи false
	 */
	public static function getList ($arParams=array(),$showSql=false)
	{
		$query = new Entity\Query("select");

		$arMap = static::getMapArray();
		$query->setTableMap($arMap);

		$tableName = static::getTableName();
		$query->setTableName($tableName);

		$primaryField = static::getPrimaryField();
		$query->setPrimaryKey($primaryField);
		$setLimit= null;

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
				case 'filter_logic':
					$query->setFilterLogic($values);
					break;
				case 'group':
					$query->setGroup($values);
					break;
				case 'order':
					$query->setOrder($values);
					break;
				case 'limit':
					$setLimit = $values;
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

		//msDebug($arMap);
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
						$arFieldsEntity = $query->getFieldsEntity();
						if (!empty($arFieldsEntity) && !is_null($arFieldsEntity[$field]))
						{
							$arResult[$i][$field] = $arFieldsEntity[$field]::fetchDataModification($value, $arFieldsEntity[$field]);
						}
						elseif (!is_null($arMap[$field]))
						{
							$arResult[$i][$field] = $arMap[$field]::fetchDataModification($value, $arMap[$field]);
						}
						else
						{
							$arResult[$i][$field] = $value;
						}
					}
				}
			}

			if (!empty($arResult))
			{
				if ($showSql)
				{
					$arResult['SQL'] = $res->getSql();
				}
				//TODO: Раскомментировать и отловить все ошибки. А то Кузя орет =(
				/*
				if (!is_null($setLimit) && intval($setLimit)==1)
				{
					$arResult = $arResult[0];
				}
				*/
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

	public static function getOne ($arParams=array(),$showSql=false)
	{
		$arParams['limit'] = 1;
		$arRes = static::getList($arParams,$showSql);
		if ($showSql)
		{
			$sql = $arRes['SQL'];
			unset($arRes['SQL']);
			if (isset($arRes[0]))
			{
				$arRes = $arRes[0];
			}
			$arRes['SQL'] = $sql;
		}
		elseif ($arRes)
		{
			$arRes = $arRes[0];
		}

		return $arRes;
	}

	/**
	 * Функция возвращает объект Query заданного типа
	 *
	 * @api
	 *
	 * @param string $type Тип объекта Query
	 *
	 * @return Entity\Query объект Query заданного типа
	 */
	public static function query ($type)
	{
		return new Entity\Query($type);
	}

	/**
	 * Функция добавляет в таблицу значения по-умолчанию, описанные в файле таблицы
	 *
	 * @api
	 *
	 * @return bool|DBResult Результат mysql запроса, либо false
	 */
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

	/**
	 * Функция создает таблицу
	 *
	 * @api
	 *
	 * @return DBResult Результат mysql запроса
	 */
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
		if ($res->getResult())
		{
			$additionalSql = static::getAdditionalCreateSql();
			if (!is_null($additionalSql))
			{
				$query = new Entity\Query('create');
				$query->setQueryBuildParts($additionalSql);
				$query->exec();
			}

			static::OnAfterCreateTable();
		}

		return $res;
	}

	/**
	 * Функция нормализует поле PRIMARY, переданное по ссылке
	 *
	 * @ignore
	 *
	 * @param mixed $primary
	 */
	protected static function normalizePrimary (&$primary)
	{
		$prim = '';
		if (!is_array($primary)) {
			$arMap = static::getMap();
			//msDebug($arMap);
			foreach ($arMap as $field=>$array) {
				//msDebug($array);
				if (isset($array["primary"])) {
					$prim = $field;
				}
			}
			if ($prim == '') $prim = 'ID';
			$primary = array('='.$prim => $primary);
		}
	}

	/**
	 * Функция проверяет описанные связи таблицы, используя запросы к DB
	 *
	 * @api
	 *
	 * @return bool Связи существуют - true, инае - false
	 */
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

	public static function OnAfterCreateTable (){}

}