<?php

/*******************************************************************
* This file is a generic interface to Aliro, Joomla 1.5+, Joomla 1.0.x and Mambo
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://acmsapi.org
* To contact Martin Brampton, write to martin@remository.com
*
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

// Define the current version
if (!defined('_GLOSSARY_VERSION')) define('_GLOSSARY_VERSION', '3.43');

// Define CMS environment
if (!defined('_JOOMLA_15PLUS') AND defined('_JEXEC') AND !defined('_ALIRO_IS_PRESENT')) define ('_JOOMLA_15PLUS', 1);

$adirectory = dirname(__FILE__);
if (!defined('CMSAPI_LOCAL_DIRECTORY')) define ('CMSAPI_LOCAL_DIRECTORY', $adirectory);
$adirectory = dirname($adirectory);
if (!defined('CMSAPI_COMPONENT_DIRECTORY')) define ('CMSAPI_COMPONENT_DIRECTORY', $adirectory);
$adirectory = dirname($adirectory);
if (!defined('_CMSAPI_ABSOLUTE_PATH')) define ('_CMSAPI_ABSOLUTE_PATH', $adirectory);

if (!defined('_JOOMLA_15PLUS') AND !defined('_ALIRO_IS_PRESENT')) {
	@include_once(_CMSAPI_ABSOLUTE_PATH.'/includes/version');
	if (class_exists('joomlaVersion') AND !defined('_JOOMLA_10X')) define ('_JOOMLA_10X', 1);
	elseif (class_exists('version')) {
		$mamboversion = new version();
		if ($mamboversion->RELEASE >= '4.6' AND !defined('_MAMBO_46PLUS')) define ('_MAMBO_46PLUS', 1);
		elseif (!defined('_MAMBO_45MINUS')) define ('_MAMBO_45MINUS', 1);
	}
}


if (!defined('_CMSAPI_INTERFACE')) {

define('_CMSAPI_INTERFACE', 1);

class cmsapiDebug {

	function trace () {
	    static $counter = 0;
		$html = '';
		foreach(debug_backtrace() as $back) {
		    if (isset($back['file']) AND $back['file']) {
			    $html .= '<br />'.$back['file'].':'.$back['line'];
			}
		}
		$counter++;
		if (1000 < $counter) {
		    echo $html;
		    die (T_('Program killed - Probably looping'));
        }
		return $html;
	}

}

if (defined('_JOOMLA_15PLUS') AND defined('_COMPONENT_ADMIN_SIDE')) {

	class cmsapiMenuBar extends JToolBarHelper {
		function startTable () {
		}
		function endTable () {
		}
	}

	jimport('joomla.html.pagination');
	class cmsapiPageNav extends Jpagination {
		function writeLimitBox () {
			return $this->getLimitBox();
		}
		function writePagesLinks () {
			return $this->getPagesLinks();
		}
		function writePagesCounter () {
			return $this->getPagesCounter();
		}
		function listFormEnd ($componentname, $pagecontrol=true) {
			$act = $_REQUEST['act'];
			$hiddenhtml = <<<HIDDEN_HTML
				
				<div>
					<input type="hidden" name="option" value="$componentname" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="limitstart" value="" />
					<input type="hidden" name="act" value="$act" />
					<input type="hidden" name="boxchecked" value="0" />
				</div>
				
HIDDEN_HTML;

			if ($pagecontrol) {
				$displaynum = _CMSAPI_DISPLAY_NUMBER;
				$links = $this->writePagesLinks();
				$limits = $this->writeLimitBox();
				$counter = $this->writePagesCounter();
				echo <<<PAGE_CONTROL1

			<tfoot>
			<tr>
	    		<td colspan="15">
	    		<del class="container"><div class="pagination">
					<div class="limit">$displaynum
						$limits
					</div>
					$links
					<div class="limit">
						$counter
					</div>
				</div></del>
				$hiddenhtml
				</td>
			</tr>
			</tfoot>

PAGE_CONTROL1;

			}
			else {
				echo <<<END_PAGE

			<tfoot>
			<tr>
	    		<th align="center" colspan="13">
	    			&nbsp;
	    			$hiddenhtml
				</th>
			</tr>
			</tfoot>

END_PAGE;

			}
		}
	}

	jimport('joomla.html.pane');
	//Force loading of JPane to compensate for un-clever autoload
	$dummy = new JPane();
	unset($dummy);
	class cmsapiPane extends JPaneTabs {
		function startTab ($tabText, $tabid) {
			echo parent::startPanel ($tabText, $tabid);
		}
		function endTab () {
			echo parent::endPanel();
		}
		function startPane ($paneid) {
			echo parent::startPane($paneid);
		}
		function endPane () {
			echo parent::endPane();
		}
	}
}

if (defined('_JOOMLA_15PLUS')) {
	class cmsapiDBTable extends JTable {
		function cmsapiDBTable ($table, $key, &$db) {
			$this->__construct ($table, $key, $db);
		}
	}

}
else {
	if (defined(_THIS_COMPONENT_NAME.'_ADMIN_SIDE')) {
		if (!defined('_ALIRO_IS_PRESENT')) {
			$remopath = str_replace('\\','/',dirname(__FILE__));
			$compath = dirname($remopath);
			$absolute_path = dirname($compath);
			require_once ($absolute_path.'/administrator/includes/menubar.html.php');
			require_once ($absolute_path.'/administrator/includes/pageNavigation.php');
		}
		
		class cmsapiMenuBar extends mosMenuBar {}

		class cmsapiPageNav extends mosPageNav {
			function listFormEnd ($componentname, $pagecontrol=true) {
				$act = $_REQUEST['act'];
				if ($pagecontrol) {
					echo <<<PAGE_CONTROL1

			<tfoot>
			<tr>
	    		<th align="center" colspan="13">

PAGE_CONTROL1;
					$this->writePagesLinks();
					echo <<<PAGE_CONTROL2

			</th>
			</tr>
			<tr>
				<td align="center" colspan="13">

PAGE_CONTROL2;
					$this->writeLimitBox();
					$this->writePagesCounter();
					echo <<<PAGE_CONTROL3

			</td>
			</tr>

PAGE_CONTROL3;

				}
				else {
					echo <<<END_PAGE

			<tfoot>
			<tr>
	    		<th align="center" colspan="13">&nbsp;</th>
			</tr>

END_PAGE;

				}
				echo <<<HIDDEN_HTML
				
			<tr>
				<td>
					<input type="hidden" name="option" value="$componentname" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="act" value="$act" />
					<input type="hidden" name="boxchecked" value="0" />
				</td>
			</tr>
			</tfoot>
				
HIDDEN_HTML;

			}
		}

		class cmsapiPane extends mosTabs {
			function cmsapiPane () {
				parent::mosTabs(0);
			}
		}
	}

	class cmsapiDBTable extends mosDBTable {
		function cmsapiDBTable ($table, $key, &$db) {
			$this->mosDBTable ($table, $key, $db);
		}
	}

}

if (defined('_JOOMLA_15PLUS') AND !defined('_JLEGACY')) {

	function initEditor () {
		$editor =& JEditor::getInstance();
		$editor->initialise();
	}
	
	function getEditorContents($editorArea, $hiddenField) {
		$editor =& JEditor::getInstance();
		echo $editor->getEditorContents( $hiddenField );
	}

	function editorArea($name, $content, $hiddenField, $width, $height, $col, $row) {
		$editor =& JEditor::getInstance();
		echo $editor->display($hiddenField, $content, $width, $height, $col, $row);
	}

}

class cmsapiInterface {

	var $mainframe;
	var $absolute_path;
	var $live_site;
	var $cachepath;
	var $lang;
	var $sitename;

	function cmsapiInterface () {
		$this->absolute_path = dirname(dirname(dirname(__FILE__)));
		$this->getMainFrame();
		if (defined('_JOOMLA_15PLUS')) $this->live_site = substr(JURI::root(), 0, -1);
	}

	function purify ($string) {
		return $string;
	}
    
	function class_exists ($string, $autoload=false) {
		if (PHP_VERSION >= '5') return class_exists($string, $autoload);
		return class_exists($string);
	}

	function getMainFrame () {
		if (!is_object($this->mainframe)) {
			if (!defined('_JLEGACY') AND is_callable(array('mosMainFrame', 'getInstance'))) $this->mainframe =& mosMainFrame::getInstance();
			else {
				global $mainframe;
				$this->mainframe =& $mainframe;
			}
		}
	}

	function &getInstance () {
        static $instance;
        if (!is_object($instance)) $instance = new cmsapiInterface();
        return $instance;
    }

	function rawGetCfg ($string) {
		if (isset($this->$string)) return $this->$string;
		if ((defined('_ALIRO_IS_PRESENT') OR method_exists($this->mainframe, 'getCfg')) AND !is_null($result = $this->mainframe->getCfg($string))) return $result;
		else {
			if (!empty($this->$string)) return $this->$string;
			if (defined('_JOOMLA_15PLUS')) {
				$this->live_site = substr(JURI::root(), 0, -1);
				$lang =& JFactory::getLanguage();
				$this->lang = $lang->get('backwardlang');
				$this->cachepath = JPATH_CACHE;
				return (empty($this->$string)) ? '' : $this->$string;
			}
			if (defined('_ALIRO_IS_PRESENT')) die ('Could not find configuration item '.$string);
			include ($this->absolute_path.'/configuration.php');
			$this->live_site = $mosConfig_live_site;
			$this->lang = $mosConfig_lang;
			$this->sitename = $mosConfig_sitename;
			$configitem = 'mosConfig_'.$string;
			$this->$string = $$configitem;
			return $$configitem;
		}
	}
	
	function getCfg ($string) {
		$result = $this->rawGetCfg($string);
		if ('live_site' == $string OR 'absolute_path' == $string) {
			if ('/' == substr($result,-1)) $result = substr($result,0,-1);
		}
		return $result;
	}

	function getTemplate () {
		return $this->mainframe->getTemplate();
	}

	function appendPathWay ($name, $link) {
		if (defined('_JOOMLA_15PLUS')) $this->mainframe->appendPathWay($name, $link);
		elseif (defined('_MAMBO_46PLUS')) {
			$pathway =& mosPathway::getInstance();
			$pathway->addItem($name, $link);
		}
		else {
			$url = $this->sefRelToAbs($link);
			$url = preg_replace ('/\&([^amp;])/', '&amp;$1', $url);
			$this->mainframe->appendPathWay('<a href="'.$url.'">'.$name.'</a>');
		}
	}

	function &getDB () {
		if (defined('_JOOMLA_15PLUS')) $database =& JFactory::getDBO();
		elseif (cmsapiInterface::class_exists('mamboDatabase', false)) $database =& mamboDatabase::getInstance();
		else global $database;
		return $database;
	}

	function getEscaped ($string) {
		$database =& $this->getDB();
		return $database->getEscaped($string);
	}

	function getParam (&$array, $name, $default='') {
		if (isset($array[$name])) {
			if (is_numeric($default)) return intval($array[$name]);
			else return $this->purify($array[$name]);
		}
		else return $default;
	}

	function getUser () {
		if (defined('_JOOMLA_15PLUS')) $my = JFactory::getUser();
		elseif (is_callable(array('mamboCore','get'))) {
			if (mamboCore::is_set('currentUser')) $my = mamboCore::get('currentUser');
			else $my =& aliroUser::getInstance();
		}
		else global $my;
		return $my;
	}

	function getIdentifiedUser ($id) {
		if (defined('_JOOMLA_15PLUS')) {
			$my =& new JUser($id);
			return $my;
		}
		$database =& $this->getDB();
		$my =& new mosUser($database);
		$my->load($id);
		return $my;
	}

	function getCurrentItemid () {
		if (is_callable(array('mamboCore','get'))) $Itemid =& mamboCore::get('Itemid');
		else global $Itemid;
		return intval($Itemid);
	}

	function getUserStateFromRequest ($var_name, $req_name, $var_default=null) {
		$this->getMainFrame();
		$mainframe = $this->mainframe;
		if (isset($var_default) AND is_numeric($var_default)) $forcenumeric = true;
		else $forcenumeric = false;
		if (isset($_REQUEST[$req_name])) {
			if ($forcenumeric) $mainframe->setUserState($var_name, intval($_REQUEST[$req_name]));
			else $mainframe->setUserState($var_name, $_REQUEST[$req_name]);
		}
        elseif (isset($var_default) AND !isset($mainframe->userstate[$var_name])) $mainframe->setUserState($var_name, $var_default);
        return $mainframe->getUserState($var_name);
	}

	function getPath ($name, $option='') {
		if (defined('_JOOMLA_15PLUS')) return JApplicationHelper::getPath($name, $option);
		$this->getMainFrame();
		return $this->mainframe->getPath($name, $option);
	}

	function setPageTitle ($title) {
		$this->getMainFrame();
		$canSetTitle = array($this->mainframe, 'SetPageTitle');
		if (is_callable($canSetTitle)) $this->mainframe->SetPageTitle($title);
	}

	function prependMetaTag ($tag, $content) {
		$this->getMainFrame();
		if (method_exists($this->mainframe, 'prependMetaTag')) $this->mainframe->prependMetaTag($tag, $content);
	}

	function addCustomHeadTag ($tag) {
		$this->getMainFrame();
		$this->mainframe->addCustomHeadTag($tag);
	}

	function addMetaTag ($name, $content, $prepend='', $append='') {
		$this->getMainFrame();
		$this->mainframe->addMetaTag($name, $content, $prepend='', $append='');
	}

	function redirect ($url, $msg='') {
    	if (defined('_JOOMLA_15PLUS')) $this->mainframe->redirect($url, $msg);
    	else mosRedirect($url, $msg);
    }

    function &makePageNav ($total, $limitstart, $limit) {
		$pagenav =& new cmsapiPageNav($total, $limitstart, $limit);
    	return $pagenav;
    }

    function triggerMambots ($event, $args=null, $doUnpublished=false) {
    	global $_MAMBOTS;
    	if (defined('_JOOMLA_15PLUS')) $handler = $this->mainframe;
    	elseif (defined('_ALIRO_IS_PRESENT')) $handler =& aliroMambotHandler::getInstance();
    	else $handler = $_MAMBOTS;
    	return $handler->trigger($event, $args, $doUnpublished);
    }

    function getEditorContents ($hiddenField) {
    	if (defined('_JOOMLA_15PLUS')) {
    		$editor =& JFactory::getEditor();
    		$editor->getContent ($hiddenField);
    	}
    	else getEditorContents ($hiddenField, $hiddenField);
    }

	function editorArea($name, $content, $hiddenField, $width, $height, $col, $row) {
		echo $this->editorAreaText($name, $content, $hiddenField, $width, $height, $col, $row);
	}

	function editorAreaText ($name, $content, $hiddenField, $width, $height, $col, $row) {
		if (defined('_JOOMLA_15PLUS')) {
			$editor =& JFactory::getEditor();
			return $editor->display($hiddenField, $content, $width, $height, $col, $row);
		}
		else {
			$results = $this->triggerMambots('onEditorArea', array( $name, $content, $hiddenField, $width, $height, $col, $row ) );
			$html = '';
			foreach ($results as $result) $html .= trim($result);
			return $html;
		}
	}
	
	function makeImageURI ($imageName, $width=32, $height=32, $title='') {
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$element = '<img src="';
		$element .= $this->getCfg('live_site')."/components/com_{$cname}/images/".$imageName;
		$element .= '" width="';
		$element .= $width;
		$element .= '" height="';
		$element .= $height;
		if ($title) {
			$element .= '" title="';
			$element .= $title;
		}
		$element .= '" alt="" />';
		return $element;
	}

	function objectSort ($objarray, $property, $direction='asc') {
		$GLOBALS['cmsapiSortProperty'] = $property;
		$GLOBALS['cmsapiDirection'] = strtolower($direction);
		usort( $objarray, create_function('$a,$b','
	        global $cmsapiSortProperty, $cmsapiDirection;
	        $result = strcmp($a->$cmsapiSortProperty, $b->$cmsapiSortProperty);
	        return \'asc\' == $cmsapiDirection ? $result : -$result;' ));
		return $objarray;
	}
	
	function sefRelToAbs ($link) {
		if (defined('_JOOMLA_15PLUS')) return JRoute::_($link);
		else return sefRelToAbs($link);
	}
	
	function sendMail ($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
		if (defined('_JOOMLA_15PLUS')) return JUTility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname );
		else return mosMail ($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}

}

}