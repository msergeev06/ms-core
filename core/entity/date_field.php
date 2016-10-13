<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

use MSergeev\Core\Exception\ArgumentNullException;
use MSergeev\Core\Exception\ArgumentOutOfRangeException;

class DateField extends ScalarField {

	public function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'date';
	}

	public function saveDataModification ($value)
	{
		$value = self::validate($value);
		list($arDate['day'],$arDate['month'],$arDate['year']) = explode(".",$value);
		$time = mktime(0,0,0,intval($arDate['month']),intval($arDate['day']),intval($arDate['year']));
		$value = date('Y-m-d',$time);

		$value = parent::saveDataModification($value);
		return $value;
	}

	public function fetchDataModification ($value)
	{
		list($arDate['year'],$arDate['month'],$arDate['day']) = explode("-",$value);
		$time = mktime(0,0,0,intval($arDate['month']),intval($arDate['day']),intval($arDate['year']));
		$value = date('d.m.Y',$time);

		$value = parent::fetchDataModification ($value);
		return $value;
	}

	public function validate ($value=null)
	{
		try
		{
			if(is_null($value))
			{
				throw new ArgumentNullException('date');
			}

			if (strlen($value)<7 || strlen($value)>10)
			{
				throw new ArgumentOutOfRangeException('date');
			}


			$arData = array();
			if (strpos($value,'.') !== false)
			{
				$arData = explode('.',$value);
			}
			elseif (strpos($value,'-') !== false)
			{
				$arData = explode('-',$value);
			}
			else
			{
				throw new ArgumentOutOfRangeException('data');
			}

			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				$value = '';
				if (intval($arData[0]) >= 1 && intval($arData[0])<=9)
				{
					$value.='0'.intval($arData[0]).'.';
				}
				else
				{
					$value.= intval($arData[0]).'.';
				}
				if (intval($arData[1]) >= 1 && intval($arData[1])<=9)
				{
					$value.='0'.intval($arData[1]).'.';
				}
				else
				{
					$value.= intval($arData[1]).'.';
				}
				$value.=intval($arData[2]);

				return $value;
			}
			else
			{
				throw new ArgumentOutOfRangeException('data','01.01.1970','31.12.9999');
			}
		}
		catch (ArgumentNullException $e)
		{
			$e->showException();
		}
		catch (ArgumentOutOfRangeException $e2)
		{
			$e2->showException();
		}
	}


	/*
	public function getValidators()
	{
		$validators = parent::getValidators();

		if ($this->validation === null)
		{
			$validators[] = new Validator\Date;
		}

		return $validators;
	}

	public function assureValueObject($value)
	{
		if ($value instanceof Type\DateTime)
		{
			// oracle sql helper returns datetime instead of date - it doesn't see the difference
			$value = new Type\Date(
				$value->format(Main\UserFieldTable::MULTIPLE_DATE_FORMAT),
				Main\UserFieldTable::MULTIPLE_DATE_FORMAT
			);
		}

		return $value;
	}
	*/

}