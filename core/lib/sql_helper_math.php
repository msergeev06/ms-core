<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class SqlHelperMath extends SqlHelper
{
	function __construct ()
	{

	}

	// Математические функции

	// Математическая функция, возвращающая абсолютное (положительное) значение указанного числового выражения.
	public function getAbsFunction ($numeric_expression)
	{
		return 'ABS ('.$numeric_expression.')';
	}

	// Математическая функция, возвращающая угол в радианах, косинус которого является указанным выражением типа float;
	// также называется арккосинусом.
	public function getAcosFunction ($float_expression)
	{
		return 'ACOS ('.$float_expression.')';
	}

	// Возвращает угол в радианах, синус которого задан как выражение типа float. Это так называемый арксинус.
	public function getAsinFunction ($float_expression)
	{
		return 'ASIN ('.$float_expression.')';
	}

	// Возвращает угол в радианах, тангенс которого задан выражением float. Эта функция называется также арктангенсом.
	// Эта функция арктангенсом.
	public function getAtanFunction ($float_expression)
	{
		return 'ATAN ('.$float_expression.')';
	}

	// Возвращает угол в радианах между положительным направлением оси X и лучом, проведенным из начала координат
	// в точку (y, x), где x и y — значения двух указанных выражений с плавающей запятой.
	public function getAtn2Function ($float_expression, $float_expression2)
	{
		return 'ATN2 ('.$float_expression.', '.$float_expression2.')';
	}

	// Возвращает наименьшее целое число, которое больше или равно данному числовому выражению.
	public function getSeilingFunction ($numeric_expression)
	{
		return 'CEILING ('.$numeric_expression.')';
	}

	// Математическая функция, возвращающая тригонометрический косинус указанного угла в радианах в
	// указанном выражении.
	public function getCosFunction ($float_expression)
	{
		return 'COS ('.$float_expression.')';
	}

	// Математическая функция, возвращающая тригонометрический котангенс заданного угла в радианах в заданном
	// выражении float.
	public function getCotFunction ($float_expression)
	{
		return 'COT ('.$float_expression.')';
	}

	// Возвращает для значения угла в радианах соответствующее значение в градусах.
	public function getDegreesFunction ($numeric_expression)
	{
		return 'DEGREES ('.$numeric_expression.')';
	}

	// Возвращает значение экспоненты заданного выражения типа float.
	public function getExpFunction ($float_expression)
	{
		return 'EXP ('.$float_expression.')';
	}

	// Возвращает наибольшее целое число, меньшее или равное указанному числовому выражению.
	public function getFloorFunction ($numeric_expression)
	{
		return 'FLOOR ('.$numeric_expression.')';
	}

	// Возвращает натуральный логарифм данного выражения типа float в SQL Server.
	public function getLogFunction ($float_expression, $base=null)
	{
		$return = 'LOG ('.$float_expression;
		if (!is_null($base)) $return .= ', '.$base;
		$return .= ')';
		return $return;
	}

	// Возвращает десятичный логарифм указанного выражения float.
	public function getLog10Function ($float_expression)
	{
		return 'LOG10 ('.$float_expression.')';
	}

	// Возвращает константное значение PI.
	public function getPiFunction ()
	{
		return 'PI ()';
	}

	// Возвращает значение указанного выражения, возведенное в заданную степень.
	public function getPowerFunction ($float_expression, $y)
	{
		return 'POWER ('.$float_expression.', '.$y.')';
	}

	// Для введенного числового выражения в градусах возвращает значение в радианах.
	public function getRadiansFunction ($numeric_expression)
	{
		return 'RADIANS ('.$numeric_expression.')';
	}

	// Возвращает псевдослучайное значение типа float от 0 до 1.
	public function getRandFunction ($seed=null)
	{
		$return = 'RAND (';
		if (!is_null($seed)) $return .= $seed;
		$return .= ')';

		return $return;
	}

	// Возвращает числовое значение, округленное до указанной длины или точности.
	public function getRoundFunction ($numeric_expression, $length, $function=0)
	{
		if (is_string($function)) {
			if ($function != "tinyint" && $function != "smallint" && $function != "int")
				$function = 0;
		}

		return 'ROUND ('.$numeric_expression.', '.$length.', '.$function.')';
	}

	// Возвращает положительное (+1), нулевое (0) или отрицательное (-1) значение,
	// обозначающее знак заданного выражения.
	public function getSignFunction ($numeric_expression)
	{
		return 'SIGN ('.$numeric_expression.')';
	}

	// Возвращает значение тригонометрического синуса указанного угла в радианах и приблизительное числовое
	// выражение типа float.
	public function getSinFunction ($float_expression)
	{
		return 'SIN ('.$float_expression.')';
	}

	// Возвращает квадратный корень данного числа с плавающей точкой.
	public function getSqrtFunction ($float_expression)
	{
		return 'SQRT ('.$float_expression.')';
	}

	// Возвращает квадрат указанного числа с плавающей точкой.
	public function getSquareFunction ($float_expression)
	{
		return 'SQUARE ('.$float_expression.')';
	}

	// Возвращает тангенс входного аргумента.
	public function getTanFunction ($float_expression)
	{
		return 'TAN ('.$float_expression.')';
	}
}