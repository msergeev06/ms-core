<?php

namespace MSergeev\Core\Lib;

class Component
{
	public $__name;
	public $__relativePath;
	public $__path;
	public $__templateName;
	public $__templatePage;
	public $__template;
	public $__component_epilog;
	public $arParams;
	public $arResult;
	public $arResultCacheKeys;
	public $__parent;
	public $__bInited;
	public $__arIncludeAreaIcons;
	public $__NavNum;
	public $__cache;
	public $__cacheID;
	public $__cachePath;
	public $__children_css;
	public $__children_js;
	public $__children_epilogs;
	public $__views;
	public $__currentCounter;
	public $__editButtons;
	public $classOfComponent="";
	//private $siteId;
	private $languageId;
	private $siteTemplateId;
	private $__componentStack=array();

	private static $__classes_map=array();
	private static $__componentCounter=array();

	public static function includeComponent ($componentName, $componentTemplate, $arParams=array(), $parentComponent=null, $arFunctionParams=array())
	{
		//Создаем относительный путь к компоненту
		$componentRelativePath = self::makeComponentPath($componentName);
		if (strlen($componentRelativePath) <= 0)
		{
			return false;
		}

		//Проверяем родительский компонент
		if (is_object($parentComponent))
		{
			if(!($parentComponent instanceof Component))
			{
				$parentComponent = null;
			}
		}

		$component = new Component();
		if ($component->initComponent($componentName))
		{
			//$component->__componentStack[] = $component;
			$result = $component->includeComponentNow($componentTemplate, $arParams, $parentComponent);

			//array_pop($this->__componentStack);
		}



		return true;
	}

	public static function makeComponentPath ($componentName)
	{
		if(!self::checkComponentName($componentName))
			return "";

		return str_replace(":", "/", $componentName);
	}

	public static function checkComponentName ($componentName)
	{
		return ($componentName <> '' && preg_match("#^([A-Za-z0-9_.-]+:)?([A-Za-z0-9_-]+\\.)*([A-Za-z0-9_-]+)$#i", $componentName));
	}

	public function __construct ($component = null)
	{
		if (is_object($component) && ($component instanceof Component))
		{
			$this->__name = $component->__name;
			$this->__relativePath = $component->__relativePath;
			$this->__path = $component->__path;
			$this->__templateName = $component->__templateName;
			$this->__templatePage = $component->__templatePage;
			$this->__template = $component->__template;
			$this->__component_epilog = $component->__component_epilog;
			$this->arParams = $component->arParams;
			$this->arResult = $component->arResult;
			$this->arResultCacheKeys = $component->arResultCacheKeys;
			$this->__parent = $component->__parent;
			$this->__bInited = $component->__bInited;
			$this->__arIncludeAreaIcons = $component->__arIncludeAreaIcons;
			$this->__NavNum = $component->__NavNum;
			$this->__cache = $component->__cache;
			$this->__cacheID = $component->__cacheID;
			$this->__cachePath = $component->__cachePath;
			$this->__children_css = $component->__children_css;
			$this->__children_js = $component->__children_js;
			$this->__children_epilogs = $component->__children_epilogs;
			$this->__view = $component->__view;
			$this->__currentCounter = $component->__currentCounter;
			$this->__editButtons = $component->__editButtons;
			$this->classOfComponent = $component->classOfComponent;
			//$this->setSiteId($component->getSiteId());
			$this->setLanguageId($component->getLanguageId());
			$this->setSiteTemplateId($component->getSiteTemplateId());
		}
		else
		{
			//$this->setSiteId(SITE_ID);
			$this->setLanguageId(Config::getConfig('LANG'));
			$this->setSiteTemplateId(Config::getConfig('TEMPLATE'));
		}

		//$this->request = \Bitrix\Main\Context::getCurrent()->getRequest();
	}

