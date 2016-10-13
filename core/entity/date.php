<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class Date
{
	protected
		$timestamp=null;

	public function __construct ($date=null,$type=null)
	{
		if (is_null($type) || ($type != 'db' && $type != 'site'))
		{
			$type = 'time';
		}
		switch ($type)
		{
			case 'time':
				if (is_null($date))
				{
					$this->timestamp = time();
				}
				else
				{
					$this->timestamp = $date;
				}
				break;
			case 'db':
				if (is_null($date))
				{
					$this->timestamp = mktime(0,0,0,intval(date('m')),intval(date('d')),intval(date('Y')));
				}
				else
				{
					list($year,$month,$day)=explode('-',$date);
					$this->timestamp = mktime(0,0,0,intval($month),intval($day),intval($year));
				}
				break;
			case 'site':
				if (is_null($date))
				{
					$this->timestamp = mktime(0,0,0,intval(date('m')),intval(date('d')),intval(date('Y')));
				}
				else
				{
					list($day,$month,$year)=explode('.',$date);
					$this->timestamp = mktime(0,0,0,intval($month),intval($day),intval($year));
				}
				break;
		}
	}

	public function getTimestamp ()
	{
		return $this->timestamp;
	}

	public function getDate ($format="Y-m-d")
	{
		return date($format,$this->getTimestamp());
	}

	public function getDateDB ()
	{
		return date("Y-m-d",$this->getTimestamp());
	}

	public static function getDateTimestamp ($format="Y-m-d", $timestamp=null)
	{
		if (is_null($timestamp)) $timestamp = time();
		return date($format,$timestamp);
	}

	public static function getDateDBTimestamp ($timestamp=null)
	{
		if (is_null($timestamp)) $timestamp = time();
		return date("Y-m-d",$timestamp);
	}
}