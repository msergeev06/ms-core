<?php

namespace MSergeev\Core\Lib;

class YandexMap
{
	private static $staticMapService = 'https://static-maps.yandex.ru/1.x/?';

	public static function showImg ($arParams=array())
	{
		if (empty($arParams))
		{
			return '';
		}

		$img = '<img src="'.self::$staticMapService;
		$imgWidth=450;
		$imgHeight=450;
		if (!isset($arParams['l']) || ($arParams['l']!='sat' && $arParams['l']!='sat,skl'))
		{
			$img.= 'l=map';
		}
		else
		{
			$img.= 'l='.$arParams['l'];
		}
		if (isset($arParams['ll']))
		{
			$img.='&ll='.$arParams['ll'];
		}
		if (isset($arParams['spn']))
		{
			$img.='&spn='.$arParams['spn'];
		}
		if (isset($arParams['z']))
		{
			$img.='&z='.$arParams['z'];
		}
		if (isset($arParams['size']))
		{
			$img.='&size='.$arParams['size'];
			list($imgWidth,$imgHeight) = explode(',',$arParams['size']);
		}
		if (isset($arParams['scale']))
		{
			$img.='&scale='.$arParams['scale'];
		}
		if (isset($arParams['pt']))
		{
			$img.='&pt='.$arParams['pt'];
		}
		if (isset($arParams['pl']))
		{
			$img.='&pl='.$arParams['pl'];
		}
		if (isset($arParams['lang']))
		{
			$img.='&lang='.$arParams['lang'];
		}
		if (isset($arParams['key']))
		{
			$img.='&key='.$arParams['key'];
		}

		$img.= '" width="'.$imgWidth.'" height="'.$imgHeight.'">';

		return $img;
	}
}