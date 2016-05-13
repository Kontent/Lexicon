<?php

// This is the base class for all user side controllers - it needs to be customised per application

class glossaryUserControllers {
	var $remUser = '';
	var $admin = '';
	var $idparm = 0;
	var $Itemid = 0;
	var $orderby = 0;

	function glossaryUserControllers ($admin) {
		$interface =& cmsapiInterface::getInstance();
		$mosConfig_absolute_path = $interface->getCfg('absolute_path');
		$mosConfig_live_site = $interface->getCfg('live_site');
		$mosConfig_lang = $interface->getCfg('lang');
		$mosConfig_sitename = $interface->getCfg('sitename');
		$this->admin = $admin;
		$this->idparm = $interface->getParam($_REQUEST, 'id', 0);
		$this->Itemid = $interface->getParam($_REQUEST, 'Itemid', 0);
		$this->orderby = $interface->getParam($_REQUEST, 'orderby', _COMPONENT_DEFAULT_ORDERING);
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$configuration =& cmsapiConfiguration::getInstance();
		if (file_exists($mosConfig_absolute_path."/components/com_$cname/$cname.css")) {
			$css = <<<GLOSSARY_USER_CSS
			
<link href="$mosConfig_live_site/components/com_$cname/$cname.css" rel="stylesheet" type="text/css"/>	

GLOSSARY_USER_CSS;

			$interface->addCustomHeadTag($css);
		}
		$mosConfig_lang = $configuration->language ? $configuration->language : $mosConfig_lang;
		//Need config values for language files
		foreach (get_object_vars($configuration) as $k=>$v) $$k = $configuration->$k;
		if(file_exists($mosConfig_absolute_path."/components/com_$cname/language/".$mosConfig_lang.'.php')) require_once($mosConfig_absolute_path."/components/com_$cname/language/".$mosConfig_lang.'.php');
		if($mosConfig_lang != 'english' AND file_exists($mosConfig_absolute_path."/components/com_$cname/language/english.php")) require_once($mosConfig_absolute_path."/components/com_$cname/language/english.php");
		$this->remUser = $interface->getUser();
	}
	
}