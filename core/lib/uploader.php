<?php
/**
 * MSergeev\Core\Lib\Uploader
 * Обработка загружаемой пользователями информации
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Lib;

class Uploader
{
	protected $tmpFilename=null;
	protected $tmpPath=null;

	public function __construct($arFile=array())
	{
		if (empty($arFile)) $arFile = $_FILES;

	}


}