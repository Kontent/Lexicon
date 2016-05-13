<?php

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if (!defined('_THIS_COMPONENT_NAME')) define ('_THIS_COMPONENT_NAME', 'Glossary');
if (!defined ('_COMPONENT_DEFAULT_ORDERING')) define ('_COMPONENT_DEFAULT_ORDERING', 1);

$cname = strtolower(_THIS_COMPONENT_NAME);
if (!defined('_ALIRO_IS_PRESENT')) {
	$current_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($current_dir);
	$absolute_path = dirname($components_dir);
	require_once($current_dir.'/cmsapi.interface.php');
	$interface =& cmsapiInterface::getInstance();
	require_once( $interface->getPath( 'class' ) );
	// require_once( $interface->getPath( 'front_html' ) );
}
// Make sure interface class is loaded to force definition of _REMOSITORY_VERSION
else $interface =& cmsapiInterface::getInstance();
require_once ($current_dir."/com_{$cname}_constants.php");

//error_reporting(E_ALL);

class cmsapiUserAdmin {
	var $magic_quotes_value = 0;
	var $c_classes_path = '';
	var $v_classes_path = '';

	function cmsapiUserAdmin ($control_name, $alternatives, $default, $title) {
		$component = strtolower(_THIS_COMPONENT_NAME);
		$interface =& cmsapiInterface::getInstance();
		// Is magic quotes on?
		if (get_magic_quotes_gpc()) {
		 	// Yes? Strip the added slashes
			$_REQUEST = $this->remove_magic_quotes($_REQUEST);
			$_GET = $this->remove_magic_quotes($_GET);
			$_POST = $this->remove_magic_quotes($_POST);
			$_FILES = $this->remove_magic_quotes($_FILES, 'name');
		}
		$this->magic_quotes_value = get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->c_classes_path = $this->v_classes_path = $interface->getCfg('absolute_path')."/components/com_$cname/";
		$this->c_classes_path .= 'c-classes/';
		$this->v_classes_path .= 'v-classes/';
		$interface->SetPageTitle($title);
		$func = $interface->getParam ($_REQUEST, $control_name, $default);
		if (isset($alternatives[$func])) $method = $alternatives[$func];
		else $method = $func;
		$qual_method = $component.'_'.$method;
		$classname = $qual_method.'_Controller';
		$classfile = $this->c_classes_path.$classname.'.php';
		require_once($this->c_classes_path.$component.'Controllers.php');
		if (file_exists($classfile)) require_once ($classfile);
		$no_html = $interface->getParam($_REQUEST, 'no_html', 0);
		if (!$no_html) {
			$propername = _THIS_COMPONENT_NAME;
			echo "\n<!-- Start of $propername HTML -->";
			echo "\n<div id='$cname'>";
		}
		if (class_exists($classname)) {
			$controller =& new $classname($this);
			if (method_exists($controller,$qual_method)) $controller->$qual_method($func);
			else {
				header ('HTTP/1.1 404 Not Found');
				trigger_error("Component $component error: attempt to use non-existent method $qual_method in $controller");
			}
		}
		else {
			header ('HTTP/1.1 404 Not Found');
			trigger_error("Component $component error: attempt to use non-existent class $classname");
		}
		if (!$no_html) {
			echo "\n</div>";
			echo "\n<!-- End of $propername HTML -->";
		}
		$this->restore_magic_quotes();
	}

	function &remove_magic_quotes (&$array, $keyname=null) {
		foreach ($array as $k => $v) {
			if (is_array($v)) $array[$k] = $this->remove_magic_quotes($v, $keyname);
			else if (empty($keyname) OR $k == $keyname) $array[$k] = stripslashes($v);
		}
		return $array;
	}

	function restore_magic_quotes () {
		set_magic_quotes_runtime($this->magic_quotes_value);
	}

}

$alternatives = array ();

$admin =& new cmsapiUserAdmin('task', $alternatives, 'list', _THIS_COMPONENT_NAME);

