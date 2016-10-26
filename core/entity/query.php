<?php
/**
 * MSergeev\Core\Entity\Query
 * Сущность запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;
use MSergeev\Core\Exception;
use \MSergeev\Core\Lib;

/**
 * Class Query
 * @package MSergeev\Core\Entity
 *
 * @var string      $type                   Тип Query (select|create|insert|update|delete|drop)
 * @var array       $select                 Необработанный массив параметра select
 * @var array       $group                  Необработанный массив параметра group
 * @var array       $order                  Необработанный массив параметра order
 * @var int|null    $limit                  Значение лимита записей
 * @var int|null    $offset                 Сдвиг получения записей
 * @var array       $filter                 Необработанный массив параметра filter
 * @var array       $where                  Необработанный массив параметра where
 * @var array       $insertArray            Массив значений для INSERT запроса
 * @var array       $updateArray            Массив значений для UPDATE запроса
 * @var string      $updatePrimary          Поле PRIMARY для UPDATE запроса
 * @var string      $deletePrimary          Поле PRIMARY для DELETE запроса
 * @var bool        $deleteConfirm          Флаг подтверждения удаления зависимых полей
 * @var array       $tableLinks             Массив связей с полями таблицы
 * @var int         $autoIncrement          Значение AUTO_INCREMENT для таблицы
 * @var string      $filter_logic           Логика фильтра (AND|OR)
 * @var string      $query_build_parts      Собранный SQL запрос
 * @var string      $table_name             Название таблицы
 * @var string      $table_alias_postfix    Алиас таблицы
 * @var array       $table_map              Описание полей таблицы
 * @var string      $primary_key            Поле PRIMARY
 * @var string      $sqlSelect              Обработанный блок SELECT
 * @var string      $sqlFrom                Обработанный блок FROM
 * @var string      $sqlWhere               Обработанный блок WHERE
 * @var string      $sqlOrder               Обработанный блок ORDER
 * @var string      $sqlLimit               Обработанный блок LIMIT
 * @var array       $arSqlFrom              Обработанный массив FROM
 * @var array       $arSqlWhere             Обработанный массив WHERE
 * @var array       $arFieldsEntity         Массив сущностей полей таблицы
 *
 * Временно не используемые переменные:
 * @var int|null    $count_total
 * @var int|null    $runtime
 * @var array       $having         Необработанный массив параметра having
 * @var             $join_map
 * @var             $join_registry
 * @var             $last_query
 * @var             $replaced_aliases
 * @var             $replaced_taliases
 */
class Query
{
	/**
	 * @var string Тип Query (select|create|insert|update|delete|drop)
	 */
	protected $type=null;

	/**
	 * @var array Необработанный массив параметра select
	 */
	protected $select = array();

	/**
	 * @var array Необработанный массив параметра group
	 */
	protected $group = array();

	/**
	 * @var array Необработанный массив параметра order
	 */
	protected $order = array();

	/**
	 * @var null Значение лимита записей
	 */
	protected $limit = null;

	/**
	 * @var null Сдвиг получения записей
	 */
	protected $offset = null;

	/**
	 * @var array Необработанный массив параметра filter
	 */
	protected $filter = array();

	/**
	 * @var array Необработанный массив параметра where
	 */
	protected $where = array();

	/**
	 * @var array Массив значений для INSERT запроса
	 */
	protected $insertArray = array();

	/**
	 * @var array Массив значений для UPDATE запроса
	 */
	protected $updateArray = array();

	/**
	 * @var string Поле PRIMARY для UPDATE запроса
	 */
	protected $updatePrimary = null;

	/**
	 * @var string Поле PRIMARY для DELETE запроса
	 */
	protected $deletePrimary = null;

	/**
	 * @var bool Флаг подтверждения удаления зависимых полей
	 */
	protected $deleteConfirm = false;

	/**
	 * @var array Массив связей с полями таблицы
	 */
	protected $tableLinks = array();

	/**
	 * @var int Значение AUTO_INCREMENT для таблицы
	 */
	protected $autoIncrement = 1;

	/**
	 * @var string Логика фильтра (AND|OR)
	 */
	protected $filter_logic = "AND";

	/**
	 * @var string Собранный SQL запрос
	 */
	protected $query_build_parts="";

	/**
	 * @var string Название таблицы
	 */
	protected $table_name=null;

