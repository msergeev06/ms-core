<?php
/**
 * MSergeev\Core\Entity\User
 * Работа с пользователями
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

use MSergeev\Core\Lib;
use MSergeev\Core\Tables\UsersTable;

class User
{
	private $ADMIN_USER = 1;
	private $GUEST_USER = 2;
	private $REMEMBER_TIME = 31536000; //365 дней

	/**
	 * @var int ID пользователя
	 */
	protected $ID = null;

	/**
	 * @var bool Флаг, говорящий администратор ли текущий пользователь
	 */
	protected $isAdmin = null;

	/**
	 * @var bool Флаг, говорящий гость ли текущий пользователь
	 */
	protected $isGuest = null;

	/**
	 * @var string Проверочная строка при авторизации
	 */
	protected $hash = null;

	protected $arParams = array();

	/**
	 * Конструктор.
	 */
	public function __construct ()
	{
		if (isset($_COOKIE['ms_user_id']) && isset($_COOKIE['ms_hash']))
		{
			$userID = intval($_COOKIE['ms_user_id']);
			$hash = $_COOKIE['ms_hash'];
			$arRes = UsersTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array(
						'ID' => $userID,
						'HASH' => $hash
					)
				)
			);
			if ($arRes)
			{
				$this->ID = $userID;
				$this->hash = $hash;
				$rememberMe = false;
				if (isset($_COOKIE['ms_remember']) && intval($_COOKIE['ms_remember'])>0)
				{
					$rememberMe = true;
				}
				if ($this->ID == $this->ADMIN_USER)
				{
					$this->logInAdmin($rememberMe);
				}
				elseif ($this->ID == $this->GUEST_USER)
				{
					$this->logInGuest();
				}
				else
				{
					$this->logInOther($rememberMe);
				}
			}
			else
			{
				$this->logInGuest();
			}
		}
		else
		{
			$this->logInGuest();
		}
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
		return $this->ID;
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
	public function isAdmin ()
	{
		return $this->isAdmin;
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
		return $this->isGuest;
	}

	/**
	 * Авторизован ли текущий пользователь
	 * true - авторизован
	 * false - требуется авторизация
	 *
	 * @return bool
	 */
	public function isAuthorise ()
	{
		return (isset($_COOKIE['ms_user_id']) && isset($_COOKIE['ms_hash'])
			&& $_COOKIE['ms_user_id'] == $this->ID
			&& $_COOKIE['ms_hash'] == $this->hash
		);
	}

	/**
	 * Авторизует указанного пользователя
	 *
	 * @param int  $userID      ID пользователя
	 * @param bool $rememberMe  Флаг, запомнить авторизацию
	 */
	public function logIn ($userID, $rememberMe=false)
	{
		$this->ID = intval($userID);
		$this->hash = $this->generateRandomString();
		UsersTable::update(intval($userID),array("VALUES"=>array("HASH"=>$this->hash)));
		if ($this->ID == $this->ADMIN_USER)
		{
			$this->logInAdmin($rememberMe);
		}
		elseif ($this->ID == $this->GUEST_USER)
		{   //Авторизация гостя не должна проходить так, но на всякий случай
			$this->logInGuest();
		}
		else
		{
			$this->logInOther($rememberMe);
		}
	}

	/**
	 * Разавторизовать пользователя. Автоматически авторизует гостя
	 */
	public function logOut ()
	{
		if (isset($_COOKIE['ms_user_id']))
		{
			UsersTable::update(intval($_COOKIE['ms_user_id']),array("VALUES"=>array("HASH"=>NULL)));
		}
		$this->arParams = array();
		$this->logInGuest();
	}

	/**
	 * Вовзращает значение констант
	 *
	 * @param $name
	 *
	 * @return int|null
	 */
	public function getConst ($name)
	{
		switch ($name)
		{
			case 'ADMIN_USER':
				return $this->ADMIN_USER;
			case 'GUEST_USER':
				return $this->GUEST_USER;
			case 'REMEMBER_TIME':
				return $this->REMEMBER_TIME;
			default:
				return NULL;
		}
	}

	/**
	 * Генерирует случайную строку для хеша
	 *
	 * @param null|string $prefix
	 *
	 * @return string
	 */
	public function generateRandomString ($prefix=null)
	{
		if (is_null($prefix))
		{
			$prefix = rand();
		}

		return md5(uniqid($prefix, true));
	}

	public function getParam ($strParamName)
	{
		if (isset($this->arParams[$strParamName]))
		{
			return $this->arParams[$strParamName];
		}
		else
		{
			return false;
		}
	}

	public function setParam ($strParamName, $value)
	{
		$this->arParams[$strParamName] = $value;
	}

/*	protected function issetAutorisedUser ()
	{
		return !is_null($this->ID);
	}*/

	protected function logInGuest ()
	{
		$this->ID = $this->GUEST_USER;
		$this->isAdmin = false;
		$this->isGuest = true;
		$this->delCookie();
	}

	protected function logInAdmin ($rememberMe=false)
	{
		$this->isAdmin = true;;
		$this->isGuest = false;
		$this->setCookie($this->ID, $this->hash, $rememberMe);
	}

	protected function logInOther ($rememberMe=false)
	{
		$this->isAdmin = false;
		$this->isGuest = false;
		$this->setCookie($this->ID, $this->hash, $rememberMe);
	}

	public function setUserCookie ($cookieName, $value, $userID=null)
	{
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
		$cookieName = str_replace('ms_','',$cookieName);

		return setcookie('ms_'.$cookieName.'_user_'.$userID,$value,(time()+$this->REMEMBER_TIME),'/');
	}

	public function getUserCookie ($cookieName, $userID=null)
	{
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
		$cookieName = str_replace('ms_','',$cookieName);
		if (isset($_COOKIE['ms_'.$cookieName.'_user_'.$userID]))
		{
			return $_COOKIE['ms_'.$cookieName.'_user_'.$userID];
		}
		else
		{
			return false;
		}
	}

	public function issetUserCookie ($cookieName, $userID=null)
	{
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
		$cookieName = str_replace('ms_','',$cookieName);
		if (isset($_COOKIE['ms_'.$cookieName.'_user_'.$userID]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function setCookie ($userID, $hash, $rememberMe=false)
	{
		if ($rememberMe)
		{
			$time = time() + $this->REMEMBER_TIME;
		}
		else
		{
			$time = 0;
		}
		setcookie('ms_user_id',$userID, $time,'/');
		setcookie('ms_hash',$hash,$time,'/');
		setcookie('ms_remember',$time,$time,'/');
	}

	protected function delCookie ()
	{
		if (isset($_COOKIE['ms_user_id']))
		{
			setcookie('ms_user_id',null,time()-30,'/');
		}
		if (isset($_COOKIE['ms_hash']))
		{
			setcookie('ms_hash',null,time()-30,'/');
		}
		if (isset($_COOKIE['ms_remember']))
		{
			setcookie('ms_remember',null,time()-30,'/');
		}
	}




}