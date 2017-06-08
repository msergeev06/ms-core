<?php
/**
 * MSergeev\Core\Lib\Tools
 * Дополнительные инструменты ядра
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class Tools
{
	private static $search =  array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;");
	private static $replace = array("<",    ">",    "\"",     "'",      "&");
	private static $searchEx =  array("&amp;",     "&lt;",     "&gt;",     "&quot;",     "&#34",     "&#x22",     "&#39",     "&#x27",     "<",    ">",    "\"");
	private static $replaceEx = array("&amp;amp;", "&amp;lt;", "&amp;gt;", "&amp;quot;", "&amp;#34", "&amp;#x22", "&amp;#39", "&amp;#x27", "&lt;", "&gt;", "&quot;");


	/**
	 * Функция генерирует код из полученной текстовой строки
	 *
	 * @api
	 *
	 * @return string
	 */
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

	/**
	 * Переводит русский символ в латинский
	 *
	 * @api
	 *
	 * @param string $char Русский символ
	 *
	 * @return string Латинский символ
	 */
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
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case '0':
				return $char;
			default:
				return '_';
		}
	}

	/**
	 * Превращает булевское значение в символ
	 *
	 * @api
	 *
	 * @example true  => 'Y'
	 * @example false => 'N'
	 *
	 * @return string
	 */
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

	/**
	 * Превращает строковое значение в булевское
	 * 'Y' превращается в true, остальное в false
	 *
	 * @api
	 *
	 * @example 'Y' => true
	 * @example 'abrakadabra' => false
	 *
	 * @return bool
	 */
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

	/**
	 * multiplication
	 *
	 * @ignore
	 *
	 * @param $arMultiplier
	 *
	 * @return int
	 */
	public static function multiplication ($arMultiplier)
	{
		$result = 1;
		foreach ($arMultiplier as $multiplier) {
			$result = $result * $multiplier;
		}

		return $result;
	}

	/**
	 * Формирует относительный путь из абсолютного
	 *
	 * @api
	 *
	 * @param string $path Абсолютный путь
	 *
	 * @return string Относительный путь
	 */
	public static function getSitePath ($path) {
		return str_replace(Config::getConfig("SITE_ROOT"),"",$path);
	}

	/**
	 * Возвращает имя класса, описывающего таблицу, по ее имени в базе данных
	 *
	 * @api
	 *
	 * @param string $strTableName Имя таблицы в базе данных
	 *
	 * @return string Имя класса описывающего таблицу
	 */
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

	/**
	 * Запускает функцию класса описывающего таблицу для указанного имени таблицы
	 *
	 * @api
	 *
	 * @param string $strTable    Имя таблицы в базе данных
	 * @param string $strFunction Имя функции в классе описывающем таблицу
	 * @param array  $arParams    Передаваемые параметры
	 *
	 * @return mixed
	 */
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

	/**
	 * Возвращает строку, у которой первый символ переведен в верхний регистр
	 *
	 * @api
	 *
	 * @param string $str Исходная строка
	 *
	 * @return string
	 */
	public static function setFirstCharToBig ($str)
	{
		if (strlen($str)>0)
		{
			$str = iconv("utf-8","windows-1251",$str);
			$str[0] = strtoupper($str[0]);
			$str = iconv("windows-1251","utf-8",$str);
		}

		return $str;
	}

	/**
	 * Преобразует полученную строку к значению floatval,
	 * убирая пробелы и заменяя запятую ',' точкой '.'
	 *
	 * @api
	 *
	 * @param string $strFloat Исходная строка
	 *
	 * @return float Значение
	 */
	public static function validateFloatVal ($strFloat)
	{
		$temp = str_replace(' ','',$strFloat);
		$temp = str_replace(',','.',$temp);
		$temp = floatval($temp);

		return $temp;
	}

	/**
	 * Преобразует полученную строку к значению intval,
	 * используя функцию self::validateFloatVal
	 *
	 * @api
	 *
	 * @param string $strInt Исходная строка
	 *
	 * @return int Значение
	 */
	public static function validateIntVal ($strInt)
	{
		return intval($strInt);
	}

	public static function validateStringVal ($str)
	{
		return htmlspecialchars($str);
	}

	public static function validateBoolVal ($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
		{
			$value = 1;
		}
		elseif (is_string($value) && ($value == 'false' || $value== 'N'))
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			if (intval($value)==1)
			{
				$value = true;
			}
			else
			{
				$value = false;
			}
		}

		return $value;
	}

	public static function validateDateVal ($date)
	{
		$value = "";
		if (strpos($date,'.') !== false)
		{
			$arData = explode('.',$date);
			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				$bFirst = true;
				for ($i=0; $i<3; $i++) {
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$value .= ".";
					}
					if (intval($arData[$i]) >= 1 && intval($arData[$i])<=9)
					{
						$value .= "0";
					}
					$value .= intval($arData[$i]);
				}
			}
			else
			{
				return false;
			}
		}
		elseif (strpos($date,'-') !== false)
		{
			$arData = explode('-',$date);
			if (
				(intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[0]) >= 1970 && intval($arData[0]) <= 9999)
			)
			{
				$bFirst = true;
				for ($i=0; $i<3; $i++) {
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$value .= "-";
					}
					if (intval($arData[$i]) >= 1 && intval($arData[$i])<=9)
					{
						$value .= "0";
					}
					$value .= intval($arData[$i]);
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		return $value;
	}

	/**
	 * Функция транслитирует строку
	 *
	 * @api
	 *
	 * @param string $string Исходная строка
	 *
	 * @return string Транслитированная строка
	 */
	public static function transliterate($string)
	{
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		return strtr($string, $converter);
	}

	public static function getCurDir ()
	{
		$arPath = explode ('/',self::getCurPath());
		$path = "";
		for ($i=0; $i<(count($arPath)-1); $i++)
		{
			$path .= $arPath[$i].'/';
		}

		return $path;
	}

	public static function getCurPath ()
	{
		return $_SERVER['SCRIPT_NAME'];
	}

	public static function isDir ($needle)
	{
		$bDir = false;
		if (is_array($needle))
		{
			foreach($needle as $dir)
			{
				if (strpos(self::getCurDir(),$dir)!==false)
				{
					$bDir = true;
					break;
				}
			}
		}
		elseif (strpos (self::getCurDir(),$needle) !== false)
		{
			$bDir = true;
		}

		return $bDir;
	}

	public static function cropString ($string, $number=50, $dots='...')
	{
		if (strlen(mb_convert_encoding($string, 'windows-1251', 'utf-8'))>$number)
		{
			$string = mb_substr($string,0,$number-3,'UTF-8');
			return $string.$dots;
		}
		else
		{
			return $string;
		}
	}

	public static function sayRusRight ($value, $subjectiveCase=null, $genitiveSingular=null, $genitivePlural=null)
	{
		if (is_null($subjectiveCase))
		{
			$subjectiveCase = Loc::getPackMessage('core','tools_day1');
		}
		if (is_null($genitiveSingular))
		{
			$genitiveSingular = Loc::getPackMessage('core','tools_day2');
		}
		if (is_null($genitivePlural))
		{
			$genitivePlural = Loc::getPackMessage('core','tools_day3');
		}

		$x = $value % 100;
		$y = ($x % 10)-1;

		return ($x/10)>>0==1 ? $genitivePlural : ($y&12 ? $genitivePlural : ($y&3 ? $genitiveSingular : $subjectiveCase));
	}

	public static function roundEx($value, $prec=0)
	{
		$eps = 1.00/pow(10, $prec+4);
		return round(doubleval($value)+$eps, $prec);
	}

	public static function trimUnsafe($path)
	{
		return rtrim($path, "\0.\\/+ ");
	}

	public static function strrpos ($haystack, $needle)
	{
		if(strtoupper(Config::getConfig("CHARSET"))=="UTF-8")
		{
			//mb_strrpos does not work on invalid UTF-8 strings
			$ln = strlen($needle);
			for($i = strlen($haystack)-$ln; $i >= 0; $i--)
				if(substr($haystack, $i, $ln) == $needle)
					return $i;
			return false;
		}
		return strrpos($haystack, $needle);
	}

	public static function htmlspecialchars ($str)
	{
		static $search =  array("&amp;",     "&lt;",     "&gt;",     "&quot;",     "&#34",     "&#x22",     "&#39",     "&#x27",     "<",    ">",    "\"");
		static $replace = array("&amp;amp;", "&amp;lt;", "&amp;gt;", "&amp;quot;", "&amp;#34", "&amp;#x22", "&amp;#39", "&amp;#x27", "&lt;", "&gt;", "&quot;");
		return str_replace($search, $replace, $str);
	}

	public static function htmlspecialcharsBack ($str)
	{
		return str_replace(self::$search, self::$replace, $str);
	}

	public static function htmlspecialcharsEx ($str)
	{
		return str_replace(self::$searchEx, self::$replaceEx, $str);
	}

	public static function checkSerializedData($str, $max_depth = 200)
	{
		if(preg_match('/O\\:\\d/', $str)) // serialized objects
		{
			return false;
		}

		// check max depth in PHP 5.3.0 and earlier
		if(!version_compare(phpversion(),"5.3.0",">"))
		{
			$str1 = preg_replace('/[^{}]+/u', '', $str);
			$cnt = 0;
			for ($i=0,$len=strlen($str1);$i<$len;$i++)
			{
				// we've just cleared all possible utf-symbols, so we can use [] syntax
				if ($str1[$i]=='}')
					$cnt--;
				else
				{
					$cnt++;
					if ($cnt > $max_depth)
						break;
				}
			}

			return $cnt <= $max_depth;
		} else
			return true;
	}

	public static function HTMLToTxt($str, $strSiteUrl="", $aDelete=array(), $maxlen=70)
	{
		//get rid of whitespace
		$str = preg_replace("/[\\t\\n\\r]/", " ", $str);

		//replace tags with placeholders
		static $search = array(
			"'<script[^>]*?>.*?</script>'si",
			"'<style[^>]*?>.*?</style>'si",
			"'<select[^>]*?>.*?</select>'si",
			"'&(quot|#34);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
		);

		static $replace = array(
			"",
			"",
			"",
			"\"",
			"\xa1",
			"\xa2",
			"\xa3",
			"\xa9",
		);

		$str = preg_replace($search, $replace, $str);

		$str = preg_replace("#<[/]{0,1}(b|i|u|em|small|strong)>#i", "", $str);
		$str = preg_replace("#<[/]{0,1}(font|div|span)[^>]*>#i", "", $str);

		//ищем списки
		$str = preg_replace("#<ul[^>]*>#i", "\r\n", $str);
		$str = preg_replace("#<li[^>]*>#i", "\r\n  - ", $str);

		//удалим то что заданно
		foreach($aDelete as $del_reg)
			$str = preg_replace($del_reg, "", $str);

		//ищем картинки
		$str = preg_replace("/(<img\\s.*?src\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(\\s.+?>|\\s*>)/is", "[".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
		$str = preg_replace("/(<img\\s.*?src\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is", "[".chr(1)."\\3".chr(1)."] ", $str);

		//ищем ссылки
		$str = preg_replace("/(<a\\s.*?href\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(.*?>)(.*?)<\\/a>/is", "\\6 [".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
		$str = preg_replace("/(<a\\s.*?href\\s*=\\s*)([\"']?)(.*?)(\\2)(.*?>)(.*?)<\\/a>/is", "\\6 [".chr(1)."\\3".chr(1)."] ", $str);

		//ищем <br>
		$str = preg_replace("#<br[^>]*>#i", "\r\n", $str);

		//ищем <p>
		$str = preg_replace("#<p[^>]*>#i", "\r\n\r\n", $str);

		//ищем <hr>
		$str = preg_replace("#<hr[^>]*>#i", "\r\n----------------------\r\n", $str);

		//ищем таблицы
		$str = preg_replace("#<[/]{0,1}(thead|tbody)[^>]*>#i", "", $str);
		$str = preg_replace("#<([/]{0,1})th[^>]*>#i", "<\\1td>", $str);

		$str = preg_replace("#</td>#i", "\t", $str);
		$str = preg_replace("#</tr>#i", "\r\n", $str);
		$str = preg_replace("#<table[^>]*>#i", "\r\n", $str);

		$str = preg_replace("#\r\n[ ]+#", "\r\n", $str);

		//мочим вообще все оставшиеся тэги
		$str = preg_replace("#<[/]{0,1}[^>]+>#i", "", $str);

		$str = preg_replace("#[ ]+ #", " ", $str);
		$str = str_replace("\t", "    ", $str);

		//переносим длинные строки
		if($maxlen > 0)
			$str = preg_replace("#([^\\n\\r]{".intval($maxlen)."}[^ \\r\\n]*[\\] ])([^\\r])#", "\\1\r\n\\2", $str);

		$str = str_replace(chr(1), " ",$str);

		return trim($str);
	}

	/*
	public static function insertSpaces($sText, $iMaxChar=80, $symbol=" ", $bHTML=false)
	{
		$iMaxChar = intval($iMaxChar);
		if ($iMaxChar > 0 && strlen($sText) > $iMaxChar)
		{
			if ($bHTML)
			{
				$obSpacer = new CSpacer($iMaxChar, $symbol);
				return $obSpacer->InsertSpaces($sText);
			}
			else
			{
				return preg_replace("/([^() \\n\\r\\t%!?{}\\][-]{".$iMaxChar."})/u","\\1".$symbol, $sText);
			}
		}
		return $sText;
	}
	*/
}