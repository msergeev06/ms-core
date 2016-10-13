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

class TimeField extends ScalarField
{
	public function __construct ($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'time';
	}

	public function saveDataModification ($value)
	{
		$value = self::validate($value);

		$value = parent::saveDataModification($value);
		return $value;
	}

	public function fetchDataModification ($value)
	{
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


			if (strpos($value,':') !== false)
			{
				$arTime = explode(':',$value);
				for ($i=0; $i<3; $i++)
				{
					$arTime[$i] = intval($arTime[$i]);
				}

				if (
					($arTime[0]>=0 && $arTime[0]<=23)
					&& ($arTime[1]>=0 && $arTime[1]<=59)
					&& ($arTime[2]>=0 && $arTime[2]<=59)
				)
				{
					$value = '';
					$bFirst = true;
					for ($i=0; $i<3; $i++)
					{
						if ($bFirst)
						{
							$bFirst = false;
						}
						else
						{
							$value .= ":";
						}

						if (intval($arTime[$i])>=0 && intval($arTime[$i])<=9)
						{
							$value .= '0'.intval($arTime[$i]);
						}
						else
						{
							$value .= intval($arTime[$i]);
						}
					}

					return $value;
				}
				else
				{
					throw new ArgumentOutOfRangeException('time','00:00:00', '23:59:59');
				}

			}
			else
			{
				throw new ArgumentOutOfRangeException('time');
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

}