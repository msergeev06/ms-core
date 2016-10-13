<?php
/**
 * MSergeev\Core\Lib
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;

class DataBase {

	protected $host;
	protected $base;
	protected $user;
	protected $pass;
	protected $db_conn;

	function __construct () {

		$this->host = Config::getConfig('DB_HOST');
		$this->base = Config::getConfig('DB_NAME');
		$this->user = Config::getConfig('DB_USER');
		$this->pass = Config::getConfig('DB_PASS');

		$this->db_conn = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->base, $this->db_conn);
	}

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