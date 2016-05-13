<?php

/**************************************************************
* This file is part of Glossary
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Please see glossary.php for more details
*/

class cmsapiAdminManager {
	var $plugin_name = '';
	var $act = '';
	var $actname = '';
	var $task = '';
	var $limitstart = 0;
	var $limit = 0;
	var $cfid = 0;
	var $currid = 0;
	var $c_classes_path = '';
	var $v_classes_path = '';

	function cmsapiAdminManager ($plugin_name) {
		$interface =& cmsapiInterface::getInstance();
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$mosConfig_live_site = $interface->getCfg('live_site');
		$style = <<<ADMIN_STYLE
<link rel="stylesheet" href="$mosConfig_live_site/administrator/components/com_$cname/admin.css" type="text/css" />
ADMIN_STYLE;
		if (defined('_MAMBO_46PLUS') OR defined ('_MAMBO_45MINUS')) echo $style;
		else $interface->addCustomHeadTag($style);
		// Include files that contain definitions
		$configuration = cmsapiConfiguration::getInstance();
		// Need to set all the config variables in case any are used in the language file
		foreach (get_object_vars($configuration) as $k=>$v) $$k = $configuration->$k;
		$mosConfig_sitename = $interface->getCfg('sitename');
		$mosConfig_live_site = $interface->getCfg('live_site');
		$mosConfig_absolute_path = $interface->getCfg('absolute_path');
		if (file_exists($mosConfig_absolute_path."/components/com_$cname/$cname.class.php")) require_once($mosConfig_absolute_path."/components/com_$cname/$cname.class.php");
		$lang = $configuration->language ? $configuration->language : $interface->getCfg('lang');
		if (file_exists($mosConfig_absolute_path."/components/com_$cname/language/".$lang.'.php')) require_once($mosConfig_absolute_path."/components/com_$cname/language/".$lang.'.php');
		if ('english' != $lang AND file_exists($mosConfig_absolute_path."/components/com_$cname/language/english.php")) require_once($mosConfig_absolute_path."/components/com_$cname/language/english.php");
		$this->plugin_name = $plugin_name;
		$this->c_classes_path = $this->v_classes_path = $mosConfig_absolute_path."/components/com_$cname/";
		$this->c_classes_path .= 'c-admin-classes/';
		$this->v_classes_path .= 'v-admin-classes/';
		$this->noMagicQuotes();
		if ($this->act = $interface->getParam ($_REQUEST, 'act', 'cpanel'));
		else $this->act = 'cpanel';
		if ($this->task = $interface->getParam($_REQUEST, 'task', 'list'));
		else $this->task = 'list';
		if ('cpanel' == $this->task) $this->act = 'cpanel';
		$_REQUEST['act'] = $this->act;
		$this->actname = strtoupper(substr($this->act,0,1)).strtolower(substr($this->act,1));
		$default_limit  = $interface->getUserStateFromRequest( "viewlistlimit", 'limit', $interface->getCfg('list_limit') );
		$this->limit = intval( $interface->getParam( $_REQUEST, 'limit', $default_limit ) );
		if (1 > $this->limit) $this->limit = 99999;
		$this->limitstart = intval( $interface->getParam( $_REQUEST, 'limitstart', 0 ) );
		$this->cfid = $interface->getParam($_REQUEST, 'cfid', array(0));
		if (is_array( $this->cfid )) {
			foreach ($this->cfid as $key=>$value) $this->cfid[$key] = intval($value);
			$this->currid=$this->cfid[0];
		}
		else $this->currid = intval($this->cfid);
		$control_class = $this->plugin_name.'Admin'.$this->actname;
		if (file_exists($this->c_classes_path.$control_class.'.php')) {
			require_once ($this->c_classes_path.$control_class.'.php');
			if (class_exists($control_class)) {
				$controller =& new $control_class($this);
				$task = $this->task.'Task';
				if (method_exists($controller,$task)) $controller->$task();
				else trigger_error(sprintf(_CMSAPI_METHOD_NOT_PRESENT, $this->plugin_name, $task, $control_class));
			}
			else trigger_error(sprintf(_CMSAPI_CLASS_NOT_PRESENT, $this->plugin_name, $control_class));
		}
		else {
			$view_class = 'list'.$this->actname.'HTML';
			$controller = new cmsapiAdminControllers($this);
			$view = $this->newHTMLClassCheck ($view_class, $controller, 0, '');
			if ($view AND $this->checkCallable($view, 'view')) $view->view();
		}
	}

	function noMagicQuotes () {
		// Is magic quotes on?
		if (get_magic_quotes_gpc()) {
			// Yes? Strip the added slashes
			$_REQUEST = $this->remove_magic_quotes($_REQUEST);
			$_GET = $this->remove_magic_quotes($_GET);
			$_POST = $this->remove_magic_quotes($_POST);
			$_FILES = $this->remove_magic_quotes($_FILES, 'tmp_name');
		}
		set_magic_quotes_runtime(0);
	}

	function remove_magic_quotes ($array, $exclude='') {
		foreach ($array as $k => $v) {
			if (is_array($v)) $array[$k] = $this->remove_magic_quotes($v, $exclude);
			elseif ($k != $exclude) $array[$k] = stripslashes(stripslashes($v));
		}
		return $array;
	}

	function check_selection ($text) {
		if (!is_array($this->cfid) OR count( $this->cfid ) < 1) {
			echo "<script> alert('".$text."'); window.history.go(-1);</script>\n";
			exit;
		}
	}

	function newHTMLClassCheck ($name, &$controller, $total_items, $clist) {
		$controller->makePageNav($this, $total_items);
		if (file_exists($this->v_classes_path.$name.'.php')) require_once ($this->v_classes_path.$name.'.php');
		if (class_exists($name)) return new $name ($controller, $this->limit, $clist);
		trigger_error(sprintf(_CMSAPI_CLASS_NOT_PRESENT, $this->plugin_name, $name));
		return false;
	}

	function checkCallable ($object, $method) {
		if (method_exists($object, $method)) return true;
		$name = get_class($object);
		trigger_error(sprintf("Component $this->plugin_name error: attempt to use non-existent method $method in $name", $this->plugin_name, $method, $name));
		return false;
	}

}

class cmsapiAdminControllers {
	var $remUser = '';
	var $configuration = '';
	var $interface = '';
	var $admin = '';
	var $pageNav = '';
	var $idparm = 0;

	function cmsapiAdminControllers ($admin) {
		$this->admin = $admin;
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->configuration = cmsapiConfiguration::getInstance();
		$this->interface =& cmsapiInterface::getInstance();
		$this->remUser = $this->interface->getUser();
		$this->idparm = $this->interface->getParam($_REQUEST, 'id', 0);
	}

	function makePageNav (&$admin, $total) {
		$this->pageNav =& $this->interface->makePageNav( $total, $admin->limitstart, $admin->limit );
	}

	function backTask() {
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->interface->redirect( "index2.php?option=com_$cname");
	}
	
	function publishToggle ($table, $idarray, $pvalue) {
		foreach ($idarray as $key=>$value) $idarray[$key] = intval($value);
		$idlist = implode(',', $idarray);
		if ($idlist) {
			$pvalue = intval($pvalue);
			$database = $this->interface->getDB();
			$database->setQuery("UPDATE $table SET published = $pvalue WHERE id IN ($idlist)");
			$database->query();
		}
	}

	function error_popup ($message) {
		echo "<script> alert('".$message."'); window.history.go(-1); </script>\n";
	}

}