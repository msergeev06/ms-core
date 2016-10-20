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
		$db_res = mysql_query($sql, $this->db_conn);

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

}