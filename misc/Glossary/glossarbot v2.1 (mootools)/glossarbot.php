<?php
/**
 * glossarbot mambot 2.0 beta for Mambo 4.5.1+/Joomla! 1.0.x
 * Support site: http://www.remository.com
 * All rights reserved
 * Glossary is Free Software
 * Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 *
 * @by Martin Brampton (martin@remository.com)
 * @Copyright (C)2007 Martin Brampton
 *
 * With thanks to Ron Severdia for pushing the functionality and helping with the CSS and JS
 */

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

$_MAMBOTS->registerFunction('onPrepareContent', 'botglossarbot');

function botglossarbot ($published, &$row, &$params, $page = 0) {
	static $glossarbotobj = null;
	if (is_null($glossarbotobj)) $glossarbotobj = new bot_glossarbot;
	$glossarbotobj->perform('onPrepareContent', null, $published, $row, $params, $page);
}

class bot_glossarbot {
	var $botparms = null;
	var $db = null;
	var $allterms = array();
	var $termsused = array();
	var $regexes = array();
	var $defs = array();
	var $id = 0;
	var $parm = array();
	var $defaultHTML = '';
	var $live_site = '';
	var $quotestring = '\'';
	var $linkdone = false;
	var $switchdone = false;
	var $nofrontpage = false;

	function bot_glossarbot () {
		global $database;
		$this->db = $database;
		$this->live_site = $this->getCfg('live_site');
		$switcherJS = <<<SCRIPT
		<script type="text/javascript" src="$this->live_site/mambots/content/glossarbot.js"></script>

		<script type="text/javascript" src="$this->live_site/mambots/content/glossarbot.css"></script>

SCRIPT;
		$this->addCustomHeadTag($switcherJS);
		$this->botparms = $this->getGlossarbotParams();
		$option = $this->getParam($_REQUEST, 'option');
		if ('com_frontpage' == $option AND !$this->botparms->get('show_frontpage', 1)) $this->nofrontpage = true;
		$this->loadTerms();
	}

	function getParam ($arr, $name, $default=null, $mask=null) {
		return mosGetParam($arr, $name, $default, $mask);
	}

	function addCustomHeadTag ($string) {
		global $mainframe;
		$mainframe->addCustomHeadTag ($string);
	}

	function getCfg ($property) {
		global $mainframe;
		return $mainframe->getCfg($property);
	}

	function trigger ($event, $args) {
		global $_MAMBOTS;
		$results = $_MAMBOTS->trigger($event, $args);
		if (isset($results[0])) return $results[0];
		switch ($event) {
			case 'fixGlossarbotTerm':
				return str_replace('\'', $this->quotestring, $args[0]);
				return $args[0];
			case 'fixGlossarbotJS':
				$escquotes = str_replace('\'', $this->quotestring, $args[0]);
				return $this->javascript_escape ($escquotes);
			default:
				return null;
		}
	}

	function javascript_escape($str) {
		$new_str = '';
		for($i = 0; $i < strlen($str); $i++) {
			$new_str .= '\x'.dechex(ord($str[$i]));
		}
		return $new_str;
	}

