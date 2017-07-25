<?php

namespace MSergeev\Core\Lib;

use MSergeev\Core\Tables;

class Events
{
	private static $arEvents = array();
	private static $arEventsPackages = array();
	private static $bGetEventHandlersFromDB = false;

	/**
	 * Регистрирует произвольный обработчик callback события event_id модуля from_module_id.
	 * Если указан полный путь к файлу с обработчиком full_path, то он будет автоматически подключен перед вызовом
	 * обработчика. Вызывается на каждом хите и работает до момента окончания работы скрипта.
	 *
	 * @param string        $fromPackage    Идентификатор пакета который будет инициировать событие
	 * @param string        $eventID        Идентификатор события
	 * @param string|array  $callback       Название функции обработчика. Если это метод класса, то массив вида
	 *                                          Array(класс(объект), название метода)
	 * @param int           $sort           Очередность (порядок), в котором выполняется данный обработчик
	 *                                          (обработчиков данного события может быть больше одного).
	 *                                          Необязательный параметр, по умолчанию равен 100
	 * @param bool|string   $fullPath       Полный путь к файлу для подключения при возникновении события перед
	 *                                          вызовом callback
	 */
	public static function addEventHandler($fromPackage, $eventID, $callback, $sort=100, $fullPath=false)
	{
		$arHash = array(
			'FROM_PACKAGE' => $fromPackage,
			'EVENT_ID' => $eventID,
			'SORT' => $sort,
			'CALLBACK' => $callback,
			'FULL_PATH' => $fullPath
		);
		$hash = md5(serialize($arHash));
		self::$arEvents[$fromPackage][$eventID][$sort][$hash] = array(
			'CALLBACK' => $callback,
			'FULL_PATH' => $fullPath
		);
	}

	/**
	 * Регистрирует обработчик события. Выполняется один раз (при установке пакета) и этот обработчик события
	 * действует до момента вызова события UnRegisterPackageDependences
	 *
	 * @param string    $fromPackage    Идентификатор пакета, который будет инициировать событие
	 * @param string    $eventID        Идентификатор события
	 * @param string    $toPackage      Идентификатор пакета, содержащий функцию-обработчик события. Необязательный
	 * @param string    $toClass        Класс принадлежащий пакету $toPackage, метод которого является
	 *                                      функцией-обработчиком события.
	 *                                      Необязательный параметр. По умолчанию - ""
	 *                                      (будет просто подключен файл /msergeev/packages/$toPackage/include.php)
	 * @param string    $toMethod       Метод класса $toClass являющийся функцией-обработчиком события.
	 *                                      Необязательный параметр. По умолчанию - ""
	 *                                      (будет просто подключен файл /msergeev/packages/$toPackage/include.php)
	 * @param int       $sort           Очередность (порядок), в котором выполняется данный обработчик
	 *                                      (обработчиков данного события может быть больше одного).
	 *                                      Необязательный параметр, по умолчанию равен 100
	 * @param string    $toPath         Относительный путь к исполняемому файлу
	 * @param string    $fullPath       Полный путь к исполняемому файлу
	 * @param array     $toMethodArg    Массив аргументов для функции-обработчика событий.
	 *                                      Необязательный параметр.
	 *
	 * @return DBResult
	 */
	public static function registerPackageDependences (
		$fromPackage, $eventID, $toPackage='', $toClass = "", $toMethod = "", $sort = 100, $toPath="", $fullPath="",
		$toMethodArg=array()
	)
	{
		$arAdd = array(
			'FROM_PACKAGE' => strtolower($fromPackage),
			'EVENT_ID' => $eventID,
			'SORT' => intval($sort)
		);
		if ($toPackage != '')
		{
			$arAdd['TO_PACKAGE_ID'] = strtolower($toPackage);
		}
		if ($toClass != '')
		{
			$arAdd['TO_CLASS'] = $toClass;
		}
		if ($toMethod != '')
		{
			$arAdd['TO_METHOD'] = $toMethod;
		}
		if ($toPath != '' && file_exists(Config::getConfig('MSERGEEV_ROOT').$toPath))
		{
			$arAdd['TO_PATH'] = $toPath;
		}
		if ($fullPath != '' && file_exists($fullPath))
		{
			$arAdd['FULL_PATH'] = $fullPath;
		}
		if (!empty($toMethodArg))
		{
			$arAdd['TO_METHOD_ARG'] = $toMethodArg;
		}

		return Tables\EventHandlersTable::add(array("VALUES"=>$arAdd));
	}