	/**
	 * @var string Алиас таблицы
	 */
	protected $table_alias_postfix = '';

	/**
	 * @var array Описание полей таблицы
	 */
	protected $table_map=array();

	/**
	 * @var string Поле PRIMARY
	 */
	protected $primary_key=null;

	/**
	 * @var string Обработанный блок SELECT
	 */
	protected $sqlSelect = '';

	/**
	 * @var string Обработанный блок FROM
	 */
	protected $sqlFrom = '';

	/**
	 * @var string Обработанный блок WHERE
	 */
	protected $sqlWhere = '';

	/**
	 * @var string Обработанный блок ORDER
	 */
	protected $sqlOrder = '';

	/**
	 * @var string Обработанный блок LIMIT
	 */
	protected $sqlLimit = '';

	/**
	 * @var array Обработанный массив FROM
	 */
	protected $arSqlFrom=array();

	/**
	 * @var array Обработанный массив WHERE
	 */
	protected $arSqlWhere=array();

	/**
	 * @var array Массив сущностей полей таблицы
	 */
	protected $arFieldsEntity = array();

	//protected $arSqlSelect=array();
	//protected $arSqlOrder=array();
	/**
	 * Временно не используется
	 * @var null
	 */
	protected $count_total = null;

	/**
	 * Временно не используется
	 * @var null
	 */
	protected $runtime = null;

	/**
	 * Временно не используется
	 * @var array Необработанный массив параметра having
	 */
	protected $having = array();

	/**
	 * Временно не используется
	 * @var array
	 */
	protected $join_map = array();

	/**
	 * Временно не используется
	 * @var array list of used joins
	 */
	protected $join_registry;

	/**
	 * Временно не используется
	 * @var string Last executed SQL query
	 */
	protected static $last_query;

	/**
	 * Временно не используется
	 * @var array Replaced field aliases
	 */
	protected $replaced_aliases;

	/**
	 * Временно не используется
	 * @var array Replaced table aliases
	 */
	protected $replaced_taliases;



	/**
	 * Конструктор. Создает объект query запроса указанного типа: (select|create|insert|update|delete|drop)
	 *
	 * @param string $type
	 */
	public function __construct($type)
	{
		$this->setType($type);
	}

	/**
	 * Возвращает массив сущностей полей таблицы
	 *
	 * @return array
	 */
	public function getFieldsEntity ()
	{
		return $this->arFieldsEntity;
	}

	/**
	 * Устанавливет тип Query
	 *
	 * @param string $type
	 */
	protected function setType ($type)
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
	protected function getSelect ()
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
	protected function getTableName ()
	{
		return $this->table_name;
	}

