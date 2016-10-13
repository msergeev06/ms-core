<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;
use MSergeev\Core\Entity\Query;
use \MSergeev\Core\Tables;

class Options {

	protected static $arOptions;

	public static function init () {
        include_once(Config::getConfig('CORE_ROOT')."default_options.php");

		foreach ($arDefaultOptions as $option => $value) {
			static::$arOptions[$option] = $value;
		}
	}

	public static function getOptionStr ($optionName) {
		$optionName = strtoupper($optionName);

		return static::getOption ($optionName);
	}


	public static function getOptionInt($optionName) {
		$optionName = strtoupper($optionName);

		return intval(static::getOption($optionName));
	}

	public static function getOptionFloat($optionName) {
		$optionName = strtoupper($optionName);

		return floatval(static::getOption($optionName));
	}

	public static function setPackageDefaultOption ($optionName, $optionValue)
	{
		static::$arOptions[$optionName] = $optionValue;
	}

	public static function setOption ($optionName, $optionValue)
	{
		$optionName = strtoupper($optionName);
		if (!isset(static::$arOptions[$optionName]))
		{
			$arInsert = array(
				'NAME' => $optionName,
				'VALUE' => $optionValue
			);
			$result = Tables\OptionsTable::getList(array(
				"filter" => array(
					"NAME" => $optionName
				)
			));
			if ($result)
			{
				$query = new Query('update');
				$query->setUpdateParams(
					$arInsert,
					$result[0]['ID'],
					Tables\OptionsTable::getTableName(),
					Tables\OptionsTable::getMapArray()
				);
				$res = $query->exec();
				if ($res->getResult())
				{
					static::$arOptions[$optionName] = $optionValue;
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$query = new Query('insert');
				$query->setInsertParams(
					$arInsert,
					Tables\OptionsTable::getTableName(),
					Tables\OptionsTable::getMapArray()
				);
				$res = $query->exec();
				if ($res->getResult())
				{
					static::$arOptions[$optionName] = $optionValue;
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return true;
		}
	}

	protected static function getOption ($optionName)
	{
		$optionName = strtoupper($optionName);
		if (isset(static::$arOptions[$optionName])) {
			return static::$arOptions[$optionName];
		}
		else {
			$result = Tables\OptionsTable::getList(array(
				"filter" => array(
					"NAME" => $optionName
				)
			));
			if (!empty($result))
			{
				return $result[0]['VALUE'];
			}
			else
			{
				return false;
			}
		}

	}


}