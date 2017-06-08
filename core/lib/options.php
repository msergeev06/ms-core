<?php
/**
 * MSergeev\Core\Lib\Options
 * Опции ядра и пакетов.
 * Используется для хранения и получения различных опций ядра и установленных пакетов
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;
use MSergeev\Core\Entity\Query;
use \MSergeev\Core\Tables;

class Options
{
	/**
	 * @var array Массив всех известных в данной сессии опций
	 */
	protected static $arOptions;

	/**
	 * Инициализация пакета. Загружает данные из файла ядра default_options.php
	 *
	 * @api
	 */
	public static function init ()
	{
		$arDefaultOptions = array();
		include_once(Config::getConfig('CORE_ROOT')."default_options.php");

		foreach ($arDefaultOptions as $option => $value) {
			self::$arOptions[$option] = $value;
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде строки
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 *
	 * @return bool|string Значение указанной опции, либо false
	 */
	public static function getOptionStr ($optionName) {
		$optionName = strtoupper($optionName);

		return self::getOption ($optionName);
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде целого числа
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 *
	 * @return bool|int Целочисленное значение указанной опции, либо false
	 */
	public static function getOptionInt($optionName) {
		$optionName = strtoupper($optionName);

		if ($optionVal = self::getOption($optionName))
		{
			return intval($optionVal);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде вещественного числа
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 *
	 * @return bool|float Вещественное значение указанной опции, либо false
	 */
	public static function getOptionFloat($optionName) {
		$optionName = strtoupper($optionName);

		return floatval(self::getOption($optionName));
	}

	/**
	 * Функция добавляющая опщии по-умолчанию, без записи новых в DB
	 *
	 * @api
	 *
	 * @param string $optionName  Название опции
	 * @param mixed  $optionValue Значение опции
	 */
	public static function setDefaultOption ($optionName, $optionValue)
	{
		self::$arOptions[$optionName] = $optionValue;
	}

	/**
	 * Функция добавляет новые опции в базу данных и в текущую сессию
	 *
	 * @api
	 *
	 * @param string $optionName  Название опции
	 * @param mixed  $optionValue Значение опции
	 *
	 * @return bool true - опция сохранена, false - ошибка сохранения
	 */
	public static function setOption ($optionName, $optionValue)
	{
		$optionName = strtoupper($optionName);
		if (
			!isset(self::$arOptions[$optionName])
			|| self::$arOptions[$optionName] != $optionValue
		)
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
					self::$arOptions[$optionName] = $optionValue;
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
					self::$arOptions[$optionName] = $optionValue;
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

	/**
	 * Функция возвращает значение опции либо из массива,
	 * либо из базы данных, сохранив в массиве
	 *
	 * @ignore
	 *
	 * @param string $optionName Имя опции
	 *
	 * @return bool|mixed Значение опции, либо false
	 */
	protected static function getOption ($optionName)
	{
		$optionName = strtoupper($optionName);
		if (isset(self::$arOptions[$optionName])) {
			return self::$arOptions[$optionName];
		}
		else {
			$result = Tables\OptionsTable::getList(array(
				"filter" => array(
					"NAME" => $optionName
				)
			));
			if (!empty($result))
			{
				self::$arOptions[$optionName] = $result[0]['VALUE'];
				return $result[0]['VALUE'];
			}
			else
			{
				return false;
			}
		}

	}

}