<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */


use \MSergeev\Core\Lib\DBResult;
use \MSergeev\Core\Exception;
use \MSergeev\Core\Lib;

/**
 * Returns HTML "input"
 *
 * @param string        $strType        input type
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string|array  $strCmp         checked
 * @param bool          $strPrintValue  Выводить strValue или strPrint
 * @param string        $strPrint       Вывод описания поля
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputType($strType, $strName, $strValue, $strCmp, $strPrintValue=false, $strPrint="", $field1="", $strId="")
{
	$bCheck = false;
	if($strValue <> '')
	{
		if (is_array($strValue))
		{
			if(is_array($strCmp))
			{
				$bCheck = in_array($strValue, $strCmp);
			}
			elseif($strCmp <> '')
			{
				$bCheck = in_array($strValue, explode(",", $strCmp));
			}
		}
		else
		{
			$bCheck = ($strValue == $strCmp);
		}
	}
	$bLabel = false;
	if ($strType == 'radio')
		$bLabel = true;
	return ($bLabel? '<label>': '').'<input type="'.$strType.'" '.$field1.' name="'.$strName.'" id="'.($strId <> ''? $strId : $strName).'" value="'.$strValue.'"'.
	($bCheck? ' checked':'').'>'.($strPrintValue? $strValue:$strPrint).($bLabel? '</label>': '');
}

/**
 * Returns HTML "select"
 *
 * @param string    $strBoxName     Input name
 * @param array     $arValues       Array with items
 * @param string    $strDetText     Empty item text
 * @param string    $strSelectedVal Selected item value
 * @param string    $field1         Additional attributes
 * @return string
 */
function SelectBox($strBoxName, $arValues, $strDetText = "", $strSelectedVal = "null", $field1="class=\"typeselect\"")
{
	$strReturnBox = "<select ".$field1." name=\"".$strBoxName."\" id=\"".$strBoxName."\">";
	if ($strDetText <> '')
	{
		$strReturnBox = $strReturnBox."<option value=\"NULL\"";
		if (strtolower($strSelectedVal) == "null")
		{
			$strReturnBox.= " selected";
		}
		$strReturnBox.= ">".$strDetText."</option>";
	}
	if (empty($arValues))
	{
		return false;
	}
	foreach ($arValues as $arValue) {
		$strReturnBox = $strReturnBox."<option ";
		if (
			(isset($arValue["SELECTED"]) && $arValue["SELECTED"])
			|| ($strSelectedVal != "" && $strSelectedVal == $arValue["VALUE"])
		)
		{
			$strReturnBox = $strReturnBox." selected ";
		}
		$strReturnBox = $strReturnBox."value=\"".$arValue["VALUE"]. "\">".$arValue["NAME"]."</option>";
	}
	return $strReturnBox."</select>";
}

function SelectBoxBool ($strBoxName, $strSelectedVal = "", $strYes='', $strNo='', $field1="class=\"typeselect\"")
{
	if ($strYes == '') $strYes = 'Да';
	if ($strNo == '') $strNo = 'Нет';
	if ($strSelectedVal == "") $strSelectedVal = 0;

	$arValues = array(
		array(
			'VALUE' => 0,
			'NAME' => $strNo
		),
		array(
			'VALUE' => 1,
			'NAME' => $strYes
		)
	);

	return SelectBox($strBoxName, $arValues, '', $strSelectedVal, $field1);
}

/**
 * Returns HTML multiple "select"
 *
 * @param string    $strBoxName             Input name
 * @param array     $arValues               Array with items
 * @param string    $strDetText             Empty item text
 * @param bool      $strDetText_selected    Allow to choose an empty item
 * @param string    $size                   Size attribute
 * @param string    $field1                 Additional attributes
 * @return string
 */
function SelectBoxM($strBoxName, $arValues, $strDetText = "", $strDetText_selected = false, $size = "5", $field1="class=\"typeselect\"")
{
	$strReturnBox = "<select ".$field1." multiple name=\"".$strBoxName."\" id=\"".$strBoxName."\" size=\"".$size."\">";
	if ($strDetText <> '')
	{
		$strReturnBox = $strReturnBox."<option ";
		if ($strDetText_selected)
			$strReturnBox = $strReturnBox." selected ";
		$strReturnBox = $strReturnBox." value='NULL'>".$strDetText."</option>";
	}
	foreach ($arValues as $arValue) {
		$strReturnBox = $strReturnBox."<option ";
		if (isset($arValue["SELECTED"]) && $arValue["SELECTED"])
		{
			$strReturnBox = $strReturnBox." selected ";
		}
		$strReturnBox = $strReturnBox."value=\"".$arValue["VALUE"]. "\">".$arValue["NAME"]."</option>";
	}
	return $strReturnBox."</select>";
}

