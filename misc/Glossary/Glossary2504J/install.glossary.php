<?php

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if (!defined('_THIS_COMPONENT_NAME')) define ('_THIS_COMPONENT_NAME', 'Glossary');
if (!defined(_THIS_COMPONENT_NAME.'_ADMIN_SIDE')) define (_THIS_COMPONENT_NAME.'_ADMIN_SIDE', 1);

class installConfig {
	
	function installConfig () {
		$interface = cmsapiInterface::getInstance();
		$cname = strtolower(_THIS_COMPONENT_NAME);
		require($interface->getCfg('absolute_path')."/components/com_{$cname}/com_{$cname}_install_settings.php");
	}
}

function com_install () {
	
	// This function should work for any component without alteration
	function makeMenuEntry ($database) {
		if (defined('_ALIRO_IS_PRESENT')) return;
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$database->setQuery("SELECT MIN(id) FROM `#__components` WHERE `option` = 'com_$cname'");
		$compnum = intval($database->loadResult());
		$database->setQuery("SELECT count(*) FROM `#__menu` WHERE published > 0 AND link = 'index.php?option=com_$cname'");
		if (!$database->loadResult()) {
			$database->setQuery("SELECT MAX(ordering) FROM `#__menu`");
			$ordering = intval($database->loadResult() + 1);
			if (defined('_JEXEC') AND !defined('_ALIRO_IS_PRESENT')) $database->setQuery("INSERT INTO `#__menu` "
			." (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) "
			." VALUES (NULL , 'mainmenu', '$compname', '$cname', 'index.php?option=com_$cname', 'components', '1', '0', $compnum, '0', $ordering, '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '')");
			else $database->setQuery("INSERT INTO `#__menu` "
			." (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) "
			." VALUES (NULL , 'mainmenu', '$compname', 'index.php?option=com_$cname', 'components', '1', '0', $compnum, '0', $ordering, '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '')");
			$database->query();
		}
		else {
			$database->setQuery("UPDATE #__menu SET componentid = $compnum WHERE link LIKE 'index.php?option=com_$cname%'");
			$database->query();
		}
	}
	
	// This initial code should work for any component
	$cname = strtolower(_THIS_COMPONENT_NAME);
	$current_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($current_dir);
	$admin_dir = dirname($components_dir);
	$absolute_path = dirname($admin_dir);
	require_once($absolute_path."/components/com_{$cname}/cmsapi.interface.php");
	require_once($absolute_path."/components/com_{$cname}/p-classes/cmsapiConfiguration.php");
	$gconfig =& cmsapiConfiguration::getInstance();
	$iconfig = new installConfig;
	foreach (get_object_vars($iconfig) as $key=>$value) if (!isset($gconfig->$key)) $gconfig->$key = $value;
	$gconfig->save();
	$interface = cmsapiInterface::getInstance();
	$database = $interface->getDB();
	makeMenuEntry($database);
	// This code is application specific
	$database->setQuery("SELECT COUNT(*) FROM #__glossaries");
	if (!$database->loadResult()) {
		$database->setQuery("SELECT id, name, description, published FROM #__categories WHERE section = 'com_glossary'");
		$categories = $database->loadObjectList();
		if ($categories) {
			foreach ($categories as $category) {
				$database->setQuery("INSERT INTO #__glossaries (id, name, description, published) VALUES ('$category->id', '$category->name', '$category->description', $category->published)");
				$database->query();
			}
		}
		else {
			$database->setQuery("INSERT INTO #__glossaries (name, description, published) VALUES ('Glossary', 'Glossary of terms used on this site', 1)");
			$database->query();
		}
	}
}