	function perform ($event, $botparams, $published, &$row, &$params, $page = 0) {
	    setlocale (LC_ALL, $this->getCfg('locale'));

		$show_frontpage = $this->botparms->get('show_frontpage', 1);
		$run = $this->botparms->get('run_default', 1);
		$times = $this->botparms->get('show_once_only', 1) ? 1 : -1;

		// checking if glossarbot creates popups
		$run = $this->popupCheck($row->text, '[mosinfopop=enable]', 1, $run);
		$run = $this->popupCheck($row->text, '[glossarbot=enable]', 1, $run);
		$run = $this->popupCheck($row->text, '[mosinfopop=disable]', 0, $run);
		$run = $this->popupCheck($row->text, '[glossarbot=disable]', 0, $run);

		// If bot is not published, once any markers have been removed, that is all.
		// If bot is published, but markers indicate not to run, that is all.
		// If we are on the front page and no processing on front page is set, that is all.
		if (!$published OR $run == 0 OR $this->nofrontpage) {
			$row->text = preg_replace('/\[glossarswitch\]/i', '', $row->text);
			$row->text = preg_replace('/\[glossar\]/i', '', $row->text);
			return true;
		}

		$this->checkForQuotes($row->text);

		$this->termsused = array();
		if (count($this->allterms)) {
			$this->linkParams();
			$newContent = '';
			$find_exact = $this->botparms->get('find_exact', 1);
			$htmlregex = '#(<a .*?</a\ *>|<script .*?</script\ *>|</?.*?>|\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>)#i';
			$bits = preg_split($htmlregex, $row->text);
			preg_match_all($htmlregex, $row->text, $matches);
			$i=0;
			$textstring = implode(' ',$bits);
			$pre = $find_exact ? '/(^|\P{L})(' : '/()(';
			$post = $find_exact ? ')($|\P{L})/i' : ')/i';
			foreach ($this->allterms as $this->id=>$term) {
				preg_replace_callback($pre.$term.$post, array($this, 'termFound'), $textstring, 1);
				$i++;
				if ($i >= 10000) break;
			}

			// No longer need this potentially very large array
			// $this->allterms = array();
			if (count($this->termsused)) {
				$this->loadDefinitions($find_exact);
				foreach ($this->regexes as $this->id=>$regex) {
					$bits = preg_replace_callback($regex, array(&$this, 'replaceTerm'), $bits, $times);
				}
			}
			// Not required any longer, so save space:
			$this->regexes = array();
			foreach ($bits as $i=>$bit) {
				$newContent .= $bit;
				if (isset($matches[0][$i])) $newContent .= $matches[0][$i];
			}
		}
		else $newContent = $row->text;
		if (stristr($newContent, '[glossarswitch]')) $newContent = $this->addSwitcher($newContent);
		if (stristr($newContent,'[glossar]')) $row->text = $this->showGlossary ($glossary, $newContent);
		else $row->text = $newContent;
		return true;
	}

	function checkForQuotes ($string) {
		if (false !== strpos($string, '&#39;')) $this->quotestring = '&#39';
		elseif (false !== strpos($string, '&#039;')) $this->quotestring = '&#039;';
	}

	function replaceTerm ($matches) {
		$split = explode($matches[2], $matches[0]);
		return $split[0].$this->makeLink($matches[2], $this->defs[$this->id]['definition']).$split[1];
	}

	function getGlossarbotParams () {
		$this->db->setQuery('SELECT id FROM #__mambots WHERE element="glossarbot" AND folder="content"');
		$id = $this->db->loadResult();
		$mambot = new mosMambot($this->db);
		$mambot->load($id);
		return new mosParameters($mambot->params);
	}

	function getDefaultHTML () {
		$this->db->setQuery('SELECT id FROM #__modules WHERE module="mod_glossarbox"');
		$id = $this->db->loadResult();
		$module = new mosModule($this->db);
		$module->load($id);
		$params = &new mosParameters($module->params);
		$this->defaultHTML = $params->get('defaultHTML');
	}

	function popupCheck (&$text, $symbol, $value, $default) {
		$newtext = str_replace($symbol, '', $text);
		if ($newtext == $text) return $default;
		else {
			$text = $newtext;
			return $value;
		}
	}

	function loadTerms () {
		static $from = array('\\', '/', '\'', '^', '$', '[', ']', '|', '(', ')');
		static $to = array('', '\\/', '&#39;', '\\^', '\\$', '\\[', '\\]', '\\|', '\\(', '\\)', '');
		$this->db->setQuery('SELECT id,tterm FROM #__glossary WHERE published=1');
		$rows = $this->db->loadObjectList();
		$max = 0;
		if ($rows) foreach ($rows as $row) {
			$this->allterms[$row->id] = str_replace($from, $to, trim($row->tterm));
			// if ($max < 100) var_dump($this->allterms[$row->id]);
			$max++;
		}
	}

	function termFound ($arr) {
		$this->termsused[$this->id] = 1;
		return '';
	}

	function loadDefinitions ($find_exact) {
		static $from = array('/', '\'');
		static $to = array('\\/', '&#39;');
		$deflist = implode(',', array_keys($this->termsused));
		$this->db->setQuery("SELECT id, tterm, tdefinition FROM #__glossary WHERE id IN ($deflist)");
		$defs = $this->db->loadObjectList();
		$this->defs = array();
		if ($defs) foreach ($defs as $def) {
			$definition = preg_replace("/(\015\012)|(\015)|(\012)/",'&nbsp;<br />',$def->tdefinition);
			$this->defs[$def->id]['definition'] = $this->trigger('fixGlossarbotJS', array($definition));
			$fixedupterm = $this->trigger('fixGlossarbotTerm', array(trim($def->tterm)));
			$escaped_keyword = str_replace('/', '\\/', $fixedupterm);
			$this->regexes[$def->id] = $find_exact ? '/(^|\P{L})('.$escaped_keyword.')($|\P{L})/i' : '/()('.$escaped_keyword.')/i';
		}
	}

