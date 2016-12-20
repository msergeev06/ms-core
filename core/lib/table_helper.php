<?php
/**
 * MSergeev\Core\Lib\TableHelper
 * Помощник обработки данных таблиц
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use \MSergeev\Core\Entity;

class TableHelper
{
	public static function primaryField ($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "ID";
		}
		$arResult = array(
			'primary' => true,
			'autocomplete' => true,
			'title' => 'Ключ'
		);
		self::parseParams($arResult,$arParams);

		return new Entity\IntegerField($field_name,$arResult);
	}

	/**
	 * Возвращает сущность Entity\BooleanField для поля таблицы 'ACTIVE' (Активность)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @api
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Entity\BooleanField
	 */
	public static function activeField($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "ACTIVE";
		}
		$arResult = array(
			'required' => true,
			'default_value' => true,
			'title' => 'Активность'
		);
		self::parseParams($arResult,$arParams);

		return new Entity\BooleanField($field_name,$arResult);
	}

	/**
	 * Возвращает сущность Entity\IntegerField для поля таблицы 'SORT' (Сортировка)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @api
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Entity\IntegerField
	 */
	public static function sortField($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "SORT";
		}
		$arResult = array(
			'required' => true,
			'default_value' => 500,
			'title' => 'Сортировка'
		);
		self::parseParams($arResult,$arParams);

		return new Entity\IntegerField($field_name,$arResult);
	}

	/**
	 * Обрабатывает переданные параметры и объединяет с параметрами сущности
	 *
	 * @param array $arResult Массив основных параметров сущности
	 * @param array $arParams Массив дополнительных параметро сущности
	 *
	 * @return array Объединенный массив параметров сущности
	 */
	private static function parseParams (array &$arResult,array $arParams)
	{
		if (isset($arParams['primary']))
		{
			$arResult['primary'] = $arParams['primary'];
			unset($arParams['primary']);
		}
		if (isset($arParams['autocomplete']))
		{
			$arResult['autocomplete'] = $arParams['autocomplete'];
			unset($arParams['autocomplete']);
		}
		if (isset($arParams['required']))
		{
			$arResult['required'] = $arParams['required'];
			unset($arParams['required']);
		}
		if (isset($arParams['default_value']))
		{
			$arResult['default_value'] = $arParams['default_value'];
			unset($arParams['default_value']);
		}
		if (isset($arParams['title']))
		{
			$arResult['title'] = $arParams['title'];
			unset($arParams['title']);
		}
		if (!empty($arParams))
		{
			$arResult = array_merge($arResult,$arParams);
		}
	}
}