	/**
	 * Удаляет регистрационную запись обработчика события
	 *
	 * @param string $fromPackage   Идентификатор пакета который инициирует событие
	 * @param string $eventID       Идентификатор события
	 * @param string $toPackage     Идентификатор пакета содержащий функцию-обработчик события
	 * @param string $toClass       Класс принадлежащий пакету $toPackage, метод которого является
	 *                                  функцией-обработчиком события.
	 *                                  Необязательный параметр. По умолчанию - "".
	 * @param string $toMethod      Метод класса $toClass являющийся функцией-обработчиком события.
	 *                                  Необязательный параметр. По умолчанию - "".
	 * @param string $toPath        Необязательный параметр, по умолчанию пустой
	 * @param string $fullPath       Полный путь к исполняемому файлу
	 * @param array  $toMethodArg   Массив аргументов для функции-обработчика событий.
	 *                                  Необязательный параметр
	 */
	public static function unRegisterModuleDependences(
		$fromPackage, $eventID, $toPackage='', $toClass = "", $toMethod = "", $toPath="", $fullPath="",
		$toMethodArg=array()
	)
	{
		$arFilter = array(
			'FROM_PACKAGE' => strtolower($fromPackage),
			'EVENT_ID' => $eventID
		);
		if ($toPackage != '')
		{
			$arFilter['TO_PACKAGE_ID'] = strtolower($toPackage);
		}
		if ($toClass != '')
		{
			$arFilter['TO_CLASS'] = $toClass;
		}
		if ($toMethod != '')
		{
			$arFilter['TO_METHOD'] = $toMethod;
		}
		if ($toPath != '')
		{
			$arFilter['TO_PATH'] = $toPath;
		}
		if ($fullPath != '')
		{
			$arFilter['FULL_PATH'] = $fullPath;
		}
		if (!empty($toMethodArg))
		{
			$arFilter['TO_METHOD_ARG'] = $toMethodArg;
		}

		$arRes = Tables\EventHandlersTable::getList(
			array(
				'select' => array('ID'),
				'filter' => $arFilter
			)
		);
		if ($arRes)
		{
			foreach ($arRes as $arHandler)
			{
				Tables\EventHandlersTable::delete($arHandler['ID']);
			}
		}
	}

	/**
	 * Возвращает массив зарегистрированных обработчиков заданного события
	 *
	 * @param string $fromPackage   Идентификатор пакета который инициирует событие
	 * @param string $eventID       Идентификатор события
	 * @param bool   $fromDB        Флаг принудительной загрузки обработчиков событий из DB
	 *
	 * @return array                Пустой массив, или массив обработчиков
	 */
	public static function getPackageEvents ($fromPackage, $eventID, $fromDB=false)
	{
		if (!self::$bGetEventHandlersFromDB || $fromDB)
		{
			if ($fromDB)
			{
				self::getEventHandlersFromDB($fromPackage, $eventID);
			}
			else
			{
				self::getEventHandlersFromDB();
			}
		}

		$arReturn = array();
		if (isset(self::$arEventsPackages[$fromPackage][$eventID]))
		{
			$arReturn = self::$arEventsPackages[$fromPackage][$eventID];
		}

		if (isset(self::$arEvents[$fromPackage][$eventID]))
		{
			foreach (self::$arEvents[$fromPackage][$eventID] as $sort=>$events)
			{
				foreach ($events as $hash=>$event)
				{
					$arReturn[$sort][$hash] = $event;
				}
			}
		}


		if (!empty($arReturn))
		{
			$arTmp = $arReturn;
			$arReturn = array();
			foreach ($arTmp as $sort=>$arEvents)
			{
				foreach ($arEvents as $hash=>$event)
				{
					$arReturn[$sort][] = $event;
				}
			}

			krsort($arReturn);

			return $arReturn;
		}
		else
		{
			return false;
		}
	}

