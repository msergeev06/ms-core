<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class SqlHelperDate extends SqlHelper
{
	function __construct ()
	{
		parent::__construct();
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

		list($year,$month,$day) = explode('-',$date);
		$day = intval($day);
		$month = intval($month);
		$year = intval($year);
		if ($day >= 1 && $day <= 9)
		{
			$day = (string) '0'.$day;
		}
		if ($month >= 1 && $month <= 9)
		{
			$month = (string) '0'.$month;
		}

		return $day.".".$month.".".$year;
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

		list($day,$month,$year)=explode('.',$date);
		$day = intval($day);
		$month = intval($month);
		$year = intval($year);
		if ($day >= 1 && $day <= 9)
		{
			$day = (string) '0'.$day;
		}
		if ($month >= 1 && $month <= 9)
		{
			$month = (string) '0'.$month;
		}

		return $year."-".$month."-".$day;
	}

	//https://msdn.microsoft.com/ru-ru/library/ms186724%28v=sql.120%29.aspx
	// 1. Функции, получающие значения системной даты и времени

	// Возвращает значение типа datetime2(7), которое содержит дату и время компьютера, на котором запущен
	// экземпляр SQL Server. Смещение часового пояса не включается.
	public function getSysDateTimeFunction ()
	{
		return 'SYSDATETIME ()';
	}

	// Возвращает значение типа datetimeoffset(7), которое содержит дату и время компьютера, на котором запущен
	// экземпляр SQL Server. Смещение часового пояса включается.
	public function getSysDateTimeOffsetFunction ()
	{
		return 'SYSDATETIMEOFFSET()';
	}

	// Возвращает значение типа datetime2(7), которое содержит дату и время компьютера, на котором запущен
	// экземпляр SQL Server. Возвращаемые дата и время отображаются в формате UTC.
	public function getSysUTCDateTimeFunction ()
	{
		return 'SYSUTCDATETIME()';
	}

	// 2. Функции системной даты и времени меньшей точности

	// Возвращает текущую системную отметку времени базы данных в виде значения datetime без смещения часового
	// пояса базы данных. Это значение наследуется от операционной системы компьютера, на котором работает
	// экземпляр SQL Server.
	public function getCurrentTimestampFunction ()
	{
		return 'CURRENT_TIMESTAMP';
	}

	// Возвращает значение типа datetime, которое содержит дату и время компьютера, на котором запущен
	// экземпляр SQL Server. Смещение часового пояса не включается.
	public function getGetDateFunction ()
	{
		return 'GETDATE()';
	}

	// Возвращает значение типа datetime, которое содержит дату и время компьютера, на котором запущен
	// экземпляр SQL Server. Возвращаемые дата и время отображаются в формате UTC.
	public function getGetUTCDateFunction ()
	{
		return 'GETUTCDATE()';
	}

	// 3. Функции, получающие компоненты даты и времени

	// Возвращает символьную строку, представляющую указанный компонент datepart указанной date.
	// https://msdn.microsoft.com/ru-ru/library/ms174395%28v=sql.120%29.aspx
	public function getDateNameFunction ($datepart, $date)
	{
		return "DATENAME(".$datepart.", '".$date."')";
	}

	// Возвращает целое число, представляющее указанный компонент datepart указанной даты date.
	public function getDatePartFunction ($datepart, $date)
	{
		return "DATEPART (".$datepart.", '".$date."')";
	}

	// Возвращает целое число, представляющее день указанной даты date.
	public function getDayFunction ($date)
	{
		return "DAY('".$date."')";
	}

	// Возвращает целое число, представляющее месяц указанной даты date.
	public function getMonthFunction ($date)
	{
		return "MONTH('".$date."')";
	}

	// Возвращает целое число, представляющее год указанной даты date.
	public function getYearFunction ($date)
	{
		return "YEAR('".$date."')";
	}

	// 4. Функции, получающие значения даты и времени из их компонентов

	//Возвращает значение date, соответствующее указанному числу, месяцу и году.
	public function getDateFromPartsFunction ($day=1, $month=1, $year=1)
	{
		return 'DATEFROMPARTS('.$year.', '.$month.', '.$day.')';
	}

	//Возвращает значение datetime2, соответствующее указанной дате и времени с заданной точностью.
	public function getDateTime2FromPartsFunction ($year=1, $month=1, $day=1, $hour=0, $minute=0,
	                                               $seconds=0, $fractions=0, $precision=0)
	{
		return 'DATETIME2FROMPARTS('.$year.', '.$month.', '.$day.', '.$hour.', '.$minute.', '.$seconds.', '
		.$fractions.', '.$precision.')';
	}

	//Возвращает значение datetime, соответствующее указанной дате и времени.
	public function getDateTimeFromPartsFunction ($year=1753, $month=1, $day=1, $hour=0, $minute=0, $seconds=0, $milliseconds=0)
	{
		if ($year<1753) $year=1753;
		return 'DATETIMEFROMPARTS('.$year.', '.$month.', '.$day.', '.$hour.', '.$minute.', '.$seconds.', '
		.$milliseconds.')';
	}

	//Возвращает значение datetimeoffset для указанных даты и времени с указанными смещением и точностью.
	public function getDateTimeOffsetFromPartsFunction ($year=1, $month=1, $day=1, $hour=0, $minute=0, $seconds=0,
	                                                    $fractions=0, $hour_offset=0, $minute_offset=0, $precision=0)
	{
		return 'DATETIMEOFFSETFROMPARTS('.$year.', '.$month.', '.$day.', '.$hour.', '.$minute.', '.$seconds.', '
		.$fractions.', '.$hour_offset.', '.$minute_offset.', '.$precision.')';
	}

	//Возвращает значение smalldatetime, соответствующее указанной дате и времени.
	public function getSmallDateTimeFromPartsFunction ($year=1900, $month=1, $day=1, $hour=0, $minute=0)
	{
		return 'SMALLDATETIMEFROMPARTS('.$year.', '.$month.', '.$day.', '.$hour.', '.$minute.')';
	}

	//Возвращает значение time, соответствующее указанному времени с установленной точностью.
	public function getTimeFromPartsFunction ($hour=0, $minute=0, $seconds=0, $fractions=0, $precision=0)
	{
		return 'TIMEFROMPARTS('.$hour.', '.$minute.', '.$seconds.', '.$fractions.', '.$precision.')';
	}

	// 5. Функции, получающие разность даты и времени

	//Возвращает количество пересеченных границ (целое число со знаком), указанных аргументом datepart,
	//за период времени, указанный аргументами startdate и enddate.
	//https://msdn.microsoft.com/ru-ru/library/ms189794%28v=sql.120%29.aspx
	public function getDateDiffFunction ($datepart, $startdate, $enddate)
	{
		return "DATEDIFF(".$datepart.", '".$startdate."', '".$enddate."')";
	}

	// 6. Функции, изменяющие значения даты и времени

	// Возвращает новое значение datetime, добавляя интервал к указанной части datepart заданной даты date.
	public function getDateAddFunction ($datepart, $number, $date)
	{
		return "DATEADD(".$datepart.", ".$number." , '".$date."')";
	}

	// Возвращает последний день месяца, содержащего указанную дату, с необязательным смещением.
	public function getEoMonthFunction ($start_date, $month_to_add=null)
	{
		$return = "EOMONTH('".$start_date."'";
		if (!is_null($month_to_add)) $return .= ", ".$month_to_add;
		$return .= ")";

		return $return;
	}

	// SWITCHOFFSET изменяет смещение часового пояса для значения DATETIMEOFFSET и сохраняет значение UTC.
	public function getSwitchOffsetFunction ($dateTimeOffset, $time_zone)
	{
		return "SWITCHOFFSET (".$dateTimeOffset.", ".$time_zone.")";
	}

	// TODATETIMEOFFSET преобразует значение типа datetime2 в значение типа datetimeoffset. Значение datetime2
	// преобразуется в местное время для указанного time_zone.
	public function getToDateTimeOffsetFunction ($expression, $time_zone)
	{
		return "TODATETIMEOFFSET (".$expression.", ".$time_zone.")";
	}

	// 7. Функции, проверяющие значения даты и времени

	// Определяет, является ли входное выражение типа datetime или smalldatetime допустимым значением даты или времени.
	public function getIsDateFunction ($expression)
	{
		return "ISDATE (".$expression.")";
	}

}