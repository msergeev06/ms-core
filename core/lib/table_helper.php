<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use \MSergeev\Core\Entity;

class TableHelper
{
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