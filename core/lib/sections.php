<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

//http://www.getinfo.ru/article610.html
namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;

class Sections
{
	protected static $tableName = "ms_core_sections";
	protected static $selectFields = array('ID','ACTIVE','SORT','NAME','LEFT_MARGIN','RIGHT_MARGIN','DEPTH_LEVEL','PARENT_SECTION_ID');

	/**
	 * getTableName - функция возвращает значение параметра класса, содержащую имя таблицы
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return static::$tableName;
	}

	/**
	 * getClassName - функция возвращает название класса по имени таблицы, используя функцию Tools::getClassNameByTableName('table')
	 *
	 * @return string
	 */
	public static function getClassName()
	{
		return Tools::getClassNameByTableName(static::getTableName());
	}

	/**
	 * getSelectFields - функция возвращает значение параметра класса, содержащую массив полей таблицы
	 *
	 * @return array
	 */
	public static function getSelectFields ()
	{
		return static::$selectFields;
	}

	/**
	 * getTreeList - функция возвращает массив дерева каталогов, либо FALSE
	 *
	 * @param bool $bActive - выбрать только активные разделы
	 *
	 * @return bool|array
	 */
	public static function getTreeList ($bActive=false)
	{
		$className = static::getClassName();
		$arGetList = array(
			'select' => static::$selectFields,
			'order' => array('LEFT_MARGIN' => 'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array('ACTIVE'=>true);
		}

		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * getInfo - функция возвращает массив параметров раздела по его ID, либо FALSE
	 *
	 * @param int $ID - ID раздела
	 *
	 * @return bool|array
	 */
	public static function getInfo ($ID)
	{
		$className = static::getClassName();
		$arResult = $className::getList(array(
			'select' => static::$selectFields,
			'filter' => array('ID'=>intval($ID))
		));
		if ($arResult)
		{
			return $arResult[0];
		}
		else
		{
			return false;
		}
	}

	/**
	 * getChild - функция возвращает массив дочерних разделов указанного раздела, либо FALSE
	 *
	 * @param int  $ID          ID раздела
	 * @param int  $DEPTH_LEVEL уровень вложенности
	 * @param bool $bActive     вывести только активные разделы
	 *
	 * @return bool
	 */
	public static function getChild ($ID, $bActive=false, $DEPTH_LEVEL=0)
	{
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::$selectFields,
			'filter' => array('>=LEFT_MARGIN'=>$arSection['LEFT_MARGIN'],'<=RIGHT_MARGIN'=>$arSection['RIGHT_MARGIN']),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'], array('ACTIVE'=>true));
		}
		if (intval($DEPTH_LEVEL)>0)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('DEPTH_LEVEL'=>intval($DEPTH_LEVEL)));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * getParent - возвращает массив родителей раздела, либо FALSE
	 *
	 * @param int  $ID          ID раздела
	 * @param bool $bActive     вернуть только активные разделы
	 *
	 * @return bool|array
	 */
	public static function getParent ($ID, $bActive=false)
	{
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::$selectFields,
			'filter' => array('<=LEFT_MARGIN'=>$arSection['LEFT_MARGIN'],'>=RIGHT_MARGIN'=>$arSection['RIGHT_MARGIN']),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('ACTIVE'=>true));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * getBranch - возвращает всю ветку, в которой участвует раздел, либо FALSE
	 *
	 * @param int  $ID          ID раздела
	 * @param bool $bActive     вернуть только активные разделы
	 *
	 * @return bool|array
	 */
	public static function getBranch ($ID, $bActive=false)
	{
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::$selectFields,
			'filter' => array('>RIGHT_MARGIN'=>$arSection['LEFT_MARGIN'],'<LEFT_MARGIN'=>$arSection['RIGHT_MARGIN']),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('ACTIVE'=>true));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * getParentID - возвращает ID родительского раздела, либо FALSE
	 *
	 * @param int $ID       ID раздела
	 *
	 * @return bool|int
	 */
	public static function getParentID ($ID)
	{
		$arSection = static::getInfo($ID);
		if (isset($arSection['PARENT_SECTION_ID']))
		{
			return $arSection['PARENT_SECTION_ID'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * getParentInfo - возвращает массив параметров родительского раздела, либо FALSE
	 *
	 * @param int $ID        ID раздела
	 *
	 * @return array|bool
	 */
	public static function getParentInfo ($ID)
	{
		if ($arSection = static::getInfo(static::getParentID($ID)))
		{
			return $arSection;
		}
		else
		{
			return false;
		}
	}


	public static function addSection ($arSection)
	{
		/*		Создание узла – самое простое действие над деревом. Для того, что бы его осуществить нам потребуется уровень и
				правый ключ родительского узла (узел в который добавляется новый), либо максимальный правый ключ, если у
				нового узла не будет родительского.*/

		$helper = new SqlHelper();
		$className = static::getClassName();
		if (!$arSection = self::checkFields($arSection))
		{
			return false;
		}

/*		Пусть $right_key – правый ключ родительского узла, или максимальный правый ключ плюс единица (если
		родительского узла нет, то узел с максимальным правым ключом не будет обновляться, соответственно, чтобы небыло
		повторов, берем число на единицу большее). $level – уровень родительского узла, либо 0, если родительского нет.*/
		if ($arSection['PARENT_SECTION_ID']==0)
		{
			$query = new Query('select');
			$sql = "SELECT\n\tMAX(".$helper->wrapQuotes('RIGHT_MARGIN').") as RIGHT_MARGIN\nFROM\n\t"
				.$helper->wrapQuotes(static::getTableName());
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			if ($ar_res = $res->fetch())
			{
				$right_key = $ar_res['RIGHT_MARGIN'] + 1;
				$level = 0;
			}
		}
		else
		{
			$arParent = static::getInfo($arSection['PARENT_SECTION_ID']);
			$right_key = $arParent['RIGHT_MARGIN'];
			$level = $arParent['DEPTH_LEVEL'];

			//1. Обновляем ключи существующего дерева, узлы стоящие за родительским узлом:
			$query = new Query('update');
			$sql = "UPDATE\n\t".$helper->wrapQuotes(static::getTableName())."\nSET\n\t".$helper->wrapQuotes('LEFT_MARGIN')
				." = ".$helper->wrapQuotes('LEFT_MARGIN')." + 2,\n\t".$helper->wrapQuotes('RIGHT_MARGIN')
				." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + 2\nWHERE\n\t".$helper->wrapQuotes('LEFT_MARGIN')." > ".$right_key;
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
/*			Но мы обновили только те узлы в которых изменяются оба ключа, при этом родительскую ветку (не узел, а все
			родительские узлы) мы не трогали, так как в них изменяется только правый ключ. Следует иметь в виду, что
			если у нас не будет родительского узла, то есть новый узел будет корневым, то данное обновление проводить
			нельзя.*/
		}

		//2. Обновляем родительскую ветку:
		$query = new Query('update');
		$sql = "UPDATE\n\t".$helper->wrapQuotes(static::getTableName())."\nSET\n\t".$helper->wrapQuotes('RIGHT_MARGIN')
			." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + 2\nWHERE\n\t".$helper->wrapQuotes('RIGHT_MARGIN')." >= "
			.$right_key."\n\tAND\n\t".$helper->wrapQuotes('LEFT_MARGIN')." < ".$right_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		//3. Теперь добавляем новый узел :
		$query = new Query('insert');
		$arSection['LEFT_MARGIN'] = $right_key;
		$arSection['RIGHT_MARGIN'] = $right_key + 1;
		$arSection['DEPTH_LEVEL'] = $level + 1;
		if (isset($arSection['ID']))
		{
			unset($arSection['ID']);
		}
		$query->setInsertParams($arSection,static::getTableName(),$className::getMapArray());
		$res = $query->exec();
		$insertID = $res->getInsertId();
		$arSection['ID'] = $insertID;
		static::sortAddingSection($arSection);

		if ($insertID > 0)
		{
			return $insertID;
		}
		else
		{
			return false;
		}
	}

	public static function sortAddingSection ($arSection)
	{
		//msDebug($arSection);
		$helper = new SqlHelper();
		$className = static::getClassName();
		$tableName = static::getTableName();

		//1. Ключи и уровень перемещаемого узла
		if ($arSection['PARENT_SECTION_ID'] > 0)
		{
			$arParent = static::getInfo(intval($arSection['PARENT_SECTION_ID']));
			$arParams = array(
				'LEFT_MARGIN' => $arParent['LEFT_MARGIN'],
				'RIGHT_MARGIN' => $arParent['RIGHT_MARGIN'],
			);
		}
		else
		{
			$query = new Query('select');
			$sql = "SELECT\n\tMAX(".$helper->wrapQuotes('RIGHT_MARGIN').") as MAX_RIGHT_MARGIN\nFROM\n\t".$helper->wrapQuotes($tableName)
				."\nWHERE\n\t".$helper->wrapQuotes('PARENT_SECTION_ID')." = 0";
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			$ar_res = $res->fetch();
			$arParams = array(
				'LEFT_MARGIN' => 0,
				'RIGHT_MARGIN' => $ar_res['MAX_RIGHT_MARGIN'] + 1,
			);
		}
		$arSort = $className::getList(array(
			'select' => static::getSelectFields(),
			'filter' => array('PARENT_SECTION_ID' => $arSection['PARENT_SECTION_ID']),
			'order' => array('SORT'=>'ASC', 'NAME'=>'ASC')
		));
		//msDebug($arSort);
		foreach ($arSort as $key=>$sort)
		{
			if ($sort['ID']!=$arSection['ID'])
			{
				$arParams['LEFT_MARGIN'] = $sort['RIGHT_MARGIN'];
			}
			else
			{
				break;
			}
		}
		/*
		 * $arParams['LEFT_MARGIN']
		 * $arParams['RIGHT_MARGIN']
		 */
		//msDebug($arParams);
		$query = new Query('update');
		$sql = "UPDATE\n\t".$helper->wrapQuotes($tableName)."\nSET\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." = ".($arParams['LEFT_MARGIN']+1).",\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = ".($arParams['LEFT_MARGIN']+2)."\nWHERE\n\t"
			.$helper->wrapQuotes('ID')." = ".$arSection['ID'];
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		$query = new Query('update');
		$sql = "UPDATE\n\t".$helper->wrapQuotes($tableName)."\nSET\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + 2,\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + 2\nWHERE\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." > ".$arParams['LEFT_MARGIN']."\n\tAND\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." < ".$arParams['RIGHT_MARGIN']."\n\tAND\n\t"
			.$helper->wrapQuotes('ID')." <> ".$arSection['ID'];
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
	}

	public static function deleteSection ($sectionID)
	{
/*		Удаление узла не намного сложнее, но требуется учесть, что у удаляемого узла могут быть подчиненные узлы. Для
		осуществления этого действия нам потребуется левый и правый ключ удаляемого узла.*/

		$tableName = static::getTableName();
		$helper = new SqlHelper();

		//Пусть $left_key – левый ключ удаляемого узла, а $right_key – правый
		$arSection = static::getInfo($sectionID);
		$left_key = $arSection['LEFT_MARGIN'];
		$right_key = $arSection['RIGHT_MARGIN'];

		//1. Удаляем узел (ветку)
		$query = new Query('delete');
		/*
		DELETE FROM
			tableName
		WHERE
			`LEFT_MARGIN` >= $left_key
			AND
			`RIGHT_MARGIN` <= $right_key
		 */
		$sql = "DELETE FROM\n\t".$helper->wrapQuotes($tableName)."\nWHERE\n\t ".$helper->wrapQuotes('LEFT_MARGIN')." >= "
			.$left_key."\n\tAND\n\t".$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		//2. Обновляем ключи оставшихся веток:
/*		Как и в случае с добавлением обновление происходит двумя командами: обновление ключей родительской ветки и
		обновление ключей узлов, стоящих за родительской веткой. Следует правда учесть, что обновление будет
		производиться в другом порядке, так как ключи у нас уменьшаются.*/
		//2.1. Обновление родительской ветки :
		$query = new Query('update');
		/*
		UPDATE
			tableName
		SET
			`RIGHT_MARGIN` = `RIGHT_MARGIN` – ($right_key - $left_key + 1)
		WHERE
			`RIGHT_MARGIN` > $right_key
			AND
			`LEFT_MARGIN` < $left_key
		 */
		$sql = "UPDATE\n\t".$helper->wrapQuotes($tableName)."\nSET\n\t".$helper->wrapQuotes('RIGHT_MARGIN')." = "
			.$helper->wrapQuotes('RIGHT_MARGIN')." – (".$right_key." - ".$left_key." + 1)\nWHERE\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." > ".$right_key."\n\tAND\n\t".$helper->wrapQuotes('LEFT_MARGIN')." < "
			.$left_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		//2.2. Обновление последующих узлов :
		$query = new Query('update');
		/*
		UPDATE
			tableName
		SET
			`LEFT_MARGIN` = `LEFT_MARGIN` – ($right_key - $left_key + 1),
			`RIGHT_MARGIN` = `RIGHT_MARGIN` – ($right_key - $left_key + 1)
		WHERE
			`LEFT_MARGIN` > $right_key
		 */
		$sql = "UPDATE\n\t".$helper->wrapQuotes($tableName)."\nSET\n\t".$helper->wrapQuotes('LEFT_MARGIN')." = "
			.$helper->wrapQuotes('LEFT_MARGIN')." – (".$right_key." - ".$left_key." + 1),\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." – (".$right_key." - "
			.$left_key." + 1)\nWHERE\n\t".$helper->wrapQuotes('LEFT_MARGIN')." > ".$right_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		//3. Проверяем.

/*		//Теперь можно объединить последние два запроса в один, что бы не делать лишних действий.
		$query = new Query('update');
		/*
		UPDATE
			tableName
		SET
			`LEFT_MARGIN` = IF(`LEFT_MARGIN` > $left_key, `LEFT_MARGIN` – ($right_key - $left_key + 1), `LEFT_MARGIN`),
			`RIGHT_MARGIN` = `RIGHT_MARGIN` – ($right_key - $left_key + 1)
		WHERE
			`RIGHT_MARGIN` > $right_key
		 *
		$sql = "UPDATE\n\t".$helper->wrapQuotes($tableName)."\nSET\n\t".$helper->wrapQuotes('LEFT_MARGIN')." = IF("
			.$helper->wrapQuotes('LEFT_MARGIN')." > ".$left_key.", ".$helper->wrapQuotes('LEFT_MARGIN')." – ("
			.$right_key." - ".$left_key." + 1), ".$helper->wrapQuotes('LEFT_MARGIN')."),\n\t "
			.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." – (".$right_key." - "
			.$left_key." + 1)\nWHERE\n\t".$helper->wrapQuotes('RIGHT_MARGIN')." > ".$right_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();*/
	}

	public static function deactivateSection($sectionID)
	{
		$className = static::getClassName();
		$tableName = static::getTableName();
		$arUpdate = array('ACTIVE'=>false);
		$query = new Query('update');
		$query->setUpdateParams($arUpdate,$sectionID,$tableName,$className::getMapArray());
		$res = $query->exec();

		return $res;
	}

	public static function activateSection ($sectionID)
	{
		$className = static::getClassName();
		$tableName = static::getTableName();
		$arUpdate = array('ACTIVE'=>true);
		$query = new Query('update');
		$query->setUpdateParams($arUpdate,$sectionID,$tableName,$className::getMapArray());
		$res = $query->exec();

		return $res;
	}

	/*
	public static function moveSection ($arSection)
	{
		msDebug($arSection);
		$helper = new SqlHelper();
		$className = static::getClassName();
		$tableName = static::getTableName();

		//1. Ключи и уровень перемещаемого узла
		$level = $arSection['DEPTH_LEVEL'];
		$left_key = $arSection['LEFT_MARGIN'];
		$right_key = $arSection['RIGHT_MARGIN'];

		//2. Уровень нового родительского узла (если узел перемещается в "корень" то сразу можно подставить значение 0)
		if ($arSection['PARENT_SECTION_ID'] > 0)
		{
			$arParent = static::getInfo($arSection['PARENT_SECTION_ID']);
			$level_up = $arParent['DEPTH_LEVEL'];
			$near_key = $arParent['LEFT_MARGIN'];
		}
		else
		{
			$level_up = 0;
			$near_key = 0;
		}

		//3. Правый ключ узла за который мы вставляем узел (ветку)
/*		Данный параметр, а не ключи нового родительского узла, выбираем по одной простой причине: Для обычного
		перемещения этого ключа нам будет достаточно, а при изменении порядка узлов и переноса в "корень" дерева
		данный параметр нам просто необходим.*

		// * При изменении порядка, когда родительский узел не меняется – правый ключ узла за которым будет стоять перемещаемый;
		//SELECT left_key, right_key FROM my_tree WHERE id = [id соседнего узла с который будет(!) выше (левее)]****
		//Следует обратить внимание, что при поднятии узла вверх по порядку, узел выбирается не соседний, а следующий,
		//за неимением оного (перемещаемый узел будет первым) берется левый ключ родительского узла
		//Получаем $right_key_near и $left_key_near (для варианта изменения порядка)
		$arSort = $className::getList(array(
			'select' => static::getSelectFields(),
			'filter' => array('PARENT_SECTION_ID' => $arSection['PARENT_SECTION_ID']),
			'order' => array('SORT'=>'ASC', 'NAME'=>'ASC')
		));
		msDebug($arSort);
		foreach ($arSort as $key=>$sort)
		{
			if ($sort['ID']!=$arSection['ID'])
			{
				$near_key = $sort['RIGHT_MARGIN'];
			}
			else
			{
				break;
			}
		}
		msDebug($near_key);

		//4. Определяем смещения:
		//$level_up - $level + 1 = $skew_level - смещение уровня изменяемого узла;
		$skew_level = $level_up - $level + 1;
		//$right_key - $left_key + 1 = $skew_tree - смещение ключей дерева;
		$skew_tree = $right_key - $left_key + 1;

		//Выбираем все узлы перемещаемой ветки:
		$query = new Query('select');
		$sql = "SELECT ".$helper->wrapQuotes('ID')." FROM ".$helper->wrapQuotes($tableName)
			." WHERE ".$helper->wrapQuotes('LEFT_MARGIN')." >= ".$left_key." AND ".$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		while($ar_res = $res->fetch())
		{
			$arIdEdit[] = $ar_res['ID'];
		}
		$id_edit = join(', ',$arIdEdit);
		msDebug($id_edit);

		//Так же требуется определить: в какую область перемещается узел, для этого сравниваем $right_key и
		//$right_key_near, если $right_key_near больше, то узел перемещается в облась вышестоящих узлов,
		//иначе - нижестоящих
		if ($near_key > $right_key)
		{
			//Перемещаем вверх
			msDebug('Перемещаем вверх');
			//Определяем смещение ключей редактируемого узла $right_key_near - $left_key + 1 = $skew_edit;
			$skew_edit = $near_key - $left_key + 1;

			$query = new Query('update');
			/*
			UPDATE
				my_tree
			SET
				`RIGHT_MARGIN` = `RIGHT_MARGIN` + $skew_tree
			WHERE
				`RIGHT_MARGIN` < $left_key
				AND
				`RIGHT_MARGIN` > $right_key_near
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('RIGHT_MARGIN')
				." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + ".$skew_tree." WHERE "
				.$helper->wrapQuotes('RIGHT_MARGIN')." < ".$left_key." AND ".$helper->wrapQuotes('RIGHT_MARGIN')
				." > ".$near_key;
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`LEFT_MARGIN` = `LEFT_MARGIN` + $skew_tree
			WHERE
				`LEFT_MARGIN` < $left_key
				AND
				`LEFT_MARGIN` > $right_key_near
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('LEFT_MARGIN')
				." = ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$skew_tree." WHERE ".$helper->wrapQuotes('LEFT_MARGIN')
				." < ".$left_key." AND ".$helper->wrapQuotes('LEFT_MARGIN')." > ".$near_key;
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			//Теперь можно переместить ветку:
			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`LEFT_MARGIN` = `LEFT_MARGIN` + $skew_edit,
				`RIGHT_MARGIN` = `RIGHT_MARGIN` + $skew_edit,
				`DEPTH_LEVEL` = `DEPTH_LEVEL` + $skew_level
			WHERE
				`ID` IN ($id_edit)
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('LEFT_MARGIN')." = "
				.$helper->wrapQuotes('LEFT_MARGIN')." + ".$skew_edit.", ".$helper->wrapQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapQuotes('RIGHT_MARGIN')." + ".$skew_edit.", ".$helper->wrapQuotes('DEPTH_LEVEL')." = "
				.$helper->wrapQuotes('DEPTH_LEVEL')." + ".$skew_level." WHERE ".$helper->wrapQuotes('ID')
				." IN (".$id_edit.")";
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			/*
			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`RIGHT_MARGIN` = IF(`LEFT_MARGIN` >= $left_key, `RIGHT_MARGIN` + $skew_edit, IF(`RIGHT_MARGIN` < $left_key, `RIGHT_MARGIN` + $skew_tree, `RIGHT_MARGIN`)),
				`DEPTH_LEVEL` = IF(`LEFT_MARGIN` >= $left_key, `DEPTH_LEVEL` + $skew_level, `DEPTH_LEVEL`),
				`LEFT_MARGIN` = IF(`LEFT_MARGIN` >= $left_key, `LEFT_MARGIN` + $skew_edit, IF(`LEFT_MARGIN` > $right_key_near, `LEFT_MARGIN` + $skew_tree, `LEFT_MARGIN`))
			WHERE
				`RIGHT_MARGIN` > $right_key_near
				AND
				`LEFT_MARGIN` < $right_key
			*
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('RIGHT_MARGIN')." = IF("
				.$helper->wrapQuotes('LEFT_MARGIN')." >= ".$left_key.", ".$helper->wrapQuotes('RIGHT_MARGIN')." + "
				.$skew_edit.", IF(".$helper->wrapQuotes('RIGHT_MARGIN')." < ".$left_key.", "
				.$helper->wrapQuotes('RIGHT_MARGIN')." + ".$skew_tree.", ".$helper->wrapQuotes('RIGHT_MARGIN').")), "
				.$helper->wrapQuotes('DEPTH_LEVEL')." = IF(".$helper->wrapQuotes('LEFT_MARGIN')." >= ".$left_key.", "
				.$helper->wrapQuotes('DEPTH_LEVEL')." + ".$skew_level.", ".$helper->wrapQuotes('DEPTH_LEVEL')."), "
				.$helper->wrapQuotes('LEFT_MARGIN')." = IF(".$helper->wrapQuotes('LEFT_MARGIN')." >= ".$left_key.", "
				.$helper->wrapQuotes('LEFT_MARGIN')." + ".$skew_edit.", IF(".$helper->wrapQuotes('LEFT_MARGIN')." > "
				.$near_key.", ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$skew_tree.", "
				.$helper->wrapQuotes('LEFT_MARGIN').")) WHERE ".$helper->wrapQuotes('RIGHT_MARGIN')." > "
				.$near_key." AND ".$helper->wrapQuotes('LEFT_MARGIN')." < ".$right_key;
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			*
/*			В данной команде особое внимание нужно уделить порядку изменения полей таблицы, самым последним полем должно
			изменяться поле левого ключа (left_key), так как его значение является условием для изменения других полей.
			Замечу, что при использовании этой команды, выбирать узлы перемещаемой ветки не нужно.*

		}
		else
		{
			//Перемещаем вниз
			msDebug('Перемещаем вниз');
/*			Опять же порядок не имеет значения, поэтому просто делаем команды на обновление. Правда хочу обратить
			внимание на тот факт, что в условии: "левый ключ узла меньше $near_key" узел в котором находится
			$near_key тоже попадает в диапазон изменения, следует иметь ввиду, что при сравнении не однотипных
			ключей (правый <-> левый) текущий узел попадает или не попадает в диапазон без использования равенства в
			условии.*
			//Определяем смещение ключей редактируемого узла $right_key_near - $left_key + 1 - $skew_tree = $skew_edit.
			$skew_edit = $near_key - $left_key + 1 - $skew_tree;

			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`RIGHT_MARGIN` = `RIGHT_MARGIN` - $skew_tree
			WHERE
				`RIGHT_MARGIN` > $right_key
				AND
				`RIGHT_MARGIN` <= $right_key_near
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapQuotes('RIGHT_MARGIN')." - ".$skew_tree." WHERE ".$helper->wrapQuotes('RIGHT_MARGIN')
				." > ".$right_key." AND ".$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$near_key;
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`LEFT_MARGIN` = `LEFT_MARGIN` - $skew_tree
			WHERE
				`LEFT_MARGIN` < $left_key
				AND
				`LEFT_MARGIN` > $right_key_near
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('LEFT_MARGIN')." = "
				.$helper->wrapQuotes('LEFT_MARGIN')." - ".$skew_tree." WHERE ".$helper->wrapQuotes('LEFT_MARGIN')
				." < ".$left_key." AND ".$helper->wrapQuotes('LEFT_MARGIN')." > ".$near_key;
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			//Теперь можно переместить ветку:
			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`LEFT_MARGIN` = `LEFT_MARGIN` + $skew_edit,
				`RIGHT_MARGIN` = `RIGHT_MARGIN` + $skew_edit,
				`DEPTH_LEVEL` = `DEPTH_LEVEL` + $skew_level
			WHERE
				`ID` IN ($id_edit)
			 *
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('LEFT_MARGIN')." = "
				.$helper->wrapQuotes('LEFT_MARGIN')." + ".$skew_edit.", ".$helper->wrapQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapQuotes('RIGHT_MARGIN')." + ".$skew_edit.", ".$helper->wrapQuotes('DEPTH_LEVEL')." = "
				.$helper->wrapQuotes('DEPTH_LEVEL')." + ".$skew_level." WHERE ".$helper->wrapQuotes('ID')
				." IN (".$id_edit.")";
			$query->setQueryBuildParts($sql);
			//$res = $query->exec();

			//После оптимизации этих запросов получаем всего один:
/*			$query = new Query('update');
			/*
			UPDATE
				tableName
			SET
				`LEFT_MARGIN` = IF(`RIGHT_MARGIN` <= $right_key, `LEFT_MARGIN` + $skew_edit, IF(`LEFT_MARGIN` > $right_key, `LEFT_MARGIN` - $skew_tree, `LEFT_MARGIN`)),
				`DEPTH_LEVEL` = IF(`RIGHT_MARGIN` <= $right_key, `DEPTH_LEVEL` + $skew_level, `DEPTH_LEVEL`),
				`RIGHT_MARGIN` = IF(`RIGHT_MARGIN` <= $right_key, `RIGHT_MARGIN` + $skew_edit, IF(`RIGHT_MARGIN` <= $right_key_near, `RIGHT_MARGIN` - $skew_tree, `RIGHT_MARGIN`))
			WHERE
				`RIGHT_MARGIN` > $left_key
				AND
				`LEFT_MARGIN` <= $right_key_near
			*
			$sql = "UPDATE ".$helper->wrapQuotes($tableName)." SET ".$helper->wrapQuotes('LEFT_MARGIN')." = IF("
				.$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key.", ".$helper->wrapQuotes('LEFT_MARGIN')." + "
				.$skew_edit.", IF(".$helper->wrapQuotes('LEFT_MARGIN')." > ".$right_key.", "
				.$helper->wrapQuotes('LEFT_MARGIN')." - ".$skew_tree.", ".$helper->wrapQuotes('LEFT_MARGIN').")), "
				.$helper->wrapQuotes('DEPTH_LEVEL')." = IF(".$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key.", "
				.$helper->wrapQuotes('DEPTH_LEVEL')." + ".$skew_level.", ".$helper->wrapQuotes('DEPTH_LEVEL')."), "
				.$helper->wrapQuotes('RIGHT_MARGIN')." = IF(".$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key.", "
				.$helper->wrapQuotes('RIGHT_MARGIN')." + ".$skew_edit.", IF(".$helper->wrapQuotes('RIGHT_MARGIN')." <= "
				.$near_key.", ".$helper->wrapQuotes('RIGHT_MARGIN')." - ".$skew_tree.", ".$helper->wrapQuotes('RIGHT_MARGIN')
				.")) WHERE ".$helper->wrapQuotes('RIGHT_MARGIN')." > ".$left_key." AND ".$helper->wrapQuotes('LEFT_MARGIN')
				." <= ".$near_key;
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			//Замечания те же, что и при перемещении ветки вверх по дереву.*

		}

	}
	*/

	private static function checkFields ($arSection=null)
	{
		if (is_null($arSection) || !isset($arSection['NAME']) || strlen($arSection['NAME'])<=0)
		{
			return false;
		}
		if (!isset($arSection['ACTIVE']) || !is_bool($arSection['ACTIVE']))
		{
			$arSection['ACTIVE'] = true;
		}
		if (!isset($arSection['PARENT_SECTION_ID']) || intval($arSection['PARENT_SECTION_ID'])<0)
		{
			$arSection['PARENT_SECTION_ID'] = 0;
		}
		if (!isset($arParams['SORT']) || intval($arParams['SORT'])<=0)
		{
			$arParams['SORT'] = 500;
		}

		return $arSection;
	}

	public static function checkTable ()
	{
		/* ОСНОВНЫЕ ПРАВИЛА ХРАНЕНИЯ ДЕРЕВА КАТАЛОГОВ
		 *
		 * 1. Левый ключ ВСЕГДА меньше правого;
		 * 2. Наименьший левый ключ ВСЕГДА равен 1;
		 * 3. Наибольший правый ключ ВСЕГДА равен двойному числу узлов;
		 * 4. Разница между правым и левым ключом ВСЕГДА нечетное число;
		 * 5. Если уровень узла нечетное число то тогда левый ключ ВСЕГДА нечетное число, то же самое и для четных чисел;
		 * 6. Ключи ВСЕГДА уникальны, вне зависимости от того правый он или левый;
		 */
		$bError = false;
		//$className = static::getClassName();
		//TODO: Невозможно сравнивать с полем. Нужно доделать, чтобы можно было. См. коммент ниже
		/*
		$res1 = $className::getList(array(
			'select' => array('ID'),
			'filter' => array('LEFT_MARGIN'=>'RIGHT_MARGIN')
		));
		*/
		$helper = new SqlHelper();
		//1. Левый ключ ВСЕГДА меньше правого;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$sql1 = "SELECT ".$helper->wrapQuotes('ID')." FROM ".$helper->wrapQuotes(static::getTableName())." WHERE "
			.$helper->wrapQuotes('LEFT_MARGIN')." >= ".$helper->wrapQuotes('RIGHT_MARGIN');
		$query1 = new Query('select');
		$query1->setQueryBuildParts($sql1);
		$res1 = $query1->exec();
		if ($res1->getResult())
		{
			while ($ar_res1 = $res1->fetch())
			{
				$arResult['RULE1'][] = $ar_res1;
			}
			$bError = true;
		}

		//2. Наименьший левый ключ ВСЕГДА равен 1;
		//3. Наибольший правый ключ ВСЕГДА равен двойному числу узлов;
		//Получаем количество записей (узлов), минимальный левый ключ и максимальный правый ключ, проверяем значения.
		$sql2 = "SELECT COUNT(".$helper->wrapQuotes('ID').") as COUNT, MIN(".$helper->wrapQuotes('LEFT_MARGIN')
			.") as MIN, MAX(".$helper->wrapQuotes('RIGHT_MARGIN').") as MAX FROM ".$helper->wrapQuotes(static::getTableName());
		$query2 = new Query('select');
		$query2->setQueryBuildParts($sql2);
		$res2 = $query2->exec();
		if ($ar_res2 = $res2->fetch())
		{
			if ($ar_res2['MIN'] != 1)
			{
				$bError = true;
				$arResult['RULE2']['MIN'] = $ar_res2['MIN'];
			}
			$double = $ar_res2['COUNT']*2;
			if ($ar_res2['MAX'] != $double)
			{
				$bError = true;
				$arResult['RULE3']['COUNT'] = $ar_res2['COUNT'];
				$arResult['RULE3']['DOUBLE'] = $double;
				$arResult['RULE3']['MAX'] = $ar_res2['MAX'];
			}
		}
		else
		{
			$bError = true;
			$arResult['RULE2'] = false;
			$arResult['RULE3'] = false;
		}

		//4. Разница между правым и левым ключом ВСЕГДА нечетное число;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$sql4 = "SELECT ".$helper->wrapQuotes('ID').", MOD((".$helper->wrapQuotes('RIGHT_MARGIN')." - "
			.$helper->wrapQuotes('LEFT_MARGIN').") / 2) AS REMAINDER FROM ".$helper->wrapQuotes(static::getTableName())
			." WHERE REMAINDER = 0";
		$query4 = new Query('select');
		$query4->setQueryBuildParts($sql4);
		$res4 = $query4->exec();
		if ($res4->getResult())
		{
			$bError = true;
			while ($ar_res4 = $res4->fetch())
			{
				$arResult['RULE4'][] = $ar_res4;
			}
		}

		//5. Если уровень узла нечетное число то тогда левый ключ ВСЕГДА нечетное число, то же самое и для четных чисел;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$sql5 = "SELECT ".$helper->wrapQuotes('ID').", MOD((".$helper->wrapQuotes('LEFT_MARGIN')." – "
			.$helper->wrapQuotes('DEPTH_LEVEL')." + 2) / 2) AS REMAINDER FROM "
			.$helper->wrapQuotes(static::getTableName())." WHERE REMAINDER = 1";
		$query5 = new Query('select');
		$query5->setQueryBuildParts($sql5);
		$res5 = $query5->exec();
		if ($res5->getResult())
		{
			$bError = true;
			while ($ar_res5 = $res5->fetch())
			{
				$arResult['RULE5'][] = $ar_res5;
			}
		}

		//6. Ключи ВСЕГДА уникальны, вне зависимости от того правый он или левый;
		/*
			Здесь, я думаю, потребуется некоторое пояснение запроса. Выборка по сути осуществляется из одной таблицы,
			но в разделе FROM эта таблица "виртуально" продублирована 3 раза: из первой мы выбираем все записи по
			порядку и начинаем сравнивать с записями второй таблицы (раздел WHERE) в результате мы получаем все записи
			неповторяющихся значений. Для того, что бы определить сколько раз запись не повторялась в таблице,
			производим группировку (раздел GROUP BY) и получаем число "не повторов" (COUNT(t1.id)). По условию,
			если все ключи уникальны, то число не повторов будет меньше на одну единицу чем общее количество записей.
			Для того, чтобы определить количество записей в таблице, берем максимальный правый ключ (MAX(t3.right_key)),
			так как его значение - двойное число записей, но так как в условии отбора для записи с максимальным правым
			ключом - максимальный правый ключ будет другим, вводится третья таблица, при этом число "неповторов"
			увеличивается умножением его на количество записей. SQRT(4*rep +1) - решение уравнения x^2 + x = rep.
			Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов
			неправильных строк;
		 */
		$sql6 = "SELECT t1.".$helper->wrapQuotes('ID').", COUNT(t1.".$helper->wrapQuotes('ID').") AS rep, MAX(t3."
			.$helper->wrapQuotes('RIGHT_MARGIN').") AS max_right FROM ".$helper->wrapQuotes(static::getTableName())
			." AS t1, ".$helper->wrapQuotes(static::getTableName())." AS t2, "
			.$helper->wrapQuotes(static::getTableName())." AS t3 WHERE t1.".$helper->wrapQuotes('LEFT_MARGIN')
			." <> t2.".$helper->wrapQuotes('LEFT_MARGIN')." AND t1.".$helper->wrapQuotes('LEFT_MARGIN')
			." <> t2.".$helper->wrapQuotes('RIGHT_MARGIN')." AND t1.".$helper->wrapQuotes('RIGHT_MARGIN')
			." <> t2.".$helper->wrapQuotes('LEFT_MARGIN')." AND t1.".$helper->wrapQuotes('RIGHT_MARGIN')
			." <> t2.".$helper->wrapQuotes('RIGHT_MARGIN')." GROUP BY t1.".$helper->wrapQuotes('ID')
			." HAVING max_right <> SQRT(4 * rep + 1) + 1";
		$query6 = new Query('select');
		$query6->setQueryBuildParts($sql6);
		$res6 = $query6->exec();
		if ($res6->getResult())
		{
			$bError = true;
			while ($ar_res6 = $res6->fetch())
			{
				$arResult['RULE6'][] = $ar_res6;
			}
		}

		if ($bError)
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}
}