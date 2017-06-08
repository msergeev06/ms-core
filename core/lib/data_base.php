<?php
/**
 * MSergeev\Core\Lib\DataBase
 * Осуществляет подключение к базе данных и посылает запросы к базе
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;

class DataBase {

	/**
	 * @var string Hostname Базы данных. Используется для подключения к DB
	 */
	protected $host;

	/**
	 * @var string Имя базы данных. Используется для подключения к DB
	 */
	protected $base;

	/**
	 * @var string Пользователь базы данных. Используется для подключения к DB
	 */
	protected $user;

	/**
	 * @var string Пароль пользователя базы данных. Используется для подключения к DB
	 */
	protected $pass;

	/**
	 * @var resource a MySQL link identifier on success or false on failure
	 */
	protected $db_conn;

	protected $db_queries=0;

	protected $db_query_start=0;

	protected $db_query_stop=0;

	protected $db_last_query_time=0;

	protected $db_all_query_time=0;

	/**
	 * Конструктор. Осуществляет подключение к базе данных и передает начальные параметры подключения
	 *
	 * @api
	 */
	function __construct () {

		$this->host = Config::getConfig('DB_HOST');
		$this->base = Config::getConfig('DB_NAME');
		$this->user = Config::getConfig('DB_USER');
		$this->pass = Config::getConfig('DB_PASS');

		$this->db_conn = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->base, $this->db_conn);
		mysql_set_charset('utf8',$this->db_conn);
	}

	/**
	 * Осуществляет запрос к базе данных, используя данных объекта Query
	 *
	 * @api
	 *
	 * @param Query $obQuery Объект, содержащий SQL запрос
	 *
	 * @return DBResult Результат MYSQL запроса
	 */
	public function query (Query $obQuery)
	{
		$sql = $obQuery->getQueryBuildParts();
		$this->setQueryStart();
		$db_res = mysql_query($sql, $this->db_conn);
		$this->setQueryStop();

		$res = new DBResult($db_res,$obQuery);
		$res->setAffectedRows(mysql_affected_rows($this->db_conn));
		if ($obQuery->getType() == "insert")
		{
			$res->setInsertId(mysql_insert_id($this->db_conn));
		}
		if (!$res->getResult())
		{
			$res->setResultErrorNumber(mysql_errno($this->db_conn));
			$res->setResultErrorText(mysql_error($this->db_conn));
		}

		return $res;
	}

	/**
	 * Возвращает время выполнения последнего SQL запроса
	 *
	 * @return float
	 */
	public function getLastQueryTime ()
	{
		return floatval($this->db_last_query_time);
	}

	/**
	 * Возвращает общее время всех SQL запросов
	 *
	 * @return float
	 */
	public function getAllQueryTime ()
	{
		return floatval($this->db_all_query_time);
	}

	/**
	 * Возвращает общее количество выполненных SQL запросов
	 *
	 * @return int
	 */
	public function getCountQuery ()
	{
		return intval($this->db_queries);
	}

	public function getDumpCommand ($path,$postfix=null,$package=null,$arTables=array(),$useGz=true,$pastDate=true,$noData=false,$dbName=null,$dbUser=null,$dbPass=null)
	{
		$comm = 'mysqldump ';
		//$comm .= '-Q -c -e ';
		if ($noData === true)
		{
			$comm .= '--no-data ';
		}
		if (is_null($dbName))
		{
			$dbName = $this->base;
		}
		if (is_null($dbUser))
		{
			$dbUser = $this->user;
		}
		if (is_null($dbPass))
		{
			$dbPass = $this->pass;
		}
		$comm .= "-u".$dbUser." -p".$dbPass." ".$dbName." ";
		if (!empty($arTables))
		{
			foreach ($arTables as $tableName)
			{
				$comm .= $tableName.' ';
			}
		}
		if ($useGz === true)
		{
			$comm .= '| gzip ';
		}
		$comm .= '> ';
/*		if ($pastDate === true)
		{
			$comm .= '`date +';
		}*/
		$comm .= $path.'dump_'.$dbName;
		if (!is_null($package))
		{
			$comm .= '_'.$package;
		}
		if (!empty($arTables))
		{
			$comm .= '_tables';
		}
		if (!is_null($postfix) && $postfix !== false)
		{
			$comm .= '_'.$postfix;
		}
		if ($pastDate === true)
		{
			//$comm .= '.%Y%m%d.%H%M%S';
			$comm .= date('.Ymd.His');
		}
		$comm .= '.sql';
		if ($useGz === true)
		{
			$comm .= '.gz';
		}

		return $comm;
	}

	/**
	 * Устанавливает начальное время SQL запроса и увеличивает общее количество запросов на 1
	 */
	private function setQueryStart ()
	{
		$this->db_query_start = microtime (true);
		$this->db_queries++;
	}

	/**
	 * Устанавливает конечное время SQL запроса, вычисляет время выполнения последнего запроса и время выполнения всех запросов
	 */
	private function setQueryStop ()
	{
		$this->db_query_stop = microtime(true);

		$this->db_last_query_time = $this->db_query_stop - $this->db_query_start;

		$this->db_all_query_time += $this->db_last_query_time;
	}


}