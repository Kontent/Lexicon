<?php

// Load constants and problem domain classes

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

$cname = strtolower(_THIS_COMPONENT_NAME);
$component_dir = str_replace('\\','/',dirname(__FILE__));
require_once ($component_dir."/com_{$cname}_constants.php");

if ('5' > phpversion() OR !defined('_ALIRO_IS_PRESENT')) {
	require_once($component_dir.'/cmsapi.interface.php');
	require_once ($component_dir.'/p-classes/cmsapiUserPage.php');
	require_once ($component_dir.'/p-classes/cmsapiConfiguration.php');
	// Specific to this component
	require_once ($component_dir.'/p-classes/glossaryGlossary.php');
	require_once ($component_dir.'/p-classes/glossaryEntry.php');
}