	function linkParams () {
		if ($this->linkdone) return;
		$this->linkdone = true;
		$keys = array('fgcolor','bgcolor','txtcolor','capcolor','width','position','alignment','offset_x','offset_y','outputmode','css', 'offcss');
		$defaults = array('#CCCCFF','#333399','#000000','#FFFFFF',300,'BELOW','RIGHT',10,10,0,'cursor:help;border-bottom:1px dotted #000000;font-weight:normal;','color: #000000;border-bottom:none;text-decoration:none;cursor:text;font-weight:normal;');
		foreach ($keys as $i=>$key) $this->parm[$key] = $this->botparms->get($key, $defaults[$i]);
		$this->parm['position'] = strtoupper($this->parm['position']);
		$this->parm['alignment'] = strtoupper($this->parm['alignment']);

		if ($this->botparms->get('show_image', 1)) $this->parm['image'] = '<img src="' . $this->live_site . '/mambots/content/glossarbot/info.gif" border="0" align="top" alt="glossary term" />';
		else $this->parm['image'] = '';
		if ($this->botparms->get('show_image', 1)) $bgimage = <<<BG_IMAGE
 background: url("$this->live_site/mambots/content/glossarbot/info.gif") top left no-repeat;
 padding-left: 16px;
BG_IMAGE;
		else $bgimage = '';

		$scripts = <<<GLBOT_SCRIPTS

<script type="text/javascript" src="$this->live_site/includes/js/overlib_mini.js"></script>
<script type="text/javascript">
	<!--//--><![CDATA[//><!--

	function glossarboton (orgTerm, description) {
		var swanchor = document.getElementById('glossarswitch');
		if (!swanchor || swanchor.className == 'gon') {
			document.getElementById('glossarbox').innerHTML = '<b><u>' + orgTerm + '</u></b><br />' + description
		}
	}

	function glossarbotoff (defaultHTML) {
		var swanchor = document.getElementById('glossarswitch');
		if (!swanchor || swanchor.className == 'gon') {
			document.getElementById('glossarbox').innerHTML=defaultHTML;
		}
	}

	function glossarbotsticky (orgTerm, description) {
	var swanchor = document.getElementById('glossarswitch');
	if (!swanchor || swanchor.className == 'gon') {
		return overlib(description,
		STICKY, CLOSECLICK, CAPTION, orgTerm, {$this->parm['position']}, {$this->parm['alignment']},
		WIDTH, {$this->parm['width']},
		FGCOLOR, '{$this->parm['fgcolor']}',
		BGCOLOR, '{$this->parm['bgcolor']}',
		TEXTCOLOR, '{$this->parm['txtcolor']}',
		CAPCOLOR, '{$this->parm['capcolor']}',
		OFFSETX, {$this->parm['offset_x']},
		OFFSETY, {$this->parm['offset_y']})
		}
	}

	function glossarbotnosticky (orgTerm, description) {
	var swanchor = document.getElementById('glossarswitch');
	if (!swanchor || swanchor.className == 'gon') {
		return overlib(description,
		CAPTION, orgTerm, {$this->parm['position']}, {$this->parm['alignment']},
		WIDTH, {$this->parm['width']},
		FGCOLOR, '{$this->parm['fgcolor']}',
		BGCOLOR, '{$this->parm['bgcolor']}',
		TEXTCOLOR, '{$this->parm['txtcolor']}',
		CAPCOLOR, '{$this->parm['capcolor']}',
		OFFSETX, {$this->parm['offset_x']},
		OFFSETY, {$this->parm['offset_y']})
		}
	}

	//--><!]]>
</script>

<style type='text/css'>
a.mosinfopop:link, a.mosinfopop:visited, a.mosinfopop:hover {
{$this->parm['css']}
$bgimage
}
a.mosinfopop-off:link, a.mosinfopop-off:visited, a.mosinfopop-off:hover {
{$this->parm['offcss']}
}

</style>

GLBOT_SCRIPTS;

		$this->addCustomHeadTag($scripts);
	}

