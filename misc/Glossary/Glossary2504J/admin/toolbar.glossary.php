<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

// ensure this file is being included by a parent file
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if (!defined('_THIS_COMPONENT_NAME')) define ('_THIS_COMPONENT_NAME', 'Glossary');
if (!defined(_THIS_COMPONENT_NAME.'_ADMIN_SIDE')) define (_THIS_COMPONENT_NAME.'_ADMIN_SIDE', 1);

$cname = strtolower(_THIS_COMPONENT_NAME);
if (!defined('_ALIRO_IS_PRESENT')) {
	// Include files that contain classes
	$current_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($current_dir);
	$admin_dir = dirname($components_dir);
	$absolute_path = dirname($admin_dir);
	require_once($absolute_path."/components/com_{$cname}/cmsapi.interface.php");
	require_once($absolute_path."/components/com_{$cname}/v-admin-classes/{$cname}Toolbar.php");
}
$interface =& cmsapiInterface::getInstance();
$mosConfig_absolute_path = $interface->getCfg('absolute_path');
$mosConfig_lang = $interface->getCfg('lang');
$mosConfig_live_site = $interface->getCfg('live_site');
$mosConfig_sitename = $interface->getCfg('sitename');
$Large_Text_Len = 300;
$Small_Text_Len = 150;
if(file_exists($mosConfig_absolute_path."/components/com_$cname/language/".$mosConfig_lang.'.php')) require_once($mosConfig_absolute_path."/components/com_$cname/language/".$mosConfig_lang.'.php');
require_once($mosConfig_absolute_path."/components/com_$cname/language/english.php");
require_once($mosConfig_absolute_path."/components/com_$cname/$cname.class.php");

$classname = $cname.'Toolbar';
$toolbar = new $classname();