/**
 * Show Input for select Date
 *
 * @param string $strName
 * @param string $strValue
 * @param string $field1
 * @param string $strId
 *
 * @throw Exception\ArgumentNullException
 *
 * @return string
 */
function InputCalendar ($strName, $strValue="", $field1="", $strId="")
{
	try
	{
		if (strlen($strName)==0)
		{
			throw new Exception\ArgumentNullException("strName");
		}
		if (strlen($strValue)==0 && !is_null($strValue)) $strValue = date("d.m.Y");
		Lib\Buffer::addJS(Lib\Config::getConfig("CORE_ROOT")."js/calendar.js");
		$strReturnBox = "<input ".$field1." type=\"text\" id=\"".(($strId!="")?$strId:$strName);
		$strReturnBox.= "\" name=\"".$strName."\" value=\"".$strValue."\"";
		$strReturnBox.= " onfocus=\"this.select();lcs(this)\"";
		$strReturnBox.= " onclick=\"event.cancelBubble=true;this.select();lcs(this)\"";
		$strReturnBox.= ">";

		return $strReturnBox;
	}
	catch (Exception\ArgumentNullException $e)
	{
		$e->showException();
	}
}

function LineCharts ($arParams = null)
{
	/*
	$arParams = array();
	$arParams['title'] = 'Средняя месячная температура';
	$arParams['subtitle'] = 'Источник: WorldClimate.com';
	$arParams['xAxis'] = array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
	$arParams['yAxis'] = 'Температура (°C)';
	$arParams['valueSuffix'] = '°C';
	$arParams['series'] = array(
		0 => array(
			'name' => 'Токио',
			'data' => array(7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6)
		),
		1 => array(
			'name' => 'Нью-Йорк',
			'data' => array(-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5)
		),
		2 => array(
			'name' => 'Берлин',
			'data' => array(-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0)
		),
		3 => array(
			'name' => 'Лондон',
			'data' => array(3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8)
		)
	);
	*/
	try
	{
		if (is_null($arParams))
		{
			throw new Exception\ArgumentNullException('arParams');
		}
		else
		{
			if (!isset($arParams['title']))
			{
				$arParams['title'] = 'График';
			}
			if (!isset($arParams['subtitle']))
			{
				$arParams['subtitle'] = 'MajorDoMo';
			}
			if (!isset($arParams['xAxis']))
			{
				throw new Exception\ArgumentNullException('arParams[xAxis]');
			}
			elseif (!is_array($arParams['xAxis']))
			{
				throw new Exception\ArgumentOutOfRangeException('arParams[xAxis]');
			}
			if (!isset($arParams['yAxis']))
			{
				throw new Exception\ArgumentNullException('arParams[yAxis]');
			}
			if (!isset($arParams['valueSuffix']))
			{
				throw new Exception\ArgumentNullException("arParams[valueSuffix]");
			}
			if (!isset($arParams['series']))
			{
				throw new Exception\ArgumentNullException('arParams[series]');
			}
			if (!isset($arParams['container']))
			{
				$containerName = $arParams['container'] = 'line_charts';
			}
			else
			{
				$containerName = $arParams['container'];
			}
		}
	}
	catch (Exception\ArgumentNullException $e)
	{
		die($e->showException());
	}
	catch (Exception\ArgumentOutOfRangeException $e1)
	{
		die($e1->showException());
	}


	Lib\Buffer::addJS (Lib\Config::getConfig ("CORE_ROOT")."js/highcharts.js");
	Lib\Buffer::addJS (Lib\Config::getConfig ("CORE_ROOT")."js/exporting.js");

	$arHighCharts = array (
		'title' => array(
			'text' => $arParams['title'],
			'x' => -20 //center
		),
		'subtitle' => array(
			'text' => $arParams['subtitle'],
			'x' => -20
		),
		'xAxis' => array(
			'categories' => $arParams['xAxis']
		),
		'yAxis' => array(
			'title' => array(
				'text' => $arParams['yAxis']
			),
			'plotLines' => array(
				0 => array(
					'value' => 0,
					'width' => 1,
					'color' => '#808080'
				)
			)
		),
		'tooltip' => array(
			'valueSuffix' => $arParams['valueSuffix']
		),
		'legend' => array(
			'layout' => 'vertical',
			'align' => 'right',
			'verticalAlign' => 'middle',
			'borderWidth' => 0
		),
		'series' => $arParams['series']
	);
	$echo = '<div id="'.$containerName.'" style="min-width: 310px; height: 400px; margin: 0 auto"></div>'."\n";
	$echo .= '<script type="text/javascript">'."\n\t"
		."\$(function () {\n\t\t"
		."\$('#".$containerName."').highcharts(".json_encode($arHighCharts).");\n\t"
		."});\n"
		."</script>\n";

	return $echo;
}