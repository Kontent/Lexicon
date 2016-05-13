<?php

/**************************************************************
* This file is part of Glossary
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* See header in glossary.php for further details
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if (!defined('_THIS_COMPONENT_NAME')) define ('_THIS_COMPONENT_NAME', 'Glossary');
if (!defined('_COMPONENT_ADMIN_SIDE')) define ('_COMPONENT_ADMIN_SIDE', 1);
	
//error_reporting(E_ALL);

$cname = strtolower(_THIS_COMPONENT_NAME);
if (!defined('_ALIRO_IS_PRESENT')) {
	// Include files that contain classes
	$current_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($current_dir);
	$admin_dir = dirname($components_dir);
	$absolute_path = dirname($admin_dir);
	require_once($absolute_path."/components/com_{$cname}/cmsapi.interface.php");
	require_once($absolute_path."/components/com_{$cname}/c-admin-classes/cmsapiAdmin.php");
	require_once($absolute_path."/components/com_{$cname}/v-admin-classes/basicHTML.php");
	$interface =& cmsapiInterface::getInstance();
	require_once($absolute_path.'/administrator/includes/pageNavigation.php');
	require_once( $interface->getPath( 'class' ) );
}
// Make sure interface class is loaded to force definition of _REMOSITORY_VERSION
else $interface =& cmsapiInterface::getInstance();

new cmsapiAdminManager($cname);