	function makeLink ($orgTerm, $desc) {
		$trimTerm = ltrim($orgTerm);
		$orgTerm = addslashes($orgTerm);
		$encTerm = $this->trigger('fixGlossarbotJS', array($orgTerm));
		switch ($this->parm['outputmode']) {
			case 1:
				$desc= strip_tags(preg_replace('/<br>|<br\/>|<br \/>|<p>/i', ' ', $desc));
				$link = <<<LINK_MODE1
<a class="mosinfopop"
				href="javascript:void(0)"
				title="$desc">$trimTerm</a>
LINK_MODE1;
				break;

			case 2:
				//Get the defaul text of the module
				$link = <<<LINK_MODE2
<a class="mosinfopop"
				href="javascript:void(0)"
				onmouseover="glossarboton('$encTerm', '$desc')"
				onmouseout="glossarbotoff('$this->defaultHTML'">$trimTerm</a>
LINK_MODE2;
				break;

			case 3:
				$link = <<<LINK_MODE3
<a class="mosinfopop"
				href="javascript:void(0)"
				onclick="return glossarboton('$encTerm', '$desc')">$trimTerm</a>
LINK_MODE3;
				break;

			case 4:
				$link = <<<LINK_MODE4
<a class="mosinfopop"
				href="javascript:void(0)"
				onclick="return glossarbotsticky('$encTerm', '$desc')"
				onmouseover="return glossarbotnosticky('$encTerm', '$desc')"
				onmouseout="return nd()">$trimTerm</a>
LINK_MODE4;
				break;

			default:
				$link = <<<LINK_DEFAULT
<a class="mosinfopop"
				href="javascript:void(0)"
				onmouseover="return glossarbotnosticky('$encTerm', '$desc')"
				onmouseout="return nd()" >$trimTerm</a>
LINK_DEFAULT;
				break;
		}
	    return $link;
	}

	function addSwitcher ($text) {
		if ($this->switchdone) return preg_replace('/\[glossarswitch\]/i', '', $text);
		$this->switchdone = true;
		$keys = array('offlink', 'onlink', 'offalt', 'onalt', 'switchname');
		$defaults = array('mambots/content/glossarbot/btn_gloss_off.png','mambots/content/glossarbot/btn_gloss_on.png','Turn off inline glossary','Turn on inline glossary','');
		foreach ($keys as $i=>$key) $parm[$key] = $this->botparms->get($key, $defaults[$i]);
		$switcher = <<<SWITCHER_HTML

<!-- Glossary buttons -->

{$parm['switchname']}

  <a href="javascript:void(0)" id="glossarswitch" class="gon" onClick="gloss_toggleImage('tog1');gloss_toggleImage('tog2');gloss_swapClass(1,'none','mosinfopop','mosinfopop-off','a');gloss_swapClass(1,'none','gon','goff','a');return false;">
  <img src="{$parm['offlink']}" alt="{$parm['offalt']}" border="0" id="tog1"/></a><a href="javascript:void(0)" onClick="gloss_toggleImage('tog1');gloss_toggleImage('tog2');gloss_swapClass(1,'none','mosinfopop','mosinfopop-off','a');gloss_swapClass(1,'none','gon','goff','a');return false;"><img src="{$parm['onlink']}" alt="{$parm['onalt']}" border="0" id="tog2" style="display:none;"/></a>&nbsp;

<!-- Glossary buttons end -->

SWITCHER_HTML;

		return preg_replace('/\[glossarswitch\]/i', $switcher, $text);
	}

	function showGlossary (&$glossary, $content) {
		foreach ($glossary as $gkey=>$entry) {
			if ($entry['found']) $temparray[$entry['term']] = $entry['desc'];
		}
		if (count($temparray) == 0) return;
		ksort($temparray);

		define ('_headline', $this->botparms->get('headline', "Nomenclature"));
		define ('_head_term',$this->botparms->get('head_term', "Term"));
		define ('_head_explanation', $this->botparms->get('head_explanation', "Description"));

		$show_headline = $this->botparms->get('show_headline', 1);
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

	function overlibInitCall () {
		$txt = "";
		$txt .= '<script language="javascript"> '."\n";
		$txt .= " if ( !document.getElementById('overDiv') ) { "."\n";
		$txt .= " document.writeln('<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:10000;\"></div>'); "."\n";
		$txt .= " document.writeln('<scr'+'ipt language=\"Javascript\" src=\"" .$this->getCfg('live_site'). "/includes/js/overlib_mini.js\"></scr'+'ipt>'); "."\n";
		$txt .= " } "."\n";
		$txt .= "</script> "."\n";
		return $txt;
	}

}

?>
