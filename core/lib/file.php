<?php

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Exception;
use MSergeev\Core\Tables;

/**
 * Class File
 * @package MSergeev\Core\Lib
 *
 * Events:
 * OnBeforeUploadNewFile (&$arFile, &$arAdd)
 * OnAfterUploadNewFile (&$arFile, &$arAdd)
 * OnBeforeAddNewFile (&$arAdd, &$arFile)
 * OnAfterAddNewFile ($arFile,$res->getInsertId())
 */
class File
{
	private static $documentRoot = null;
	private static $uploadDir = null;

	private static function init ()
	{
		if (is_null(self::$documentRoot))
		{
			if (!self::$documentRoot = Options::getOptionStr('DOCUMENT_ROOT'))
			{
				if (!self::$documentRoot = Config::getConfig('DOCUMENT_ROOT'))
				{
					self::$documentRoot = (($_SERVER["DOCUMENT_ROOT"]=='')?'/var/www/':$_SERVER["DOCUMENT_ROOT"].'/');
				}
			}
		}
		if (is_null(self::$uploadDir))
		{
			if (!self::$uploadDir = Options::getOptionStr('UPLOAD_DIR'))
			{
				if (!self::$uploadDir = Config::getConfig('UPLOAD_DIR'))
				{
					self::$uploadDir = self::$documentRoot.'/msergeev/upload/';
				}
			}
		}
	}

	private static function getByID ($fileID=null)
	{
		try
		{
			if (is_null($fileID))
			{
				throw new Exception\ArgumentNullException('$fileID');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}

		$arRes = Tables\FileTable::getOne(
			array(
				'filter' => array('ID'=>$fileID)
			)
		);

		return $arRes;
	}

	public static function addNewImg ($packageName, array $arFile)
	{
		self::init();
		if (strpos($arFile['type'],'image')===false)
		{
			return false;
		}
		$data = array();
		if (file_exists($arFile['tmp_name']))
		{
			list($data['width'], $data['height'], $data['type_num'], $data['attr']) = getimagesize($arFile['tmp_name']);
			$arFile['width'] = $data['width'];
			$arFile['height'] = $data['height'];
			$arFile['type_num'] = $data['type_num'];
		}
		$arAdd = array();
		if (strlen($packageName)<=0 || !Loader::issetPackage($packageName))
		{
			return false;
		}
		else
		{
			$arAdd['PACKAGE'] = htmlspecialchars($packageName);
		}
		if (isset($arFile['name']))
		{
			$arAdd['ORIGINAL_NAME'] = basename($arFile['name']);
		}
		if (isset($arFile['type']))
		{
			$arAdd['CONTENT_TYPE'] = htmlspecialchars($arFile['type']);
		}
		if (isset($arFile['size']))
		{
			$arAdd['FILE_SIZE'] = intval($arFile['size']);
		}
		if (isset($arFile['width']) && intval($arFile['width'])>0)
		{
			$arAdd['WIDTH'] = intval($arFile['width']);
		}
		if (isset($arFile['height']) && intval($arFile['height'])>0)
		{
			$arAdd['HEIGHT'] = intval($arFile['height']);
		}
		if (isset($arFile['title']))
		{
			$arAdd['DESCRIPTION'] = htmlspecialchars($arFile['title']);
		}
		$newName = md5($arFile['name'].time());
		$arExt = explode('.',$arFile['name']);
		$countExt = count($arExt);
		$ext = $arExt[$countExt-1];
		$sub = substr ($newName,0,3);
		$arAdd['SUBDIR'] = $arAdd['PACKAGE'].'/'.$sub;
		$arAdd['FILE_NAME'] = $newName.'.'.$ext;
		$uploadDir = self::$uploadDir;

		if ($arEvents = Events::getPackageEvents('core','OnBeforeUploadNewFile'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					Events::executePackageEvent($arEvent,array(&$arFile, &$arAdd));
				}
			}
		}

		if (!file_exists($uploadDir.$arAdd['PACKAGE']))
		{
			mkdir($uploadDir.$arAdd['PACKAGE']);
		}
		if (!file_exists($uploadDir.$arAdd['SUBDIR']))
		{
			mkdir($uploadDir.$arAdd['SUBDIR']);
		}
		if (isset($arFile['tmp_name']) && file_exists($arFile['tmp_name']))
		{
			move_uploaded_file($arFile['tmp_name'],$uploadDir.$arAdd['SUBDIR'].'/'.$arAdd['FILE_NAME']);
		}

		if ($arEvents = Events::getPackageEvents('core','OnAfterUploadNewFile'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					Events::executePackageEvent($arEvent,array(&$arFile, &$arAdd));
				}
			}
		}

