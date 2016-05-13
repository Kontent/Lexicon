<?php
/**
 * glossarbot mambot 2.0beta for Mambo 4.5.1+
 * Support site: http://www.remository.com
 * All rights reserved
 * Mambo Open Source is Free Software
 * Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * 
 * @by Martin Brampton (martin@remository.com)
 * @package Mambo Open Source
 * @Copyright (C)2005 Martin Brampton
 */

defined('_VALID_MOS') OR defined( '_JEXEC' ) OR die('Direct Access to this location is not allowed.');

//if (mosGetParam($_REQUEST, 'task', '') != "edit") {
//	//mosCommonHTML::loadOverlib();
//	echo overlibInitCall ();
//}

if (!class_exists('cmsapiInterface')) require_once(dirname(__FILE__).'/cmsapi.interface.php');

if (defined('_JOOMLA_15PLUS')) {
	$mainframe->registerEvent( 'onPrepareContent', 'plgGlossarbot' );
	$mainframe->registerEvent( 'onAfterRender', 'plgGlossarJS' );
}
else $_MAMBOTS->registerFunction('onPrepareContent', 'botglossarbot');

function botglossarbot($published, &$row, &$params, $page=0) {
	static $botobject = null;
	if (null == $botobject) {
		$botobject = new glossarbot();
	}
	$botobject->botglossarbot($published, $row, $params, $page); 
}

function plgGlossarbot (&$row, &$params, $page=0) {
	$plugin =& JPluginHelper::getPlugin('content', 'glossarbot');
	$botParams = new JParameter( $plugin->params );
	
	static $botobject = null;
	if (null == $botobject) {
		$botobject = new glossarbot($botParams);
	}
	$botobject->botglossarbot($plugin->published, $row, $params, $page); 
}

function plgGlossarJS () {
	$glossarjs = <<<GLOSS_JS
	
				<script type="text/javascript"><!--//--><![CDATA[//><!--
			    var myTips = new Tips($$('.toolTipImg'), {
			        maxTitleChars: 500   //I like my captions a little long
			    });
/* Tips 1 */
var Tips1 = new Tips($$('.Tips1'));
 
/* Tips 2 */
var Tips2 = new Tips($$('.Tips2'), {
	initialize:function(){
		this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
	},
	onShow: function(toolTip) {
		this.fx.start(1);
	},
	onHide: function(toolTip) {
		this.fx.start(0);
	}
});
 
/* Tips 3 */
var Tips3 = new Tips($$('.Tips3'), {
	showDelay: 400,
	hideDelay: 400,
	fixed: true
});
 
/* Tips 4 */
var Tips4 = new Tips($$('.Tips4'), {
	className: 'custom'
});				//--><!]]></script>
	
GLOSS_JS;

	JResponse::appendBody($glossarjs);
}

	
class glossarbot {
	var $interface = null;
	var $database = null;
	var $param = null;
	var $show_frontpage = 0;
	var $initrun = 0;
	var $times = 1;
	var $glossary = null;
	var $defaultHTML = '';
	var $noChange = true;
	
	function glossarbot ($params=null) {
		$this->interface = cmsapiInterface::getInstance();
		$this->database = $this->interface->getDB();

		$this->param = is_null($params) ? $this->getGlossarbotParams() : $params;
		$this->show_frontpage = $this->param->get('show_frontpage', 1);
		$this->initrun = $this->param->get('run_default', 1);
		$this->times = $this->param->get('show_once_only', 1) ? 1 : -1;
		$this->glossary = $this->makeSearchable();
		
		$this->defaultHTML = $this->getDefaultHTML();
	}

	function getGlossarbotParams () {
		$this->database->setQuery('SELECT id FROM #__mambots WHERE element="glossarbot" AND folder="content"');
		$id = $this->database->loadResult();
		$mambot = new mosMambot($this->database);
		$mambot->load($id);
		return new mosParameters($mambot->params);
	}
	
