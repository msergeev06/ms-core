<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Users
{
	private $userID = 0;
	private $userSessionID = "qwerty";
	protected $isAutorised = false;
	protected $userIsAdmin = false;
	protected $userIsGuest = true;

	public function __construct ()
	{
		self::setID(1);
	}

	public function getID ()
	{
		return $this->userID;
	}

	public function getSessionID ()
	{
		return $this->userSessionID;
	}

	public function isAdmin ()
	{
		return ($this->userIsAdmin && $this->isAutorised);
	}

	public function isUserAdmin ()
	{
		return $this->userIsAdmin;
	}

	public function isGuest()
	{
		return $this->userIsGuest;
	}

	public function isAutorised ()
	{
		return $this->isAutorised;
	}

	private function setID ($USER_ID)
	{
		$this->userID = $USER_ID;
	}
}