<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Calendar
{
	const PERIOD_EMPTY = "NULL";
	const PERIOD_DAY = "day";
	const PERIOD_WEEK = "week";
	const PERIOD_MONTH = "month";
	const PERIOD_QUARTER = "quarter";
	const PERIOD_YEAR = "year";
	const PERIOD_EXACT = "exact";
	const PERIOD_BEFORE = "before";
	const PERIOD_AFTER = "after";
	const PERIOD_INTERVAL = "interval";

	private function InitPeriodList($arPeriodParams = array())
	{
		$arPeriod = array(
			self::PERIOD_EMPTY => "(нет)",
			self::PERIOD_DAY => "День",
			self::PERIOD_WEEK => "Неделя",
			self::PERIOD_MONTH => "Месяц",
			self::PERIOD_QUARTER => "Квартал",
			self::PERIOD_YEAR => "Год",
			self::PERIOD_EXACT => "Точно",
			self::PERIOD_BEFORE => "Позже",
			self::PERIOD_AFTER => "Раньше",
			self::PERIOD_INTERVAL => "Интервал"
		);

		if (empty($arPeriodParams) || !is_array($arPeriodParams))
			return $arPeriod;

		$arReturnPeriod = array();

		foreach ($arPeriodParams as $periodName => $lPhrase)
		{
			if (isset($arPeriod[$periodName]))
				$arReturnPeriod[$periodName] = $lPhrase;
			elseif (isset($arPeriod[$arPeriodParams[$periodName]]))
				$arReturnPeriod[$arPeriodParams[$periodName]] = $arPeriod[$arPeriodParams[$periodName]];
		}

		if (empty($arReturnPeriod))
			$arReturnPeriod = $arPeriod;
		return $arReturnPeriod;
	}

	public function ShowScript()
	{
		//Buffer::addJS(Config::getConfig("CORE_ROOT")."js/core.js");
		//Buffer::addCSS(Config::getConfig("CORE_ROOT")."css/core.css");
		Buffer::addJS(Config::getConfig("CORE_ROOT")."js/core_date.js");
		Buffer::addCSS(Config::getConfig("CORE_ROOT")."css/core_date.css");

		//CJSCore::Init(array('date'));
		/*
		'js' => '/bitrix/js/main/core/core_date.js',
		'css' => '/bitrix/js/main/core/css/core_date.css',
		'lang' => '/bitrix//modules/main/lang/ru/date_format.php',
		'css_core' => '/bitrix/js/main/core/css/core.css',
		'js_core' => '/bitrix/js/main/core/core.js',

		 */
	}

	public function Calendar($sFieldName, $sFromName="", $sToName="", $bTime=false)
	{
		/*
		/** @global CMain $APPLICATION *
		global $APPLICATION;

		ob_start();
		$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
			'RETURN' => 'Y',
			'SHOW_INPUT' => 'N',
			'INPUT_NAME' => $sFieldName,
			'SHOW_TIME' => $bTime ? 'Y' : 'N'
		), null, array('HIDE_ICONS' => 'Y'));
		$res = ob_get_contents();
		ob_end_clean();

		return $res;

		*/
		//Buffer::addJS(Config::getConfig("CORE_ROOT")."js/core.js");
		//Buffer::addCSS(Config::getConfig("CORE_ROOT")."css/core.css");
		Buffer::addJS(Config::getConfig("CORE_ROOT")."js/core_date.js");
		Buffer::addCSS(Config::getConfig("CORE_ROOT")."css/core_date.css");
		Buffer::addJS(Config::getConfig("CORE_ROOT")."js/core_popup.js");
		Buffer::addCSS(Config::getConfig("CORE_ROOT")."css/core_popup.css");

		$arParams = array(
			'RETURN' => 'Y',
			'SHOW_INPUT' => 'N',
			'INPUT_NAME' => $sFieldName,
			'SHOW_TIME' => $bTime ? 'Y' : 'N'
		);
		?><img src="/bitrix/js/main/core/images/calendar-icon.gif" alt="Выбрать дату в календаре" class="calendar-icon" onclick="BX.calendar({node:this, field:'<?=$arParams['INPUT_NAME']?>', form: '', bTime: <?=$arParams['SHOW_TIME'] == 'Y' ? 'true' : 'false'?>, currentTime: '<?=(time())?>', bHideTime: <?=$arParams['HIDE_TIMEBAR'] == 'Y' ? 'true' : 'false'?>});" onmouseover="BX.addClass(this, 'calendar-icon-hover');" onmouseout="BX.removeClass(this, 'calendar-icon-hover');" border="0"/><?
	}

	public function CalendarDate($sFieldName, $sValue="", $size="10", $bTime=false)
	{
		// component can't set 'size' param
		/*
		return '
	<div class="adm-input-wrap adm-input-wrap-calendar">
		<input class="adm-input adm-input-calendar" type="text" name="'.$sFieldName.'" size="'.(intval($size)+3).'" value="'.htmlspecialcharsbx($sValue).'">
		<span class="adm-calendar-icon" title="'.GetMessage("admin_lib_calend_title").'" onclick="BX.calendar({node:this, field:\''.$sFieldName.'\', form: \'\', bTime: '.($bTime ? 'true' : 'false').', bHideTime: false});"></span>
	</div>';
		*/

	}

	/**
	 * @param string $sFromName
	 * @param string $sToName
	 * @param string $sFromVal
	 * @param string $sToVal
	 * @param bool $bSelectShow
	 * @param int $size
	 * @param bool $bTime
	 * @param bool|array $arPeriod
	 * @param string $periodValue
	 * @return string
	 */
	public function CalendarPeriodCustom($sFromName, $sToName, $sFromVal="", $sToVal="", $bSelectShow=false, $size=10, $bTime=false, $arPeriod = false, $periodValue = '')
	{
		$arPeriodList = self::InitPeriodList($arPeriod);

		return self::GetPeriodHtml($sFromName, $sToName, $sFromVal, $sToVal, $bSelectShow, $size, $bTime, $arPeriodList, $periodValue);
	}

	/**
	 * @param string $sFromName
	 * @param string $sToName
	 * @param string $sFromVal
	 * @param string $sToVal
	 * @param bool $bSelectShow
	 * @param int $size
	 * @param bool $bTime
	 * @return string
	 */
	public function CalendarPeriod($sFromName, $sToName, $sFromVal="", $sToVal="", $bSelectShow=false, $size=10, $bTime=false)
	{
		$arPeriodList = self::InitPeriodList();

		return self::GetPeriodHtml($sFromName, $sToName, $sFromVal, $sToVal, $bSelectShow, $size, $bTime, $arPeriodList);
	}

	/**
	 * @param $sFromName
	 * @param $sToName
	 * @param string $sFromVal
	 * @param string $sToVal
	 * @param bool $bSelectShow
	 * @param int $size
	 * @param bool $bTime
	 * @param $arPeriod
	 * @param string $periodValue
	 * @return string
	 */
	private function GetPeriodHtml($sFromName, $sToName, $sFromVal="", $sToVal="", $bSelectShow=false, $size = 10, $bTime=false, $arPeriod, $periodValue = '')
	{
		/*
		$size = (int)$size;

		$s = '
		<div class="adm-calendar-block adm-filter-alignment">
			<div class="adm-filter-box-sizing">';

		if($bSelectShow)
		{
			$sPeriodName = $sFromName."_FILTER_PERIOD";
			$sDirectionName = $sFromName."_FILTER_DIRECTION";

			$arDirection = array(
				"previous"=>GetMessage("admin_lib_calend_previous"),
				"current"=>GetMessage("admin_lib_calend_current"),
				"next"=>GetMessage("admin_lib_calend_next")
			);

			$s .= '<span class="adm-select-wrap adm-calendar-period" ><select class="adm-select adm-calendar-period" id="'.$sFromName.'_calendar_period" name="'.$sPeriodName.'" onchange="BX.CalendarPeriod.OnChangeP(this);" title="'.GetMessage("admin_lib_calend_period_title").'">';

			$currentPeriod = '';
			if (isset($GLOBALS[$sPeriodName]))
				$currentPeriod = (string)$GLOBALS[$sPeriodName];
			$periodValue = (string)$periodValue;
			if ($periodValue != '')
				$currentPeriod = $periodValue;
			foreach($arPeriod as $k => $v)
			{
				$k = ($k != "NOT_REF" ? $k : "");
				$s .= '<option value="'.$k.'"'.(($currentPeriod != '' && $currentPeriod == $k) ? " selected":"").'>'.$v.'</option>';
			}
			unset($currentPeriod);

			$s .='</select></span>';

			$currentDirection = '';
			if (isset($GLOBALS[$sDirectionName]))
				$currentDirection = (string)$GLOBALS[$sDirectionName];
			$s .= '<span class="adm-select-wrap adm-calendar-direction" style="display: none;"><select class="adm-select adm-calendar-direction" id="'.$sFromName.'_calendar_direct" name="'.$sDirectionName.'" onchange="BX.CalendarPeriod.OnChangeD(this);"  title="'.GetMessage("admin_lib_calend_direct_title").'">';
			foreach($arDirection as $k => $v)
				$s .= '<option value="'.$k.'"'.($currentDirection == $k ? " selected":"").'>'.$v.'</option>';
			unset($currentDirection);

			$s .='</select></span>';
		}

		$s .=''.
			'<div class="adm-input-wrap adm-calendar-inp adm-calendar-first" style="display: '.($bSelectShow ? 'none' : 'inline-block').';">'.
			'<input type="text" class="adm-input adm-calendar-from" id="'.$sFromName.'_calendar_from" name="'.$sFromName.'" size="'.($size+5).'" value="'.htmlspecialcharsbx($sFromVal).'">'.
			'<span class="adm-calendar-icon" title="'.GetMessage("admin_lib_calend_title").'" onclick="BX.calendar({node:this, field:\''.$sFromName.'\', form: \'\', bTime: '.($bTime ? 'true' : 'false').', bHideTime: false});"></span>'.
			'</div>
		<span class="adm-calendar-separate" style="display: '.($bSelectShow ? 'none' : 'inline-block').'"></span>'.
			'<div class="adm-input-wrap adm-calendar-second" style="display: '.($bSelectShow ? 'none' : 'inline-block').';">'.
			'<input type="text" class="adm-input adm-calendar-to" id="'.$sToName.'_calendar_to" name="'.$sToName.'" size="'.($size+5).'" value="'.htmlspecialcharsbx($sToVal).'">'.
			'<span class="adm-calendar-icon" title="'.GetMessage("admin_lib_calend_title").'" onclick="BX.calendar({node:this, field:\''.$sToName.'\', form: \'\', bTime: '.($bTime ? 'true' : 'false').', bHideTime: false});"></span>'.
			'</div>'.
			'<script type="text/javascript">
			window["'.$sFromName.'_bTime"] = '.($bTime ? "true" : "false").';';

		if($bSelectShow)
			$s .='BX.CalendarPeriod.Init(BX("'.$sFromName.'_calendar_from"), BX("'.$sToName.'_calendar_to"), BX("'.$sFromName.'_calendar_period"));';

		$s .='
		</script>
		</div>
		</div>';

		return $s;
		*/
	}

}