	function getDefaultHTML () {
		return '';
		$this->database->setQuery('SELECT id FROM #__modules WHERE module="mod_glossarbox"');
		$id = $this->database->loadResult();
		$module = new mosModule($this->database);
		$module->load($id);
		$params = &new mosParameters($module->params);
		return $params->get('defaultHTML');
	}
	
	function makeSearchable () {
		$find_exact = $this->param->get('find_exact', 1);
		$this->database->setQuery('SELECT id,tterm,tdefinition FROM #__glossary WHERE published=1 ORDER BY length(tterm) DESC');
		$rows = $this->database->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) {
				$keyword = htmlentities(trim($row->tterm));
				if ($keyword) {
					$escaped_keyword = str_replace('/','\\/',$keyword);
					if ($find_exact) $regex='/(^|\W)'.$escaped_keyword.'($|\W)/i';
					else $regex='/'.$escaped_keyword.'/i';
					$key = strtoupper($keyword);
                	$definition = preg_replace("/(\015\012)|(\015)|(\012)/",'&nbsp;<br />',$row->tdefinition);
					$glossary[$key]=array('id' => $row->id,'term' => $keyword, 'desc' => $definition, 'regex' => $regex, 'found' => false);
				}
			}
			if (isset($glossary)) return $glossary;
		}
		return array();
	}

	function botglossarbot($published, &$row, &$params, $page = 0) {
		if (0 == count($this->glossary)) return true;

		// checking if glossarbot creates popups
		$run = $this->popupCheck($row->text, '{mosinfopop=enable}', 1, $this->initrun);
		$run = $this->popupCheck($row->text, '{glossarbot=enable}', 1, $run);
		$run = $this->popupCheck($row->text, '{mosinfopop=disable}', 0, $run);
		$run = $this->popupCheck($row->text, '{glossarbot=disable}', 0, $run);
	
		if ($run == 0 OR (!$show_frontpage AND 'com_frontpage' == $this->interface->getParam($_REQUEST, 'option'))) return true;

	    setlocale (LC_ALL, $this->interface->getCfg('locale'));
	    $this->noChange = true;
		foreach ($this->glossary as $key=>$entry) $length[] = $key;
		$minkeylen = strlen(min($length));
		$newContent = '';
		$htmlregex = '#(<a .*?</a\ *>|<script .*?</script\ *>|</?.*?>|\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>)#i';
		$bits = preg_split($htmlregex, $row->text);
		preg_match_all($htmlregex, $row->text, $matches);
		foreach ($bits as $i=>$bit) {
			if (strlen($bit) < $minkeylen) $newContent .= $bit;
			else $newContent .= $this->buildContent ($this->glossary, $bit, $this->times);
			if (isset($matches[0][$i])) $newContent .= $matches[0][$i];
		}
		$newContent = $this->convertEntries ($newContent, $this->glossary);
		if (stristr($newContent,'{glossar}')) $row->text = $this->showGlossary ($this->glossary, $newContent);
		else $row->text = $newContent;
		return true;
	}	
		
	function popupCheck (&$text, $symbol, $value, $default) {
		$newtext = str_replace($symbol, '', $text);
		if ($newtext == $text) return $default;
		else {
			$text = $newtext;
			return $value;
		}
	}
	
	function buildContent (&$glossary, $content, $times) {
		$task = $this->interface->getParam($_REQUEST, 'task');
		foreach ($glossary as $gkey=>$entry) {  // run through all glossary entries
			if ($times == 1 AND $entry['found']) continue;
			$id = $entry['id'];
			$newcontent = preg_replace($entry['regex'],"\$1{ShowGlossar:$id}\$2",$content,$times);
			if ($newcontent != $content) {
				$glossary[$gkey]['found'] = true;
				$content = $newcontent;
				if ($this->noChange AND 'edit' != $task) {
					$this->noChange = false;
					// mosCommonHTML::loadOverlib();
				}
			}
		}
		return $content;
	}
	
	function linkParams () {
		$keys = array('fgcolor','bgcolor','txtcolor','capcolor','width','position','alignment','offset_x','offset_y','outputmode','css');
		$defaults = array('#CCCCFF','#333399','#000000','#FFFFFF',300,'BELOW','RIGHT',10,10,0,'');
		foreach ($keys as $i=>$key) $parm[$key] = $this->param->get($key, $defaults[$i]);

		$mambotdir = defined('_JOOMLA_15PLUS') ? '/plugins' : '/mambots';
		if ($this->param->get('show_image', 1)) $parm['image'] = '<img src="' . $this->interface->getCfg('live_site') . $mambotdir. '/content/glossarbot/info.gif" border="0" align="top" alt="glossary term" />';
		else $parm['image'] = '';
		return $parm;
	}
	
	function makeLink (&$entry, &$parm, $defaultHTML) {
		$orgTerm = $entry['term'];
		$desc = $entry['desc'];
		$trimTerm = ltrim($orgTerm);
		// Default tooltip requires class of toolTipImg, fancy versions are Tips1, Tips2, Tips3 or Tips4
		// The selection should be made through a plugin parameter
		$tipclass = 'Tips2';
		if (defined('_JOOMLA_15PLUS')) return <<<MOO_POP
				
				<span title="$trimTerm::$desc" class="$tipclass glossarbot" style="{$parm['css']}">{$parm['image']}$trimTerm</span>
		
MOO_POP;

		switch ($parm['outputmode']) {
			case 1:
				$desc= preg_replace('/<br>|<br\/>|<br \/>|<p>/i', ' ', $desc);
				$link = '<a style="'.$parm['css'].'" href="javascript:void(0)" title="'.strip_tags($desc).'">' . $parm['image'] . $trimTerm.'</a>';
				break;
			case 2:
				//Get the defaul text of the module
				$link = '<a class="mosinfopop" style="'.$parm['css'].'" href="javascript:void(0)" onmouseover="javascript:document.getElementById(&quot;glossarbox&quot;).innerHTML=\'<b><u>'.$orgTerm.'</u></b><br>'.addslashes(htmlspecialchars($desc)).'\'" onmouseout="javascript:document.getElementById(&quot;glossarbox&quot;).innerHTML=\''.$defaultHTML.'\'">'.$parm['image'] . $trimTerm . '</a>';
				break;
			case 3:
				$link = '<a class="mosinfopop" style="'.$parm['css'].'" href="javascript:void(0)" onclick="return overlib(\'' . addslashes(htmlspecialchars($desc)) . '\', STICKY, CLOSECLICK, CAPTION, \'' . $orgTerm . '\',' . strtoupper($parm['position']) . ',' . strtoupper($parm['alignment']) . ', WIDTH, ' . $parm['width'] . ', FGCOLOR, \'' . $parm['fgcolor'] . '\', BGCOLOR, \'' . $parm['bgcolor'] . '\', TEXTCOLOR, \'' . $parm['txtcolor'] . '\', CAPCOLOR, \'' . $parm['capcolor'] . '\', OFFSETX, ' . $parm['offset_x'] . ', OFFSETY, ' . $parm['offset_y'] . ');">' . $parm['image'] . $trimTerm . '</a>';
				break;
			case 4:
				$link = '<a class="mosinfopop" style="'.$parm['css'].'" href="javascript:void(0)" onmouseover="return overlib(\'' . addslashes(htmlspecialchars($desc)) . '\', CAPTION, \'' . $orgTerm . '\',' . strtoupper($parm['position']) . ',' . strtoupper($parm['alignment']) . ', WIDTH, ' . $parm['width'] . ', FGCOLOR, \'' . $parm['fgcolor'] . '\', BGCOLOR, \'' . $parm['bgcolor'] . '\', TEXTCOLOR, \'' . $parm['txtcolor'] . '\', CAPCOLOR, \'' . $parm['capcolor'] . '\', OFFSETX, ' . $parm['offset_x'] . ', OFFSETY, ' . $parm['offset_y'] . ');" onmouseout="return nd();"
    			onclick="return overlib(\'' . addslashes(htmlspecialchars($desc)) . '\', STICKY, CLOSECLICK, CAPTION, \'' . $orgTerm . '\',' . strtoupper($parm['position']) . ',' . strtoupper($parm['alignment']) . ', WIDTH, ' . $parm['width'] . ', FGCOLOR, \'' . $parm['fgcolor'] . '\', BGCOLOR, \'' . $parm['bgcolor'] . '\', TEXTCOLOR, \'' . $parm['txtcolor'] . '\', CAPCOLOR, \'' . $parm['capcolor'] . '\', OFFSETX, ' . $parm['offset_x'] . ', OFFSETY, ' . $parm['offset_y'] . ');">' . $parm['image'] . $trimTerm . '</a>';
				break;
			default:
				$link = '<a class="mosinfopop" style="'.$parm['css'].'" href="javascript:void(0)" onmouseover="return overlib(\'' . addslashes(htmlspecialchars($desc)) . '\', CAPTION, \'' . $orgTerm . '\',' . strtoupper($parm['position']) . ',' . strtoupper($parm['alignment']) . ', WIDTH, ' . $parm['width'] . ', FGCOLOR, \'' . $parm['fgcolor'] . '\', BGCOLOR, \'' . $parm['bgcolor'] . '\', TEXTCOLOR, \'' . $parm['txtcolor'] . '\', CAPCOLOR, \'' . $parm['capcolor'] . '\', OFFSETX, ' . $parm['offset_x'] . ', OFFSETY, ' . $parm['offset_y'] . ');" onmouseout="return nd();" >' . $parm['image'] . $trimTerm . '</a>';
				break;
		}
	    return $link;
	}

	function convertEntries ($content, &$glossary) {
		// replace temporary {showdesc:id} tasks
		$before = array();
		$after = array();
		$parm = $this->linkParams();
		$defaultHTML = $this->getDefaultHTML();
		foreach ($glossary as $gkey=>$entry) {
			if ($entry['found']) {
				$before[] = '/{ShowGlossar:'.$entry['id'].'}/';
				$after[] = $this->makeLink($entry, $parm, $defaultHTML);
			}
		}
		if (count($before)) return preg_replace($before, $after, $content);
		return $content;
	}

	function showGlossary (&$glossary, $content) {
		foreach ($glossary as $gkey=>$entry) {
			if ($entry['found']) $temparray[$entry['term']] = $entry['desc'];
		}
		if (count($temparray) == 0) return;
		ksort($temparray);

		define ('_headline', $this->param->get('headline', "Nomenclature"));
		define ('_head_term',$this->param->get('head_term', "Term"));
		define ('_head_explanation', $this->param->get('head_explanation', "Description"));

		$show_headline = $this->param->get('show_headline', 1);
		$style = '2';
		if ($_SERVER['SCRIPT_NAME'] == "/index2.php") {
			$border = '1';
			$cellspacing = '0';
			$cellpadding = '2';
		}
		else {
			$border = '0';
			$cellspacing = '2';
			$cellpadding = '2';
		}
		// --- Array Sorting by Keyword

		$glossartable = '<table width="100%" cellpadding="1" cellspacing="0">';
		if ($show_headline) $glossartable .= '<tr><td width="100%" style="border-bottom: 1px solid #C9C9C9;border-top: 1px solid #C9C9C9;"><b>' . _headline . '</b></td></tr>';
		$glossartable .= '<tr><td><br><table border="' . $border . '" align="center" style="border: 1px solid #C9C9C9;" width="100%" cellpadding="' . $cellpadding . '" cellspacing="' . $cellspacing . '"><tr class="sectiontableheader"><td><b>' . _head_term . '</b></td><td><b>' . _head_explanation . '</b></td></tr>';
		foreach ($temparray as $keyword=>$definition) {
			$glossartable .= '<tr class="sectiontableentry' . $style . '"><td width="auto">' . $keyword . '&nbsp;</td><td>' . $definition . '</td></tr>';
			$style = ($style % 2) + 1;
		}
		$glossartable .= '</td></tr></table></td></tr><tr><td align="right"><a href="http://www.remository.com" target="_blank">&copy;2005 remository.com</a></td></tr></table>';
		return str_replace('{glossar}', $glossartable, $content);
	}
}