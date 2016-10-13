<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class Tools {
	public static function generateCode ()
	{
		$text = func_get_arg (0);
		$code = "";
		$text = iconv("utf-8","windows-1251",$text);
		$array = str_split($text);

		$lastChar = "";
		for ($i=0; $i<count($array); $i++)
		{
			$array[$i] = iconv("windows-1251","utf-8",$array[$i]);
			$char = static::convertRusToLat($array[$i]);
			if ($char == "_" && $char != $lastChar)
			{
				$code .= $char;
				$lastChar = $char;
			}
			elseif ($char != "_")
			{
				$code .= $char;
				$lastChar = $char;
			}
		}
		$code = strtolower($code);

		return $code;
	}

	public static function convertRusToLat ($char)
	{
		switch ($char)
		{
			case 'А':
				return 'A';
			case 'Б':
				return 'B';
			case 'В':
				return 'V';
			case 'Г':
				return 'G';
			case 'Д':
				return 'D';
			case 'Е':
			case 'Ё':
				return 'E';
			case 'Ж':
				return 'J';
			case 'З':
				return 'Z';
			case 'И':
			case 'Й':
			case 'Ы':
				return 'I';
			case 'К':
				return 'K';
			case 'Л':
				return 'L';
			case 'М':
				return 'M';
			case 'Н':
				return 'N';
			case 'О':
				return 'O';
			case 'П':
				return 'P';
			case 'Р':
				return 'R';
			case 'С':
				return 'S';
			case 'Т':
				return 'T';
			case 'У':
				return 'U';
			case 'Ф':
				return 'F';
			case 'Х':
				return 'Kh';
			case 'Ц':
				return 'C';
			case 'Ч':
				return 'Ch';
			case 'Ш':
				return 'Sh';
			case 'Щ':
				return 'Sch';
			case 'Э':
				return 'Ae';
			case 'Ю':
				return 'Yu';
			case 'Я':
				return 'Ya';
			case 'а':
				return 'a';
			case 'б':
				return 'b';
			case 'в':
				return 'v';
			case 'г':
				return 'g';
			case 'д':
				return 'd';
			case 'е':
			case 'ё':
				return 'e';
			case 'ж':
				return 'j';
			case 'з':
				return 'z';
			case 'и':
			case 'й':
			case 'ы':
				return 'i';
			case 'к':
				return 'k';
			case 'л':
				return 'l';
			case 'м':
				return 'm';
			case 'н':
				return 'n';
			case 'о':
				return 'o';
			case 'п':
				return 'p';
			case 'р':
				return 'r';
			case 'с':
				return 's';
			case 'т':
				return 't';
			case 'у':
				return 'u';
			case 'ф':
				return 'f';
			case 'х':
				return 'kh';
			case 'ц':
				return 'c';
			case 'ч':
				return 'ch';
			case 'ш':
				return 'sh';
			case 'щ':
				return 'sch';
			case 'э':
				return 'ae';
			case 'ю':
				return 'yu';
			case 'я':
				return 'ya';
			case 'A':
			case 'B':
			case 'C':
			case 'D':
			case 'E':
			case 'F':
			case 'G':
			case 'H':
			case 'I':
			case 'J':
			case 'K':
			case 'L':
			case 'M':
			case 'N':
			case 'O':
			case 'P':
			case 'Q':
			case 'R':
			case 'S':
			case 'T':
			case 'U':
			case 'V':
			case 'W':
			case 'X':
			case 'Y':
			case 'Z':
			case 'a':
			case 'b':
			case 'c':
			case 'd':
			case 'e':
			case 'f':
			case 'g':
			case 'h':
			case 'i':
			case 'j':
			case 'k':
			case 'l':
			case 'm':
			case 'n':
			case 'o':
			case 'p':
			case 'q':
			case 'r':
			case 's':
			case 't':
			case 'u':
			case 'v':
			case 'w':
			case 'x':
			case 'y':
			case 'z':
				return $char;
			default:
				return '_';
		}
	}

	public static function boolToStr () {
		$bool = func_get_arg (0);

		if (is_bool($bool)) {
			if ($bool)
				return 'Y';
			else
				return 'N';
		}
		else {
			return $bool;
		}
	}

	public static function strToBool () {
		$str = func_get_arg(0);

		if (is_string($str)) {
			if ($str=="Y")
				return true;
			else
				return false;
		}
		else {
			return $str;
		}
	}

    public static function multiplication ($arMultiplier) {
        $result = 1;
        foreach ($arMultiplier as $multiplier) {
            $result = $result * $multiplier;
        }

        return $result;
    }

	public static function getSitePath ($path) {
		return str_replace(Config::getConfig("SITE_ROOT"),"",$path);
	}

	public static function getClassNameByTableName ($strTableName)
	{
		$strClassName = "MSergeev\\";
		$strTableName = str_replace("ms_","",$strTableName);
		$arStr = explode("_",$strTableName);
		for($i=0;$i<count($arStr);$i++)
		{
			if($i==0)
			{
				if ($arStr[$i] == "core")
				{
					$strClassName .= "Core\\Tables\\";
				}
				else
				{
					$strClassName .= "Packages\\";
					$arStr[$i] = static::setFirstCharToBig($arStr[$i]);
					$strClassName .= $arStr[$i]."\\Tables\\";
				}
			}
			else
			{
				$arStr[$i] = static::setFirstCharToBig($arStr[$i]);
				$strClassName .= $arStr[$i];
			}
		}
		$strClassName .= "Table";

		return $strClassName;
	}

	public static function runTableClassFunction ($strTable,$strFunction,$arParams=array())
	{
		$strClassFunction = static::getClassNameByTableName($strTable);
		$strClassFunction .= "::".$strFunction;
		if (empty($arParams))
		{
			return call_user_func($strClassFunction);
		}
		else
		{
			return call_user_func($strClassFunction,$arParams);
		}
	}

	public static function setFirstCharToBig ($str)
	{
		$str = iconv("utf-8","windows-1251",$str);
		$str[0] = strtoupper($str);
		$str = iconv("windows-1251","utf-8",$str);

		return $str;
	}

	/**
	 * @deprecated
	 * @see DateHelper->getShortNameDayOfWeek
	 */
	public static function getNameDayOfWeek ($day=null)
	{
		$dateHelper = new DateHelper();

		return $dateHelper->getShortNameDayOfWeek($day);
	}

	/**
	 * @deprecated
	 * @see DateHelper->getNameMonth
	 */
	public static function getNameMonth ($month=null)
	{
		$dateHelper = new DateHelper();

		return $dateHelper->getNameMonth($month);
	}

	public static function validateFloatVal ($strFloat)
	{
		$temp = str_replace(' ','',$strFloat);
		$temp = str_replace(',','.',$temp);
		$temp = floatval($temp);

		return $temp;
	}

	public static function validateIntVal ($strInt)
	{
		$temp = str_replace(' ','',$strInt);
		$temp = str_replace(',','.',$temp);
		$temp = intval($temp);

		return $temp;
	}
}