<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class DatetimeField extends DateField {
	public function __construct($name, $parameters = array())
	{
		ScalarField::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'datetime';
	}

	public function saveDataModification ($value)
	{
		//msDebug($value);
		list($date,$time) = explode(' ',$value);
		$date = DateField::saveDataModification($date);
		$time = TimeField::saveDataModification($time);
		$value = $date.' '.$time;

		//msDebug($value);
		return $value;
	}

	public function fetchDataModification ($value)
	{
		list($date,$time) = explode(' ',$value);
		$date = DateField::fetchDataModification($date);
		$time = TimeField::fetchDataModification($time);
		$value = $date.' '.$time;

		return $value;
	}
}