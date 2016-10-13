<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class SqlHelperStr extends SqlHelper
{
	function __construct ()
	{

	}

	//*********** Строковые функции ***************

	// Возвращает код ASCII первого символа указанного символьного выражения.
	public function getAsciiFunction ($character_expression)
	{
		return 'ASCII ('.$character_expression.')';
	}

	// Преобразует код ASCII int в символ.
	public function getCharFunction ($integer_expression)
	{
		return 'CHAR ('.$integer_expression.')';
	}

	// Ищет в выражении другое выражение и возвращает его начальную позицию, если оно найдено.
	public function getCharIndexFunction ($expressionToFind, $expressionToSearch, $start_location=0)
	{
		$result = "CHARINDEX ('".$expressionToFind."', ";
		if (strpos($expressionToSearch,'@'))
			$result .= $expressionToSearch.", ";
		else
			$result .= "'".$expressionToSearch."', ";
		$result .= $start_location.")";

		return $result;
	}

	// Возвращает строку, являющуюся результатом объединения двух или более строковых значений.
	public function getConcatFunction ($arValue=array())
	{
		$result = "CONCAT (";
		$bFirst = true;
		if (is_string($arValue))
		{
			$result = $arValue;
		}
		elseif (is_array($arValue))
		{
			foreach ($arValue as $value)
			{
				if ($bFirst)
				{
					$result .= $value;
					$bFirst = false;
				}
				else
				{
					$result .= ", ".$value;
				}
			}
		}
		$result .= ")";

		return $result;
	}

	// Возвращает целочисленную разницу между значениями SOUNDEX двух символьных выражений.
	public function getDifferenceFunction ($character_expression1, $character_expression2)
	{
		return "DIFFERENCE ('".$character_expression1."', '".$character_expression2."')";
	}

	// Возвращает значение, указанное в формате, языке и региональных параметрах (необязательно) в SQL Server 2014.
	// Для выполнения форматирования значения даты, времени и чисел с учетом локали в виде строк используется функция
	// FORMAT. Для общих преобразований типов данных продолжайте использовать CAST и CONVERT.
	public function getFormat ($value, $format, $culture=null)
	{
		$result = "FORMAT (".$value.", '".$format."'";
		if (!is_null($culture))
			$result .= ", ".$culture;
		$result .= ")";

		return $result;
	}

	// Возвращает указанное число символов символьного выражения слева.
	public function getLeftFunction ($character_expression, $integer_expression)
	{
		return "LEFT ('".$character_expression."', ".$integer_expression.")";
	}

	// Возвращает количество символов указанного строкового выражения, исключая конечные пробелы.
	public function getLenFunction ($string_expression)
	{
		return "LEN ('".$string_expression."')";
	}

	// Возвращает символьное выражение после преобразования символьных данных верхнего регистра в символьные
	// данные нижнего регистра.
	public function getLowerFunction ($character_expression)
	{
		return "LOWER ( '".$character_expression."')";
	}

	// Возвращает символьное выражение после удаления начальных пробелов.
	public function getLTrimFunction ($character_expression)
	{
		return "LTRIM ( '".$character_expression."')";
	}

	// Возвращает символ Юникода с указанным целочисленным кодом, определенным в стандарте Юникода.
	public function getNCharFunction ($integer_expression)
	{
		return 'NCHAR ( '.$integer_expression.')';
	}

	// Для любого допустимого символьного или текстового типа данных возвращает начальную позицию первого
	// вхождения шаблона в указанном выражении или нули, если шаблон не найден.
	public function getPatIndexFunction ($pattern, $expression)
	{
		return "PATINDEX ( '%".$pattern."%' , ".$expression.")";
	}

	// Возвращает Юникод-строку с разделителями, образуя из строки ввода правильный идентификатор
	// с разделителем SQL Server.
	public function getQuoteNameFunction ($character_string, $quote_character=null)
	{
		$result = "QUOTENAME ( '".$character_string."'";
		if (!is_null($quote_character))
			$result .= ", '".$quote_character."'";
		$result .= ")";

		return $result;
	}

	// Заменяет все вхождения указанного строкового значения другим строковым значением.
	public function getReplaceFunction ($string_expression, $string_pattern, $string_replacement)
	{
		return "REPLACE ('".$string_expression."' , '".$string_pattern."', '".$string_replacement."')";
	}

	// Повторяет значение строки указанное число раз.
	public function getReplicateFunction ($string_expression, $integer_expression)
	{
		return "REPLICATE ('".$string_expression."', ".$integer_expression.")";
	}

	// Возвращает строковое значение, где символы переставлены в обратном порядке справа налево.
	public function getReverseFunction ($string_expression)
	{
		return "REVERSE ('".$string_expression."')";
	}

	// Возвращает указанное число символов символьной строки справа.
	public function getRightFunction ($character_expression, $integer_expression)
	{
		return "RIGHT ('".$character_expression."', ".$integer_expression.")";
	}

	// Возвращает строковое выражение, удаляя все завершающие пробелы.
	public function getRTrimFunction ($character_expression)
	{
		return "RTRIM ( '".$character_expression."')";
	}

	// Возвращает четырехсимвольный код (SOUNDEX) для оценки степени сходства двух строк.
	public function getSoundexFunction ($character_expression)
	{
		return "SOUNDEX ( '".$character_expression."')";
	}

	// Возвращает строку пробелов.
	public function getSpaceFunction ($integer_expression)
	{
		return 'SPACE ( '.$integer_expression.')';
	}

	// Возвращает символьные данные, преобразованные из числовых данных.
	public function getStrFunction ($float_expression, $length=10, $decimal=16)
	{
		if ($decimal>16) $decimal=16;
		return 'STR ('.$float_expression.', '.$length.', '.$decimal.')';
	}

	// Функция STUFF вставляет одну строку в другую. Она удаляет указанное количество символов первой строки
	// в начальной позиции и вставляет на их место вторую строку.
	public function getStuffFunction ($character_expression, $start, $length, $replaceWith_expression)
	{
		return "STUFF ( '".$character_expression."', ".$start.", ".$length.", '".$replaceWith_expression."')";
	}

	// Возвращает часть символьного, двоичного, текстового или графического выражения в SQL Server.
	public function getSubStringFunction ($expression, $start, $length)
	{
		return 'SUBSTRING ( '.$expression.', '.$start.', '.$length.')';
	}

	// Возвращает целочисленное значение, соответствующее стандарту Юникод, для первого символа входного выражения.
	public function getUnicodeFunction ($ncharacter_expression)
	{
		return "UNICODE ('".$ncharacter_expression."')";
	}

	// Возвращает символьное выражение, в котором символы нижнего регистра преобразованы в символы верхнего регистра.
	public function getUpperFunction ($character_expression)
	{
		return "UPPER ( '".$character_expression."')";
	}
}