	public function initComponent ($componentName, $componentTemplate = false)
	{
		$this->__bInited = false;

		$componentName = trim($componentName);
		if ($componentName == '')
		{
			$this->__ShowError("Empty component name");
			return false;
		}

		$path2Comp = self::makeComponentPath($componentName);
		if ($path2Comp == '')
		{
			$this->__ShowError(sprintf("'%s' is not a valid component name", $componentName));
			return false;
		}

		$componentPath = Config::getConfig('COMPONENTS_ROOT').$path2Comp;
		$this->classOfComponent = self::__getClassForPath($componentPath);
		if($this->classOfComponent === "")
		{
			$componentFile = $componentPath."/component.php";
			if (!file_exists($componentFile) || !is_file($componentFile))
			{
				$this->__ShowError(sprintf("'%s' is not a component", $componentName));
				return false;
			}
		}

		if (!isset(self::$__componentCounter[$componentName]))
			self::$__componentCounter[$componentName] = 1;
		else
			self::$__componentCounter[$componentName]++;

		$this->__currentCounter = self::$__componentCounter[$componentName];

		$this->__name = $componentName;
		$this->__relativePath = $path2Comp;
		$this->__path = $componentPath;
		$this->arResult = array();
		$this->arParams = array();
		$this->__parent = null;
		$this->__arIncludeAreaIcons = array();
		$this->__cache = null;
		if ($componentTemplate !== false)
			$this->__templateName = $componentTemplate;

		$this->__bInited = true;

		return true;
	}

	public function includeComponentNow ($componentTemplate, $arParams, $parentComponent)
	{
		if (!$this->__bInited)
			return null;

		if ($componentTemplate !== false)
			$this->setTemplateName($componentTemplate);

		if ($parentComponent instanceof Component)
			$this->__parent = $parentComponent;

/*		if ($arParams["CACHE_TYPE"] != "Y" && $arParams["CACHE_TYPE"] != "N")
			$arParams["CACHE_TYPE"] = "A";*/
/*
		if($this->classOfComponent)
		{
			/** @var CBitrixComponent $component  * /
			$component = new $this->classOfComponent($this);
			$component->onIncludeComponentLang();
			$component->arParams = $component->onPrepareComponentParams($arParams);
			$component->__prepareComponentParams($component->arParams);

			$componentFrame = new Bitrix\Main\Page\FrameComponent($component);
			$componentFrame->start();

			$result = $component->executeComponent();
			$this->__arIncludeAreaIcons = $component->__arIncludeAreaIcons;
			$frameMode = $component->getFrameMode();

			$componentFrame->end();
		}
		else
		{
			$this->includeComponentLang();
			$this->__prepareComponentParams($arParams);
			$this->arParams = $arParams;

			$componentFrame = new Bitrix\Main\Page\FrameComponent($this);
			$componentFrame->start();

			$result = $this->__IncludeComponent();
			$frameMode = $this->getFrameMode();

			$componentFrame->end();
		}

		if (!$frameMode)
		{
			\Bitrix\Main\Data\StaticHtmlCache::applyComponentFrameMode($this->__name);
		}

		return $result;
*/
		return true;
	}

	public function setTemplateName($templateName)
	{
		if (!$this->__bInited)
			return null;

		$this->__templateName = $templateName;
		return true;
	}

/*	public function setSiteId ($siteId)
	{
		$this->siteId = $siteId;
	}

	public function getSiteId ()
	{
		return $this->siteId;
	}*/

	public function setLanguageId ($languageId)
	{
		$this->languageId = $languageId;
	}

	public function getLanguageId ()
	{
		return $this->languageId;
	}

	public function setSiteTemplateId ($siteTemplateId)
	{
		$this->siteTemplateId = $siteTemplateId;
	}

	public function getSiteTemplateId ()
	{
		return $this->siteTemplateId;
	}

	public function __showError($errorMessage, $errorCode = "")
	{
		if ($errorMessage <> '')
			echo "<font color=\"#FF0000\">".$errorMessage.($errorCode <> '' ? " [".$errorCode."]" : "")."</font>";
	}

	private function __getClassForPath ($componentPath)
	{
		if (!isset(self::$__classes_map[$componentPath]))
		{
			$fname = $componentPath."/class.php";
			if (file_exists($fname) && is_file($fname))
			{
				$beforeClasses = get_declared_classes();
				$beforeClassesCount = count($beforeClasses);
				include_once($fname);
				$afterClasses = get_declared_classes();
				$afterClassesCount = count($afterClasses);
				for ($i = $beforeClassesCount; $i < $afterClassesCount; $i++)
				{
					if (is_subclass_of($afterClasses[$i], "Component"))
						self::$__classes_map[$componentPath] = $afterClasses[$i];
				}
			}
			else
			{
				self::$__classes_map[$componentPath] = "";
			}
		}
		return self::$__classes_map[$componentPath];
	}
}