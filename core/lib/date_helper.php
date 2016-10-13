<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class DateHelper
{
	public function __construct ()
	{

	}

	public function convertDateFromDB ($date=null)
	{
		try
		{
			if (is_null($date))
			{
				throw new Exception\ArgumentNullException('date');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		list($year,$month,$day) = explode("-",$date);
		$time = mktime(0,0,0,intval($month),intval($day),intval($year));

		return date('d.m.Y',$time);
	}

	public function convertDateToDB ($date=null)
	{
		try
		{
			if (is_null($date))
			{
				throw new Exception\ArgumentNullException('date');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		list($day,$month,$year) = explode(".",$date);
		$time = mktime(0,0,0,intval($month),intval($day),intval($year));

		return date('Y-m-d',$time);
	}

	public function strToTime ($time=null, $str='+1 day', $type='site')
	{
		if (is_null($time))
		{
			if ($type === 'site')
			{
				$time = date('d.m.Y');
			}
			elseif ($type === 'db')
			{
				$time = date('Y-m-d');
			}
			elseif ($type === 'time')
			{
				$time = time();
			}
		}

		if ($type === 'site')
		{
			list($day,$month,$year)=explode('.',$time);
			$time = mktime(0,0,0,intval($month),intval($day),intval($year));
			$time = strtotime($str,$time);
			return date('d.m.Y',$time);
		}
		elseif ($type === 'db')
		{
			list($year,$month,$day)=explode('-',$time);
			$time = mktime(0,0,0,intval($month),intval($day),intval($year));
			$time = strtotime($str,$time);
			return date('Y-m-d',$time);
		}
		elseif ($type === 'time')
		{
			return strtotime($str,$time);
		}
		else
		{
			return false;
		}

	}

	public function getDateTimestamp ($date=null)
	{
		try
		{
			if (is_null($date))
			{
				throw new Exception\ArgumentNullException('date');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		list($day,$month,$year) = explode(".",$date);

		return mktime(0,0,0,intval($month),intval($day),intval($year));
	}

	public function getDayOfWeekFromDate($date=null)
	{
		try
		{
			if (is_null($date))
			{
				throw new Exception\ArgumentNullException('date');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		//list($day,$month,$year) = explode('.',$date);
		//$mktime = mktime(0,0,0,intval($month),intval($day),intval($year));
		return intval(date('w',$this->getDateTimestamp($date)));
	}

	public static function getShortNameDayOfWeek ($day=null)
	{
		try
		{
			if (is_null($day))
			{
				throw new Exception\ArgumentNullException('day');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		switch (intval($day))
		{
			case 0:
				return 'Вс.';
			case 1:
				return 'Пн.';
			case 2:
				return 'Вт.';
			case 3:
				return 'Ср.';
			case 4:
				return 'Чт.';
			case 5:
				return 'Пт.';
			case 6:
				return 'Сб.';
			default:
				return false;
		}
	}

	public static function getNameDayOfWeek ($day=null)
	{
		try
		{
			if (is_null($day))
			{
				throw new Exception\ArgumentNullException('day');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		switch (intval($day))
		{
			case 0:
				return 'Воскресенье';
			case 1:
				return 'Понедельник';
			case 2:
				return 'Вторник';
			case 3:
				return 'Среда';
			case 4:
				return 'Четверг';
			case 5:
				return 'Пятница';
			case 6:
				return 'Суббота';
			default:
				return false;
		}
	}

	public static function getNameMonth ($month=null)
	{
		try
		{
			if (is_null($month))
			{
				throw new Exception\ArgumentNullException('month');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		switch ($month)
		{
			case 1:
				return 'Январь';
			case 2:
				return 'Февраль';
			case 3:
				return 'Март';
			case 4:
				return 'Апрель';
			case 5:
				return 'Май';
			case 6:
				return 'Июнь';
			case 7:
				return 'Июль';
			case 8:
				return 'Август';
			case 9:
				return 'Сентябрь';
			case 10:
				return 'Октябрь';
			case 11:
				return 'Ноябрь';
			case 12:
				return 'Декабрь';
			default:
				return FALSE;
		}
	}

	public static function showDaysRus ($day=null)
	{
		try
		{
			if(is_null($day))
			{
				throw new Exception\ArgumentNullException('day');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
		}

		$dayRus = array(
			0 => 'дней',
			1 => 'день',
			2 => 'дня',
		);

/*		0 дней
		1 день
		2 дня
		3 дня
		4 дня
		5 дней
		6 дней
		7 дней
		8 дней
		9 дней
		10 дней +
		11 дней -
		12 дней -
		13 дней -
		14 дней -
		15 дней +
		16 дней +
		17 дней +
		18 дней +
		19 дней +
		20 дней +
		21 день +.*/
		$dayNum = (($day/10)-floor($day/10))*10;
		if ($day == 1)
		{
			return $dayRus[1];
		}
		elseif ($day >= 2 && $day <= 4)
		{
			return $dayRus[2];
		}
		elseif ($day >= 11 && $day <= 14)
		{
			return $dayRus[0];
		}
		else
		{
			if ($dayNum == 0 || ($dayNum >= 5 && $dayNum <= 9))
			{
				return $dayRus[0];
			}
			elseif ($dayNum == 1)
			{
				return $dayRus[1];
			}
			else
			{
				return $dayRus[2];
			}
		}
	}

	public static function checkDate ($date)
	{
		$arData = array();
		if (strpos($date,'.') !== false)
		{
			$arData = explode('.',$date);
			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif (strpos($date,'-') !== false)
		{
			$arData = explode('-',$date);
			if (
				(intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[0]) >= 1970 && intval($arData[0]) <= 9999)
			)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public static function validateDate ($date)
	{
		$value = "";
		if (strpos($date,'.') !== false)
		{
			$arData = explode('.',$date);
			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				$bFirst = true;
				for ($i=0; $i<3; $i++) {
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$value .= ".";
					}
					if (intval($arData[$i]) >= 1 && intval($arData[$i])<=9)
					{
						$value .= "0";
					}
					$value .= intval($arData[$i]);
				}
			}
			else
			{
				return false;
			}
		}
		elseif (strpos($date,'-') !== false)
		{
			$arData = explode('-',$date);
			if (
				(intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[0]) >= 1970 && intval($arData[0]) <= 9999)
			)
			{
				$bFirst = true;
				for ($i=0; $i<3; $i++) {
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$value .= "-";
					}
					if (intval($arData[$i]) >= 1 && intval($arData[$i])<=9)
					{
						$value .= "0";
					}
					$value .= intval($arData[$i]);
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		return $value;
	}
}