	public static function executePackageEvent ($arEvent, $arParams=array())
	{
		$r = true;

		if (
			isset($arEvent["TO_PACKAGE_ID"])
			&& $arEvent["TO_PACKAGE_ID"]<>""
			&& $arEvent["TO_PACKAGE_ID"]<>"core"
		)
		{
			//Подключаем нужный пакет
			if(!Loader::IncludePackage($arEvent["TO_PACKAGE_ID"]))
				return null;
		}
		elseif(
			isset($arEvent["TO_PATH"])
			&& $arEvent["TO_PATH"]<>""
			&& file_exists(Config::getConfig('MSERGEEV_ROOT').$arEvent["TO_PATH"])
		)
		{
			$r = include_once(Config::getConfig('MSERGEEV_ROOT').$arEvent["TO_PATH"]);
		}
		elseif ($arEvent['FULL_PATH']!==false && file_exists($arEvent['FULL_PATH']))
		{
			//Выполняем код из заданного файла
			$r = include_once($arEvent['FULL_PATH']);
		}

		if (array_key_exists("CALLBACK", $arEvent))
		{
			if(isset($arEvent["TO_METHOD_ARG"]) && is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]))
				$args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
			else
				$args = $arParams;

			return call_user_func_array($arEvent["CALLBACK"], $args);
		}
		elseif(
			$arEvent["TO_CLASS"] != ""
			&& !is_null($arEvent["TO_CLASS"])
			&& $arEvent["TO_METHOD"] != ""
			&& !is_null($arEvent["TO_METHOD"]))
		{
			if(is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]))
				$args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
			else
				$args = $arParams;

			//php bug: http://bugs.php.net/bug.php?id=47948
			class_exists($arEvent["TO_CLASS"]);
			return call_user_func_array(array($arEvent["TO_CLASS"], $arEvent["TO_METHOD"]), $args);
		}
		else
		{
			return $r;
		}
	}

	/**
	 * Функция получает список зарегистрированных обработчиков события и запускает их
	 *
	 * @see MSergeev\Core\Lib\Events::getPackageEvents
	 * @see MSergeev\Core\Lib\Events::executePackageEvent
	 *
	 * @param string $fromPackage   Идентификатор пакета который инициирует событие
	 * @param string $eventID       Идентификатор события
	 * @param array  $arParams      Параметры события
	 * @param bool   $fromDB        Флаг принудительной загрузки обработчиков событий из DB
	 *
	 * @return bool
	 */
	public static function runEvents ($fromPackage,$eventID,$arParams=array(),$fromDB=false)
	{
		if ($arEvents = static::getPackageEvents($fromPackage,$eventID, $fromDB))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					$bStop = static::executePackageEvent($arEvent,$arParams);
					if ($bStop===false)
						return $bStop;
				}
			}
		}

		return true;
	}

	/**
	 * Получает информацию о зарегистрированных обработчиках событий из DB
	 *
	 * @param null|string $fromPackage Идентификатор инициализирующего пакета
	 * @param null|string $eventID     Идентификатор обновляемого события
	 */
	private static function getEventHandlersFromDB ($fromPackage=null, $eventID=null)
	{
		if (is_null($fromPackage) && is_null($eventID))
		{
			self::$bGetEventHandlersFromDB = true;
		}

		$arList = array(
			'order' => array('SORT'=>'DESC')
		);
		if (!is_null($fromPackage) && !is_null($eventID))
		{
			$arList['filter'] = array(
				'FROM_PACKAGE' => strtolower($fromPackage),
				'EVENT_ID' => $eventID
			);
		}

		$arRes = Tables\EventHandlersTable::getList($arList);

		if ($arRes)
		{
			foreach ($arRes as $arHandler)
			{
				unset($arHandler['ID']);
				$hash = md5(serialize($arHandler));
				$fromPack = $arHandler['FROM_PACKAGE'];
				unset($arHandler['FROM_PACKAGE']);
				$eventID = $arHandler['EVENT_ID'];
				unset($arHandler['EVENT_ID']);
				$sort = $arHandler['SORT'];
				unset($arHandler['SORT']);

				self::$arEventsPackages[$fromPack][$eventID][$sort][$hash] = $arHandler;
			}
		}
		//msDebug(self::$arEventsPackages);
	}

}