		if ($arEvents = Events::getPackageEvents('core','OnBeforeAddNewFile'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					Events::executePackageEvent($arEvent,array(&$arAdd, &$arFile));
				}
			}
		}

		$query = new Query('insert');
		$query->setInsertParams(
			$arAdd,
			Tables\FileTable::getTableName(),
			Tables\FileTable::getMapArray()
		);
		$res = $query->exec();
		if ($res->getResult())
		{
			if ($arEvents = Events::getPackageEvents('core','OnAfterAddNewFile'))
			{
				foreach ($arEvents as $sort=>$ar_events)
				{
					foreach ($ar_events as $arEvent)
					{
						Events::executePackageEvent($arEvent,array($arFile,$res->getInsertId()));
					}
				}
			}

			return $res->getInsertId();
		}

		return false;
	}

	/**
	 * @param mixed     $strImage           ID файла или путь к файлу на текущем сайте либо URL к файлу лежащем на другом
	 *                                      сайте. Если задается путь к файлу на текущем сайте, то его необходимо задавать
	 *                                      относительно корня
	 * @param int       $maxWidth           Максимальная ширина изображения. Если ширина картинки больше maxWidth, то она
	 *                                      будет пропорционально смаштабирована.
	 *                                      Необязательный. По умолчанию - "0" - без ограничений
	 * @param int       $maxHeight          Максимальная высота изображения. Если высота картинки больше maxHeight, то она
	 *                                      будет пропорционально смаштабирована.
	 *                                      Необязательный. По умолчанию - "0" - без ограничений.
	 *                                      Если maxWidth установлен в 0, то maxHeight учитываться не будет. Чтобы ограничить
	 *                                      высоту можно установить максимальную ширину в некое бо́льшее значение
	 *                                      (например, 9999) вместо 0
	 * @param string    $sParams            Произвольный HTML добавляемый в тэг IMG:
	 *                                      <img image_params ...>
	 *                                      Необязательный. По умолчанию "null". Если в этом параметре передать атрибут
	 *                                      alt="текст", то в теге <img> будет использовано это значение. Иначе, если
	 *                                      картинка имеет описание в таблице, для атрибута alt будет использовано это
	 *                                      описание.
	 * @param string    $imageUrl           Ссылка для перехода при нажатии на картинку.
	 *                                      Необязательный. По умолчанию "" - не выводить ссылку.
	 * @param bool      $bPopup             Открывать ли при клике на изображении дополнительное popup окно с увеличенным
	 *                                      изображением.
	 *                                      Необязательный. По умолчанию - "false".
	 * @param bool      $popupTitle         Текст всплывающей подсказки на изображении (только если popup = true)
	 *                                      Необязательный. По умолчанию выводится фраза "Увеличить" на языке страницы.
	 * @param int       $sizeWHTTP          Ширина изображения (в пикселах) (только если в параметре image задан URL
	 *                                      начинающийся с "http://")
	 *                                      Необязательный. По умолчанию "0".
	 * @param int       $sizeHHTTP          Высота изображения (в пикселах) (только если в параметре image задан URL
	 *                                      начинающийся с "http://")
	 *                                      Необязательный. По умолчанию "0".
	 *
	 * @return string
	 */
	public static function showImage ($strImage, $maxWidth=0,$maxHeight=0,$sParams=null,$imageUrl='',$bPopup=false,$popupTitle=false,$sizeWHTTP=0,$sizeHHTTP=0)
	{
		if (is_array($strImage))
		{
			$arImgParams = $strImage;
			$iImageID = isset($arImgParams['ID']) ? intval($arImgParams['ID']) : 0;
		}
		else
		{
			$arImgParams = self::getImgParams($strImage,$sizeWHTTP,$sizeHHTTP);
			$iImageID = intval($strImage);
		}

		if(!$arImgParams)
			return "";

		$iMaxW = intval($maxWidth);
		$iMaxH = intval($maxHeight);
		$intWidth = $arImgParams['WIDTH'];
		$intHeight = $arImgParams['HEIGHT'];

		if(
			$iMaxW > 0 && $iMaxH > 0
			&& ($intWidth > $iMaxW || $intHeight > $iMaxH)
		)
		{
			$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
			$iHeight = intval(Tools::roundEx($intHeight/$coeff));
			$iWidth = intval(Tools::roundEx($intWidth/$coeff));
		}
		else
		{
			$coeff = 1;
			$iHeight = $intHeight;
			$iWidth = $intWidth;
		}

		$strImage = $arImgParams['SRC'];

		//if (!preg_match("/^https?:/i", $strImage))
			//$strImage = urlencode($strImage);

		if(self::getFileType($strImage) == "FLASH")
		{
			$strReturn = '
                <object
                    classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000"
                    codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
                    id="banner"
                    WIDTH="'.$iWidth.'"
                    HEIGHT="'.$iHeight.'"
                    ALIGN="">
                        <PARAM NAME="movie" VALUE="'.$strImage.'" />
                        <PARAM NAME="quality" VALUE="high" />
                        <PARAM NAME="bgcolor" VALUE="#FFFFFF" />
                        <embed
                            src="'.$strImage.'"
                            quality="high"
                            bgcolor="#FFFFFF"
                            WIDTH="'.$iWidth.'"
                            HEIGHT="'.$iHeight.'"
                            NAME="banner"
                            ALIGN=""
                            TYPE="application/x-shockwave-flash"
                            PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
                        </embed>
                </object>
            ';
		}
		else
		{
			$strAlt = $arImgParams['ALT']? $arImgParams['ALT']: $arImgParams['DESCRIPTION'];

			if($sParams === null || $sParams === false)
			{
				$sParams = 'border="0" alt="'.Tools::htmlspecialchars($strAlt).'"';
			}
			elseif(!preg_match('/(^|\\s)alt\\s*=\\s*(["\']?)(.*?)(\\2)/is', $sParams))
			{
				$sParams .= ' alt="'.Tools::htmlspecialchars($strAlt).'"';
			}

			if($coeff === 1 && !$bPopup)
			{
				$strReturn = '<img src="'.$strImage.'" '.$sParams.' width="'.$iWidth.'" height="'.$iHeight.'" />';
			}
			else
			{
				if($popupTitle === false)
					$popupTitle = Loc::getPackMessage('core','core_file_enlarge');

				Plugins::includeMagnificPopup();

				if (intval($iImageID) <=0)
				{
					$iImageID = str_replace(' ','', str_replace('0.','', microtime(false)));
				}
				if(strlen($imageUrl)>0)
				{
					$strReturn =
						'<a href="'.$imageUrl.'" title="'.$popupTitle.'" class="popup-link-'.$iImageID.'">'.
						'<img src="'.$strImage.'" '.$sParams.' width="'.$iWidth.'" height="'.$iHeight.'" title="'
						.Tools::htmlspecialchars($popupTitle).'" />'.
						'</a>';
				}
				else
				{
					$strReturn =
						'<a href="'.$strImage.'" title="'.$strAlt.'" class="popup-link-'.$iImageID.'">'.
						'<img src="'.$strImage.'" '.$sParams.' width="'.$iWidth.'" height="'.$iHeight.'" title="'
						.Tools::htmlspecialchars($popupTitle).'" />'.
						'</a>';
				}
				Buffer::addJsToDownPage("$('.popup-link-".$iImageID."').magnificPopup({type: 'image'});");
/*				$strReturn .= "
						<script>
							$('.popup-link-".$iImageID."').magnificPopup({
								type: 'image'
							});
						</script>
					";*/
			}
		}
		return $strReturn;
	}

	public static function deleteFile ($fileID=null)
	{
		if (!is_null($fileID) && intval($fileID)>0)
		{
			if ($arFile = self::getFileArray($fileID))
			{
				if (file_exists($arFile['FILE_PATH']))
				{
					unlink($arFile['FILE_PATH']);
					if (!file_exists($arFile['FILE_PATH']))
					{
						$query = new Query('delete');
						$query->setDeleteParams(
							$arFile['ID'],
							true,
							Tables\FileTable::getTableName(),
							Tables\FileTable::getMapArray(),
							Tables\FileTable::getTableLinks()
						);
						$res = $query->exec();
						if ($res->getResult())
						{
							return true;
						}
						else
						{
							$query = new Query('update');
							$query->setUpdateParams(
								array('EXTERNAL_ID'=>'DELETE'),
								$arFile['ID'],
								Tables\FileTable::getTableName(),
								Tables\FileTable::getMapArray()
							);
							$res = $query->exec();
							if ($res->getResult())
							{
								return true;
							}
						}
					}
				}
			}
		}

		return false;
	}

	protected static function getImgParams ($strImage, $iSizeWHTTP=0, $iSizeHHTTP=0)
	{
		if(strlen($strImage) <= 0)
			return false;

		if(intval($strImage)>0)
		{
			if ($arFile = self::getFileArray($strImage))
			{
				$strImage = $arFile["SRC"];
				$intWidth = intval($arFile["WIDTH"]);
				$intHeight = intval($arFile["HEIGHT"]);
				$strAlt = $arFile["DESCRIPTION"];
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(!preg_match("#^https?://#", $strImage))
			{
				self::init();
				if (file_exists(self::$documentRoot.$strImage))
				{
					$arSize = getimagesize(self::$documentRoot.$strImage);
					$intWidth = intval($arSize[0]);
					$intHeight = intval($arSize[1]);
					$strAlt = "";
				}
				else
				{
					return false;
				}
			}
			else
			{
				$intWidth = intval($iSizeWHTTP);
				$intHeight = intval($iSizeHHTTP);
				$strAlt = "";
			}
		}

		return array(
			"SRC"=>$strImage,
			"WIDTH"=>$intWidth,
			"HEIGHT"=>$intHeight,
			"ALT"=>$strAlt,
		);
	}

	protected static function getFileArray ($fileID, $upload_dir = false)
	{
		if (!$upload_dir)
		{
			self::init();
			$upload_dir = Tools::getSitePath(self::$uploadDir);
		}

		if(!is_array($fileID) && intval($fileID) > 0)
		{
			if ($arFile = self::getByID($fileID))
			{
				$arFile['SRC'] = $upload_dir.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
				$arFile['FILE_PATH'] = self::$uploadDir.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];

				return $arFile;
			}
		}

		return false;
	}

	public static function getFileType($path)
	{
		$extension = self::getFileExtension(strtolower($path));
		switch ($extension)
		{
			case "jpg":
			case "jpeg":
			case "gif":
			case "bmp":
			case "png":
				$type = "IMAGE";
				break;
			case "swf":
				$type = "FLASH";
				break;
			case "html":
			case "htm":
			case "asp":
			case "aspx":
			case "phtml":
			case "php":
			case "php3":
			case "php4":
			case "php5":
			case "php6":
			case "shtml":
			case "sql":
			case "txt":
			case "inc":
			case "js":
			case "vbs":
			case "tpl":
			case "css":
			case "shtm":
				$type = "SOURCE";
				break;
			default:
				$type = "UNKNOWN";
		}
		return $type;
	}

	public static function getFileExtension($path)
	{
		$path = self::getFileName($path);
		if($path <> '')
		{
			$pos = Tools::strrpos($path, '.');
			if($pos !== false)
				return substr($path, $pos+1);
		}
		return '';
	}

	public static function getFileName($path)
	{
		$path = Tools::trimUnsafe($path);
		$path = str_replace("\\", "/", $path);
		$path = rtrim($path, "/");

		$p = Tools::strrpos($path, "/");
		if($p !== false)
			return substr($path, $p+1);

		return $path;
	}
}