	/**
	 * Возвращает комментарий таблицы DB
	 *
	 * @return mixed
	 */
	protected function getTableTitle()
	{
		$tableName = $this->table_name;
		$tableName = str_replace("ms_","",$tableName);
		$arName = explode("_",$tableName);
		$package = array_shift($arName);

		if ($package=="core")
		{
			$namespace = "MSergeev\\Core\\Tables\\";
		}
		else
		{
			$namespace = "MSergeev\\Packages\\".Lib\Tools::setFirstCharToBig ($package)."\\Tables\\";
		}
		if (is_array($arName))
		{
			$tName = "";
			foreach ($arName as $n)
			{
				$tName .= Lib\Tools::setFirstCharToBig ($n);
			}
		}
		else
		{
			$tName = Lib\Tools::setFirstCharToBig ($arName);
		}
		$tName .= "Table";
		$runClass = $namespace.$tName;

		return $runClass::getTableTitle();
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
	protected function getPrimaryKey ()
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

	/**
	 * Задает необработанный массив параметра filter
	 *
	 * @param $filter
	 */
	public function setFilter ($filter)
	{
		if (!is_array($filter))
			$this->filter = array($filter);
		else
			$this->filter = $filter;
	}

	/**
	 * Возвращает необработанный массив параметра filter
	 *
	 * @return array
	 */
	protected function getFilter ()
	{
		return $this->filter;
	}

	/**
	 * Устанавливает логику работы фильтра
	 *
	 * @param string $logic
	 */
	protected function setFilterLogic ($logic="AND")
	{
		if ($logic != "AND" && $logic != "OR") $logic="AND";
		$this->filter_logic = $logic;
	}

	/**
	 * Возвращает логику работы фильтра
	 *
	 * @return string
	 */
	protected function getFilterLogic ()
	{
		return $this->filter_logic;
	}

	/**
	 * Устанавливает значение в необработанный массив параметра where
	 *
	 * @param array $where
	 */
	public function setWhere ($where=array())
	{
		if (empty($where))
			$this->where = $this->filter;
		else
			$this->where = $where;
	}

	/**
	 * Возвращает необработанный массив параметра where
	 *
	 * @return array
	 */
	protected function getWhere ()
	{
		return $this->where;
	}

	/**
	 * Устанавливает значение в необработанный массив параметра group
	 *
	 * @param $group
	 */
	public function setGroup ($group)
	{
		if (!is_array($group))
			$this->group = array($group);
		else
			$this->group = $group;
	}

	/**
	 * Возвращает необработанный массив параметра order
	 *
	 * @return array
	 */
	protected function getGroup ()
	{
		return $this->group;
	}

	/**
	 * Устанавливает значение в необработанный массив параметра order
	 *
	 * @param        $order
	 * @param string $by
	 */
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

	/**
	 * Возвращает необработанный массив параметра order
	 *
	 * @return array
	 */
	public function getOrder ()
	{
		return $this->order;
	}

	/**
	 * Устанавливает значение лимита записей
	 *
	 * @param $limit
	 */
	public function setLimit ($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * Возвращает значение лимита записей
	 *
	 * @return null
	 */
	protected function getLimit ()
	{
		return $this->limit;
	}

	/**
	 * Устанавливает сдвиг получения записей
	 *
	 * @param $offset
	 */
	public function setOffset ($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * Возвращает сдвиг получения записей
	 *
	 * @return null
	 */
	protected function getOffset ()
	{
		return $this->offset;
	}

	/**
	 * Временно не используется
	 *
	 * @param $runtime
	 */
	public function setRuntime ($runtime)
	{
		$this->runtime = $runtime;
	}

	/**
	 * Временно не используется
	 *
	 * @return null
	 */
	protected function getRuntime ()
	{
		return $this->runtime;
	}

	/**
	 * Устанавливает собранный SQL запрос
	 *
	 * @param $sql
	 */
	public function setQueryBuildParts ($sql)
	{
		$this->query_build_parts = $sql;
	}

	/**
	 * Возвращает собранный SQL запрос
	 *
	 * @return string
	 */
	public function getQueryBuildParts ()
	{
		return $this->query_build_parts;
	}

	/**
	 * Устанавливает массив значений для INSERT запроса
	 *
	 * @param $array
	 */
	private function setInsertArray ($array)
	{
		$this->insertArray = $array;
	}

	/**
	 * Возвращает массив значений для INSERT запроса
	 *
	 * @return array
	 */
	private function getInsertArray ()
	{
		return $this->insertArray;
	}

	/**
	 * Задает значение AUTO_INCREMENT для таблицы
	 *
	 * @param $autoI
	 */
	private function setAutoIncrement ($autoI)
	{
		$this->autoIncrement = $autoI;
	}

	/**
	 * Возвращает значение AUTO_INCREMENT для таблицы
	 *
	 * @return int
	 */
	private function getAutoIncrement ()
	{
		return $this->autoIncrement;
	}

	/**
	 * Задает алиас таблицы
	 *
	 * @param $alias
	 */
	protected function setTableAliasPostfix ($alias)
	{
		$this->table_alias_postfix = $alias;
	}

	/**
	 * Возвращает алиас таблицы
	 *
	 * @return string
	 */
	protected function getTableAliasPostfix ()
	{
		return $this->table_alias_postfix;
	}

	/**
	 * Задает массив значений для UPDATE запроса
	 *
	 * @param $array
	 */
	protected function setUpdateArray($array)
	{
		$this->updateArray = $array;
	}

	/**
	 * Возвращает массив значений для UPDATE запроса
	 *
	 * @return array
	 */
	protected function getUpdateArray()
	{
		return $this->updateArray;
	}

	/**
	 * Задает поле PRIMARY для UPDATE запроса
	 *
	 * @param $primary
	 */
	protected function setUpdatePrimary ($primary)
	{
		$this->updatePrimary = $primary;
	}

	/**
	 * Возвращает поле PRIMARY для UPDATE запроса
	 *
	 * @return string
	 */
	protected function getUpdatePrimary ()
	{
		return $this->updatePrimary;
	}

	/**
	 * Задает поле PRIMARY для DELETE запроса
	 *
	 * @param $primary
	 */
	protected function setDeletePrimary ($primary)
	{
		$this->deletePrimary = $primary;
	}

	/**
	 * Возвращает поле PRIMARY для DELETE запроса
	 *
	 * @return string
	 */
	protected function getDeletePrimary ()
	{
		return $this->deletePrimary;
	}

	/**
	 * Задает флаг подтверждения удаления зависимых полей
	 *
	 * @param bool $confirm
	 */
	protected function setDeleteConfirm ($confirm=false)
	{
		$this->deleteConfirm = $confirm;
	}

	/**
	 * Возвращает флаг подтверждения удаления зависимых полей
	 *
	 * @return bool
	 */
	protected function getDeleteConfirm ()
	{
		return $this->deleteConfirm;
	}

	/**
	 * Задает массив связей с полями таблицы
	 *
	 * @param $arLinks
	 */
	protected function setTableLinks ($arLinks)
	{
		$this->tableLinks = $arLinks;
	}

	/**
	 * Возвращает массив связей с полями таблицы
	 *
	 * @return array
	 */
	protected function getTableLinks ()
	{
		return $this->tableLinks;
	}

	/**
	 * Заполняет все необходимые параметры для INSERT запроса
	 *
	 * @api
	 *
	 * @param array     $insertArray    Массив добавляемый полей => значений
	 * @param string    $tableName      Название таблицы
	 * @param array     $tableMapArray  Массив сущностей полей таблицы
	 */
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

	/**
	 * Заполняет все необходимые параметры для UPDATE запроса
	 *
	 * @api
	 *
	 * @param array     $updateArray    Массив обновляемых полей => значений
	 * @param mixed     $updatePrimary  Значение поля PRIMARY обновляемой записи
	 * @param string    $tableName      Название таблицы
	 * @param array     $tableMapArray  Массив сущностей полей таблицы
	 */
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

	/**
	 * Заполняет все необходимые параметры для DELETE запроса
	 *
	 * @api
	 *
	 * @param mixed     $deletePrimary  Значение поля PRIMARY удаляемой записи
	 * @param bool      $deleteConfirm  Флаг, подтверждающий удаление связанных записей
	 * @param string    $tableName      Название таблицы
	 * @param array     $tableMapArray  Массив сущностей полей таблицы
	 * @param array     $tableLinks     Массив связей полей таблицы с другими таблицами
	 */
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

	/**
	 * Заполняет все необходимые параметры для CREATE запроса
	 *
	 * @api
	 *
	 * @param int|null  $autoIncrement  Значение AUTO_INCREMENT для таблицы
	 * @param string    $tableName      Название таблицы
	 * @param array     $arMapArray     Массив сущностей полей таблицы
	 */
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
	 * @api
	 *
	 * @param bool $debug Включен ли режим отладки. Если true, функция просто возвращает собранный SQL запрос
	 *
	 * @throw Exception\SqlQueryException
	 *
	 * @return Lib\DBResult $res Возвращает объект типа DBResult
	 */
	public function exec ($debug=false)
	{
		if ($this->getQueryBuildParts() == '')
		{
			$this->setQueryBuildParts($this->BuildQuery());
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

	/**
	 * Определяет обработчик, который будет собирать SQL запрос.
	 * Обработчик зависит от типа Query (select|create|insert|update|delete)
	 *
	 * @return bool|string SQL запрос, либо false
	 */
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

	/**
	 * Собирает блок SELECT SQL запроса
	 *
	 * @return string SQL запрос
	 */
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

	/**
	 * Собирает блок FROM SQL запроса
	 *
	 * @return string
	 */
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

	/**
	 * Собирает блок WHERE SQL запроса
	 *
	 * @return string
	 */
	private function CreateSqlWhere ()
	{
		$sqlWhere = "WHERE\n\t";

		$tableName = $this->getTableName();
		$arWhere = $this->getWhere();
		$helper = new Lib\SqlHelper($tableName);
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
						$bEquating_str = FALSE;
						$bValueField = false;
						$equating = ' = ';
						if (!is_array($value) && strpos($value,'FIELD_')!==false)
						{

							if (isset($mask))
							{
								$equating = ' '.$mask.' ';
							}
							else
							{
								$equating = ' = ';
							}
							$bValueField = true;
							$value = str_replace('FIELD_','',$value);
						}
						else
						{
							if (!is_array ($value))
							{
								$fieldClassName = $arMap[$field]->getClassName ();
								$value = $fieldClassName::saveDataModification ($value, $arMap[$field]);
							}

							if ($arMap[$field] instanceof IntegerField)
							{
								if (isset($mask))
								{
									if (!is_array ($value))
									{
										$equating = ' '.$mask.' ';
									} elseif ($mask == "!")
									{
										$equating = ' NOT IN ';
									} else
									{
										throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
									}
								} else
								{
									if (!is_array ($value))
									{
										$equating = " = ";
									} else
									{
										$equating = ' IN ';
									}
								}
							} elseif ($arMap[$field] instanceof BooleanField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " LIKE ";
								}
								$bEquating_str = TRUE;
							} elseif ($arMap[$field] instanceof DateField)
							{
								if (isset($mask))
								{
									if (!is_array ($value))
									{
										$equating = ' '.$mask.' ';
									} elseif ($mask == "!")
									{
										$equating = ' NOT IN ';
									} else
									{
										throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
									}
								} else
								{
									if (!is_array ($value))
									{
										$equating = " = ";
									} else
									{
										$equating = ' IN ';
									}
								}
								$bEquating_str = TRUE;
							} elseif ($arMap[$field] instanceof DatetimeField)
							{
								if (isset($mask))
								{
									if (!is_array ($value))
									{
										$equating = ' '.$mask.' ';
									} elseif ($mask == "!")
									{
										$equating = ' NOT IN ';
									} else
									{
										throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
									}
								} else
								{
									if (!is_array ($value))
									{
										$equating = " = ";
									} else
									{
										$equating = ' IN ';
									}
								}
								$bEquating_str = TRUE;
							} elseif ($arMap[$field] instanceof EnumField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " = ";
								}
								//TODO: Доделать
							} elseif ($arMap[$field] instanceof ExpressionField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " = ";
								}
								//TODO: Доделать
							} elseif ($arMap[$field] instanceof FloatField)
							{
								if (isset($mask))
								{
									if (!is_array ($value))
									{
										$equating = ' '.$mask.' ';
									} elseif ($mask == "!")
									{
										$equating = ' NOT IN ';
									} else
									{
										throw new Exception\ArgumentOutOfRangeException('arMap['.$oldField.']');
									}
								} else
								{
									if (!is_array ($value))
									{
										$equating = " = ";
									} else
									{
										$equating = ' IN ';
									}
								}
							} elseif ($arMap[$field] instanceof ReferenceField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " = ";
								}
								//TODO: Доделать
							} elseif ($arMap[$field] instanceof StringField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " LIKE ";
								}
								$bEquating_str = TRUE;
							} elseif ($arMap[$field] instanceof TextField)
							{
								if (isset($mask))
								{
									$equating = ' '.$mask.' ';
								} else
								{
									$equating = " LIKE ";
								}
								$bEquating_str = TRUE;
							}
						}

						if (!is_array ($value))
						{
							if ($bFirst)
							{
								$sqlWhere .= $helper->wrapFieldQuotes($field).$equating;
								if ($bValueField)
								{
									$sqlWhere .= $helper->wrapFieldQuotes($value);
								}
								else
								{
									if ($bEquating_str)
									{
										$sqlWhere .= "'".$value."'";
									} else
									{
										$sqlWhere .= $value;
									}
								}
								$bFirst = FALSE;
							} else
							{
								$sqlWhere .= ' '.$this->getFilterLogic ()."\n\t"
									.$helper->wrapFieldQuotes($field).$equating;
								if ($bValueField)
								{
									$sqlWhere .= $helper->wrapFieldQuotes($value);
								}
								else
								{
									if ($bEquating_str)
									{
										$sqlWhere .= "'".$value."'";
									} else
									{
										$sqlWhere .= $value;
									}
								}
							}
						} else
						{
							//TODO: Посмотреть, как ведет себя, если значение это массив
							if ($bFirst)
							{
								$sqlWhere .= $helper->wrapFieldQuotes($field).$equating;
								$sqlWhere .= '(';
								$bFFirst = TRUE;
								for ($i = 0; $i < count ($value); $i++)
								{
									if ($bFFirst)
									{
										$bFFirst = FALSE;
									} else
									{
										$sqlWhere .= ', ';
									}
									if ($bEquating_str)
									{
										$sqlWhere .= "'".$value[$i]."'";
									} else
									{
										$sqlWhere .= $value[$i];
									}
								}
								$sqlWhere .= ')';
								$bFirst = FALSE;
							} else
							{
								$sqlWhere .= ' '.$this->getFilterLogic ()."\n\t"
									.$helper->wrapFieldQuotes($field).$equating;
								$sqlWhere .= '(';
								$bFFirst = TRUE;
								for ($i = 0; $i < count ($value); $i++)
								{
									if ($bFFirst)
									{
										$bFFirst = FALSE;
									} else
									{
										$sqlWhere .= ', ';
									}
									if ($bEquating_str)
									{
										$sqlWhere .= "'".$value[$i]."'";
									} else
									{
										$sqlWhere .= $value[$i];
									}
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

	/**
	 * Собирает блок GROUP SQL запроса
	 *
	 * @return string
	 */
	private function CreateSqlGroup ()
	{
		$sqlGroup = "";
		$helper = new Lib\SqlHelper();
		$arGroup = $this->getGroup();
		if (!empty($arGroup))
		{
			//TODO: Доделать (вспомнить бы только, что доделать)
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

	/**
	 * Собирает блок ORDER SQL запроса
	 *
	 * @return string
	 */
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

	/**
	 * Собирает блок LIMIT SQL запроса
	 *
	 * @return string
	 */
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

	/**
	 * Создает SQL запрос типа "select"
	 *
	 * @return string
	 */
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
	 * Создает SQL запрос типа "create"
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
		$sql .= "\n\t) ENGINE=InnoDB CHARACTER SET=utf8 COMMENT=\"".$this->getTableTitle()."\" AUTO_INCREMENT=".$this->getAutoIncrement()." ;";

		return $sql;
	}

	/**
	 * Создает SQL запрос типа "insert"
	 *
	 * @return string
	 */
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
						$fieldClassName = $obMap->getClassName();
						//$arValue[$fieldName] = $obMap->saveDataModification($arValue[$fieldName]);
						$arValue[$fieldName] = $fieldClassName::saveDataModification($arValue[$fieldName],$obMap);
						//$sqlValues .= $arValue[$fieldName];
						$sqlValues .= "'".$arValue[$fieldName]."'";
						$sqlNames .= $helper->wrapQuotes($columnName);
					}
					else
					{
						$fieldClassName = $obMap->getClassName();
						//$arValue[$fieldName] = $obMap->saveDataModification($arValue[$fieldName]);
						$arValue[$fieldName] = $fieldClassName::saveDataModification($arValue[$fieldName],$obMap);
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
							$fieldClassName = $obMap->getClassName();
							//$value = $obMap->saveDataModification($value);
							$value = $fieldClassName::saveDataModification($value,$obMap);
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

	/**
	 * Создает SQL запрос типа "update"
	 *
	 * @return string
	 */
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

					$fieldClassName = $arMap[$field]->getClassName();
					//$value = $arMap[$field]->saveDataModification($value);
					$value = $fieldClassName::saveDataModification($value,$arMap[$field]);
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

	/**
	 * Создает SQL запрос типа "delete"
	 *
	 * @return bool|string
	 */
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
			$this->sqlMassDelete();
			return false;
		}
		else
		{
			$bCanDelete = $this->checkCanDelete();

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
	/**
	 * Функция массового удаления записи и всех связанных с ней записей
	 *
	 */
	private function sqlMassDelete ()
	{
		$helper = new Lib\SqlHelper();
		$arMap = $this->getTableMap();
		$primaryId = $this->getDeletePrimary();
		$arTableLinks = $this->getTableLinks();
		$tableName = $this->getTableName();

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
	/**
	 * Функция проверки возможности удаления записи. Проверяет нет ли записей, связанных с удаляемой
	 *
	 * @return bool
	 */
	private function checkCanDelete()
	{
		$primaryId = $this->getDeletePrimary();
		$arTableLinks = $this->getTableLinks();
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

	/**
	 * Функция разбирает маску поля на действие и название поля
	 *
	 * @param string $field
	 *
	 * @return array|bool
	 */
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