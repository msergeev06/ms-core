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
		$arResult = array(
			'required' => true,
			'default_value' => true,
			'title' => 'Активность'
		);
		$arResult = self::parseParams($arResult,$arParams);

		return new Entity\BooleanField('ACTIVE',$arResult);
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
		$arResult = array(
			'required' => true,
			'default_value' => 500,
			'title' => 'Сортировка'
		);
		$arResult = self::parseParams($arResult,$arParams);

		return new Entity\IntegerField('SORT',$arResult);
	}

	/**
	 * Обрабатывает переданные параметры и объединяет с параметрами сущности
	 *
	 * @param array $arResult Массив основных параметров сущности
	 * @param array $arParams Массив дополнительных параметро сущности
	 *
	 * @return array Объединенный массив параметров сущности
	 */
	private static function parseParams (array $arResult,array $arParams)
	{
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

		return $arResult;
	}
}