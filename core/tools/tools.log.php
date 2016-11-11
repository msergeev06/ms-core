<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

/**
 * Функция добавляет новую запись в лог-файл, если логирование включено
 *
 * @param mixed  $strText       Текст записи или массив
 * @param string $strPackage    Название пакета, для которого осуществляется логирование
 * @param int    $traceDepth    Глубина backtrace
 * @param bool   $bShowArgs     Показывать ли аргументы
 */
function AddMessage2Log ($strText, $strPackage='', $traceDepth = 6, $bShowArgs = false)
{
	$LOG_FILENAME = \MSergeev\Core\Lib\Options::getOptionStr('LOG_FILENAME');
	if ($LOG_FILENAME && strlen($LOG_FILENAME)>0)
	{
		if(is_array($strText))
		{
			$strTemp = '';
			printArray($strTemp,$strText);
			$strText = $strTemp;
		}
		if (strlen($strText)>0)
		{
			ignore_user_abort(true);
			if ($fp = @fopen($LOG_FILENAME, "ab"))
			{
				if (flock($fp, LOCK_EX))
				{
					@fwrite($fp, "Host: ".$_SERVER["HTTP_HOST"]."\nDate: ".date("Y-m-d H:i:s")."\nPackage: ".$strPackage."\n".$strText."\n");
					$arBacktrace = getBackTrace($traceDepth, ($bShowArgs? null : DEBUG_BACKTRACE_IGNORE_ARGS));
					$strFunctionStack = "";
					$strFilesStack = "";
					$firstFrame = (count($arBacktrace) == 1? 0: 1);
					$iterationsCount = min(count($arBacktrace), $traceDepth);
					for ($i = $firstFrame; $i < $iterationsCount; $i++)
					{
						if (strlen($strFunctionStack)>0)
							$strFunctionStack .= " < ";

						if (isset($arBacktrace[$i]["class"]))
							$strFunctionStack .= $arBacktrace[$i]["class"]."::";

						$strFunctionStack .= $arBacktrace[$i]["function"];

						if(isset($arBacktrace[$i]["file"]))
							$strFilesStack .= "\t".$arBacktrace[$i]["file"].":".$arBacktrace[$i]["line"]."\n";
						if($bShowArgs && isset($arBacktrace[$i]["args"]))
						{
							$strFilesStack .= "\t\t";
							if (isset($arBacktrace[$i]["class"]))
								$strFilesStack .= $arBacktrace[$i]["class"]."::";
							$strFilesStack .= $arBacktrace[$i]["function"];
							$strFilesStack .= "(\n";
							foreach($arBacktrace[$i]["args"] as $value)
								$strFilesStack .= "\t\t\t".$value."\n";
							$strFilesStack .= "\t\t)\n";

						}
					}

					if (strlen($strFunctionStack)>0)
					{
						@fwrite($fp, "    ".$strFunctionStack."\n".$strFilesStack);
					}

					@fwrite($fp, "----------\n");
					@fflush($fp);
					@flock($fp, LOCK_UN);
					@fclose($fp);
				}
			}
			ignore_user_abort(false);
		}
	}
}

/**
 * Формирует строку, содержащую представление массива в печатном виде
 *
 * @param string $strText   Результирующая строка
 * @param mixed  $array     Массив для разбора
 * @param int    $numTab    Количество отсупов от начала
 */
function printArray (&$strText, $array, $numTab=0)
{
	if (is_array($array))
	{
		$strText .= 'Array ['.count($array)."] (\n";
		$numTab++;
		foreach ($array as $key=>$value)
		{
			for ($i=0; $i<$numTab; $i++) $strText .= "\t";
			$strText .= '['.$key.'] => ';
			if (is_array($value))
			{
				printArray($strText, $value, $numTab);
			}
			else
			{
				$strText .= strval($value)."\n";
			}
		}
	}
}

function getBackTrace ($limit = 0, $options = null, $skip = 1)
{
	if(!defined("DEBUG_BACKTRACE_PROVIDE_OBJECT"))
	{
		define("DEBUG_BACKTRACE_PROVIDE_OBJECT", 1);
	}

	if ($options === null)
	{
		$options = ~DEBUG_BACKTRACE_PROVIDE_OBJECT;
	}

	if (PHP_VERSION_ID < 50306)
	{
		$trace = debug_backtrace($options & DEBUG_BACKTRACE_PROVIDE_OBJECT);
	}
	elseif (PHP_VERSION_ID < 50400)
	{
		$trace = debug_backtrace($options);
	}
	else
	{
		$trace = debug_backtrace($options, ($limit > 0? $limit + 1: 0));
	}

	if ($limit > 0)
	{
		return array_slice($trace, $skip, $limit);
	}

	return array_slice($trace, $skip);
}

