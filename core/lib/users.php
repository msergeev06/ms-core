<?php
/**
 * MSergeev\Core\Lib\Users
 * Работа с пользователями
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Users
{
	/**
	 * @var int ID пользователя
	 */
	private $userID = 0;

	/**
	 * @var string ID сессии пользователя
	 */
	private $userSessionID = "qwerty";

	/**
	 * @var bool Флаг, говорящий авторизован ли пользователь
	 */
	protected $isAutorised = false;

	/**
	 * @var bool Флаг, говорящий администратор ли текущий пользователь
	 */
	protected $userIsAdmin = false;

	/**
	 * @var bool Флаг, говорящий гость ли текущий пользователь
	 */
	protected $userIsGuest = true;

	/**
	 * Конструктор.
	 */
	public function __construct ()
	{
		self::setID(1);
	}

	/**
	 * Возвращает ID текущего пользователя
	 *
	 * @api
	 *
	 * @return int ID текущего пользователя
	 */
	public function getID ()
	{
		return $this->userID;
	}

	/**
	 * Возвращает ID сессии текущего пользователя
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getSessionID ()
	{
		return $this->userSessionID;
	}

	/**
	 * Администратор ли текущий пользователь и авторизован ли пользователь
	 * true - администратор
	 * false - не администратор
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isAdmin ()
	{
		return ($this->userIsAdmin && $this->isAutorised);
	}

	/**
	 * Администратор ли текущий пользователь
	 * true - администратор
	 * false - не администратор
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isUserAdmin ()
	{
		return $this->userIsAdmin;
	}

	/**
	 * Гость ли текущий пользователь
	 * true - гость
	 * false - не гость
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isGuest()
	{
		return $this->userIsGuest;
	}

	/**
	 * Авторизован ли текущий пользователь
	 * true - авторизован
	 * false - не авторизован
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isAutorised ()
	{
		return $this->isAutorised;
	}

	/**
	 * Устанавливает ID текущего пользователя
	 *
	 * @ignore
	 *
	 * @param $USER_ID
	 */
	private function setID ($USER_ID)
	{
		$this->userID = $USER_ID;
	}
}