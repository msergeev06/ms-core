<?php
/**
 * MSergeev\Core\Lib\Webix
 * Работа с дополнительной библиотекой Webix
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;

class Webix
{
	/**
	 * @var string Путь к корню
	 */
	protected static $coreRoot = null;

	/**
	 * @var string Путь к главному JS файлу
	 */
	protected static $mainJs = null;

	/**
	 * @var string Путь к главному CSS файлу
	 */
	protected static $mainCss = null;

	/**
	 * @var string Путь к каталогу с дополнительными CSS файлами
	 */
	protected static $otherCssCatalog = null;

	/**
	 * Инициализация переменных и подключение библиотеки
	 *
	 * @api
	 */
	public static function init()
	{
		if (is_null(static::$coreRoot))
		{
			static::$coreRoot = Config::getConfig('CORE_ROOT');
		}
		if (is_null(static::$mainJs))
		{
			static::$mainJs = static::$coreRoot.'plugins/webix/codebase/webix.js';
		}
		if (is_null(static::$mainCss))
		{
			static::$mainCss = static::$coreRoot.'plugins/webix/codebase/webix.css';
		}
		if (is_null(static::$otherCssCatalog))
		{
			static::$otherCssCatalog = static::$coreRoot.'plugins/webix/codebase/css/';
		}
		//if
		Buffer::addJS(static::$mainJs);
		Buffer::addCSS(static::$mainCss);
	}

	public static function showDataTable ($arData=null)
	{
		try
		{
			if (is_null($arData))
			{
				throw new Exception\ArgumentNullException('arData');
			}
			elseif (!is_array($arData))
			{
				throw new Exception\ArgumentTypeException('arData');
			}
			else
			{
				if (!isset($arData['grid']))
				{
					throw new Exception\ArgumentNullException ('arData[grid]');
				}
				if (!isset($arData['container']))
				{
					throw new Exception\ArgumentNullException ('arData[container');
				}
				if (!isset($arData['columns']))
				{
					throw new Exception\ArgumentNullException ('arData[columns]');
				}
				if (!isset($arData['data']))
				{
					throw new Exception\ArgumentNullException ('arData[data]');
				}
			}
		}
		catch (Exception\ArgumentNullException $e1)
		{
			die($e1->showException());
		}
		catch (Exception\ArgumentTypeException $e2)
		{
			die($e2->showException());
		}

		static::init();
		$func = 'winYandexMap = webix.ui({'."\n"
			."\t".'view:"popup",'."\n"
			."\t".'height:450,'."\n"
			."\t".'width:600,'."\n"
			."\t".'position:"center",'."\n"
			."\t".'body:{'."\n"
			."\t\t".'template: ""'."\n"
			."\t".'}'."\n"
			.'});'."\n";
		Buffer::addWebixJs($func,"winYandexMap");

		$webixJS = trim($arData['grid'])." = webix.ui({\n\t";
		$webixJS.= 'container:"'.trim($arData['container']).'",'."\n\t"
			.'view:"datatable",'."\n\t";
		if (isset($arData['id']))
		{
			$webixJS.="id:'".trim($arData['id']).",\n\t";
		}
		if (!isset($arData['autoheight']) || $arData['autoheight'])
		{
			$webixJS.="autoheight:true,\n\t";
		}
		if (!isset($arData['autowidth']) || $arData['autowidth'])
		{
			$webixJS.="autowidth:true,\n\t";
		}
		if ($arData['editable'])
		{
			$webixJS.="editable:true,\n\t"
				.'editaction:"dblclick",'."\n\t";
		}
		if (isset($arData['leftSplit']) && intval($arData['leftSplit'])>0)
		{
			$webixJS.='leftSplit:'.$arData['leftSplit'].",\n\t";
		}
		if (isset($arData['rightSplit']) && intval($arData['rightSplit'])>0)
		{
			$webixJS.='rightSplit:'.$arData['rightSplit'].",\n\t";
		}
		if (isset($arData['minHeight']) && intval($arData['minHeight'])>0)
		{
			$webixJS.='minHeight:'.$arData['minHeight'].",\n\t";
		}
		else
		{
			$webixJS.='minHeight:50,'."\n\t";
		}
		if (isset($arData['footer']) && $arData['footer'])
		{
			$webixJS.="footer:true,\n\t";
		}
		if (isset($arData['tooltip']) && $arData['tooltip'])
		{
			$webixJS.="tooltip:true,\n\t";
		}
		if (isset($arData['pager']))
		{
			$webixJS.="pager:{\n\t\t".'template:"';
			//Первая страница
			if (!isset($arData['pager']['first']) || $arData['pager']['first'])
				$webixJS.='{common.first()} ';
			//Предыдущая страница
			if (!isset($arData['pager']['prev']) || $arData['pager']['prev'])
				$webixJS.='{common.prev()} ';
			//Страницы
			$webixJS.='{common.pages()} ';
			//Следущая
			if (!isset($arData['pager']['next']) || $arData['pager']['next'])
				$webixJS.='{common.next()} ';
			//Последняя страница
			if (!isset($arData['pager']['last']) || $arData['pager']['last'])
				$webixJS.='{common.last()}';

			if (!isset($arData['pager']['container']))
			{
				//$arData['pager']['container'] = "pagination";
				$arData['pager']['container'] = trim($arData['container']);
			}
			if (!isset($arData['pager']['size']) || intval($arData['pager']['size'])<=0)
			{
				$arData['pager']['size'] = 20;
			}
			if (!isset($arData['pager']['group']) || intval($arData['pager']['group'])<=0)
			{
				$arData['pager']['group'] = 5;
			}

			$webixJS.='",'."\n\t\t"
				.'container:"'.$arData['pager']['container']
				.'",'."\n\t\t".'size:'.$arData['pager']['size']
				.','."\n\t\t".'group:'.$arData['pager']['group']
				."\n\t},\n\t";
		}
		if (isset($arData['width']) && $arData['width']>0)
		{
			$webixJS.='width:'.$arData['width'].','."\n\t";
		}
			//."activeContent:{\n"
			//."deleteButton:{\n"
			//.'id:"deleteButtonId",'."\n".'view:"button",'."\n"
			//.'label:"Delete",'."\n".'width:50,click:deleteClick},'."\n"
			//."editButton:{\n"
			//.'id:"editButtonId",'."\n".'view:"button",'."\n"
			//.'label:"Edit",'."\n".'width:50,click:editClick}},'."\n"
		$webixJS.="on:{\n\t\tonAfterLoad:function(){\n\t\t\tif (!this.count())\n\t\t\t\t"
			.'this.showOverlay("'.Loc::getPackMessage('core','webix_no_data_to_view').'");'."\n\t\t"
			."}\n\t},\n\t";
/*		$webixJS.="on:{\n"
			.'"onItemClick":function(id, e, trg){'."\n"
			//id.column - column id
			//id.row - row id
			.'if (id.column=="point_name") {'
			.'webix.message("Click on row: " + id.row+", column: " + id.column);'."\n"
			.'var item = fuelGrid.getItem(id.row); '
			.'var lat = item.point_latitude; '
			.'var long = item.point_longitude; '
			.'var yandexMap = item.yandex_map; ';
			//.'console.log(lat);'
			//.'window.open("https://static-maps.yandex.ru/1.x/?l=map&z=12&size=600,450&pt="+long+","+lat+",pm2blm");'
		$webixJS.='winYandexMap.body = {template:yandexMap};';
			//.'console.log(winYandexMap);'
		$webixJS.='console.log(winYandexMap.body);';
		$webixJS.='winYandexMap.show();'
			."}}},\n";*/
		$webixJS.="columns:[\n";
		$bFirst = true;
		foreach ($arData['columns'] as $arColumns)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$webixJS.=",\n";
			}
			$webixJS.="\t\t{\n\t\t\t";

			$bFFirst = true;
			foreach ($arColumns as $key=>$value)
			{
				if ($bFFirst)
				{
					$bFFirst = false;
				}
				else
				{
					$webixJS.=",\n\t\t\t";
				}
				$first = substr($value,0,1);
				if ($first=='=')
				{
					$count = strlen($value);
					$value = substr($value,1,$count-1);
					$webixJS.= $key.":".$value;
				}
				else
				{
					$webixJS.= $key.':"'.$value.'"';
				}
			}

			$webixJS.="\n\t\t}";
		}
		$webixJS.= "\n\t],\n\t"."data:[\n";
		$bFirst = true;
		foreach ($arData['data'] as $arDat)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$webixJS.= ",\n";
			}
			$webixJS.= "\t\t{\n\t\t\t";

			$bFFirst = true;
			foreach ($arDat as $key=>$value)
			{
				if ($bFFirst)
				{
					$bFFirst = false;
				}
				else
				{
					$webixJS.= ",\n\t\t\t";
				}
				$first = substr($value,0,1);
				if ($first=='=')
				{
					$count = strlen($value);
					$value = substr($value,1,$count-1);
					$webixJS.= $key.":".$value;
				}
				else
				{
					$webixJS.= $key.':"'.$value.'"';
				}
			}

			$webixJS.= "\n\t\t}";
		}
		$webixJS.= "\n\t]\n";

		$webixJS.= "});\n";

		Buffer::addWebixJs($webixJS, $arData['grid']);
		Buffer::addCSS(static::$otherCssCatalog.'samples.css');
	}

}