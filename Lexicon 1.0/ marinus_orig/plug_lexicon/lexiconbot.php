<?php
/**
 * Lexicon plugin 2.0 beta for Mambo 4.5.1+/Joomla! 1.0.x
 * All rights reserved
 * Lexicon is Free Software
 * Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 *
 * With thanks to Ron Severdia for pushing the functionality and helping with the CSS and JS
 */

defined('_VALID_MOS') OR defined( '_JEXEC' ) OR die('Direct Access to this location is not allowed.');

$mainframe->registerEvent( 'onPrepareContent', 'plgLexiconbot' );

function plgLexiconbot (&$row, &$params, $page=0) {
	$plugin =& JPluginHelper::getPlugin('content', 'lexiconbot');
	$botParams = new JParameter( $plugin->params );
	$published = JPluginHelper::isEnabled('content','lexiconbot');
	static $botobject = null;
	if (null == $botobject) {
		$botobject = new bot_lexiconbot($botParams);
	}
	$botobject->perform('onPrepareContent', $botParams, $published, $row, $params, $page); 
}

class bot_lexiconbot {
	var $botparams = null;
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

	function bot_lexiconbot ($params=null) {
		$this->db =& JFactory::getDBO();
		$this->live_site = $this->getCfg('live_site');
		$switcherJS = <<<SCRIPT
		<script type="text/javascript" src="$this->live_site/plugins/content/lexiconbot.js"></script>
SCRIPT;
		$this->addCustomHeadTag($switcherJS);
		$this->botparams = $params;
		$option = $this->getParam($_REQUEST, 'option');
		if ('com_frontpage' == $option AND !$this->botparams->get('show_frontpage', 1)) $this->nofrontpage = true;
		$this->loadTerms();
		$this->overlibInitCall();
	}

	function getParam ($arr, $name, $default=null, $mask=null) {
		return JRequest::getVar($name, $default, $arr, $mask);
	}

	function addCustomHeadTag ($string) {
		global $mainframe;
		$mainframe->addCustomHeadTag ($string);
	}

	function getCfg ($property) {
		global $mainframe;
		// NICOLAES is this a j15 bugs?????
		// ($property='live_site') return 'http://beta.playshakespeare.com';
		//if ($property='live_site') return '/~marinus/j15';
		return $mainframe->getCfg($property);
	}

	function trigger ($event, $args) {
	if (isset($results[0])) return $results[0];
		switch ($event) {
			case 'fixLexiconbotTerm':
				return str_replace('\'', $this->quotestring, $args[0]);
				return $args[0];
			case 'fixLexiconbotJS':
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

		$show_frontpage = $this->botparams->get('show_frontpage', 1);
		$run = $this->botparams->get('run_default', 1);
		$times = $this->botparams->get('show_once_only', 1) ? 1 : -1;

		// checking if lexiconbot creates popups
		$run = $this->popupCheck($row->text, '[lexicon=enable]', 1, $run);
		$run = $this->popupCheck($row->text, '[lexicon=disable]', 0, $run);


		// If bot is not published, once any markers have been removed, that is all.
		// If bot is published, but markers indicate not to run, that is all.
		// If we are on the front page and no processing on front page is set, that is all.
 		if (!$published OR $run == 0 OR $this->nofrontpage) {
			$row->text = preg_replace('/\[lexiconswitch\]/i', '', $row->text);
			$row->text = preg_replace('/\[lexicon\]/i', '', $row->text);
			return true;
		}

		$this->checkForQuotes($row->text);
		
		$this->termsused = array();
		if (count($this->allterms)) {
			$this->linkParams();
			$newContent = '';
			$find_exact = $this->botparams->get('find_exact', 1);
			$htmlregex = '#(<a .*?</a\ *>|<script .*?</script\ *>|</?.*?>|\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>)#i';
			$bits = preg_split($htmlregex, $row->text);
			preg_match_all($htmlregex, $row->text, $matches);
			$i=0;
			$textstring = implode(' ',$bits);
			$pre = $find_exact ? '/(^|\P{L})(' : '/()(';
			$post = $find_exact ? ')($|\P{L})/i' : ')/i';
			$preg_count=0;
			foreach ($this->allterms as $this->id=>$term) {
				preg_replace_callback($pre.$term.$post, array(&$this, 'termFound'), $textstring, 1);
 /*
// php5
				preg_replace_callback($pre.$term.$post, array($this, 'termFound'), $textstring, 1, &$preg_count);
				$this->termsused[$this->id] = $preg_count;
				*/				
				$i++;
				if ($i >= 10000) break;
			}

			if (count($this->termsused)) {
				$this->loadDefinitions($find_exact);
				foreach ($this->regexes as $this->id=>$regex) {
					$bits = preg_replace_callback($regex, array(&$this, 'replaceTerm'), $bits, $times);
				}
			}
			
			// Not required any longer, so save space:
			unset($this->regexes);
			foreach ($bits as $i=>$bit) {
				$newContent .= $bit;
				if (isset($matches[0][$i])) $newContent .= $matches[0][$i];
			}
		}
		else $newContent = $row->text;

		if (stristr($newContent, '[lexiconswitch]')) $newContent = $this->addSwitcher($newContent);
		if (stristr($newContent,'[lexicon]')) $row->text = $this->showLexicon ($lexicon, $newContent);
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
		$this->db->setQuery('SELECT id,tterm FROM #__lexicon WHERE published=1');
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
		$this->db->setQuery("SELECT id, tterm, tlexicon FROM #__lexicon WHERE id IN ($deflist)");
		$defs = $this->db->loadObjectList();
		$this->defs = array();
		if ($defs) foreach ($defs as $def) {
			$definition = preg_replace("/(\015\012)|(\015)|(\012)/",'&nbsp;<br />',$def->tlexicon);
			$this->defs[$def->id]['definition'] = $this->trigger('fixLexiconbotJS', array($definition));
			$fixedupterm = $this->trigger('fixLexiconbotTerm', array(trim($def->tterm)));
			$escaped_keyword = str_replace('/', '\\/', $fixedupterm);
			$this->regexes[$def->id] = $find_exact ? '/(^|\P{L})('.$escaped_keyword.')($|\P{L})/i' : '/()('.$escaped_keyword.')/i';
		}
	}

	function linkParams () {
		if ($this->linkdone) return;
		$this->linkdone = true;
		$keys = array('fgcolor','bgcolor','txtcolor','capcolor','width','position','alignment','offset_x','offset_y','outputmode','css', 'offcss');
		$defaults = array('#CCCCFF','#333399','#000000','#FFFFFF',300,'BELOW','RIGHT',10,10,0,'cursor:help;border-bottom:1px dotted #000000;font-weight:normal;','color: #000000;border-bottom:none;text-decoration:none;cursor:text;font-weight:normal;');
		foreach ($keys as $i=>$key) $this->parm[$key] = $this->botparams->get($key, $defaults[$i]);
		$this->parm['position'] = strtoupper($this->parm['position']);
		$this->parm['alignment'] = strtoupper($this->parm['alignment']);

		if ($this->botparams->get('show_image', 1)) $this->parm['image'] = '<img src="' . $this->live_site . '/mambots/content/lexiconbot/info.gif" border="0" align="top" alt="lexicon term" />';
		else $this->parm['image'] = '';
		if ($this->botparams->get('show_image', 1)) $bgimage = <<<BG_IMAGE
 background: url("$this->live_site/plugins/content/lexiconbot/info.gif") top left no-repeat;
 padding-left: 16px;
BG_IMAGE;
		else $bgimage = '';

		$scripts = <<<GLBOT_SCRIPTS

<script type="text/javascript" src="$this->live_site/includes/js/overlib_mini.js"></script>
<script type="text/javascript">
	<!--//--><![CDATA[//><!--

	function lexiconboton (orgTerm, description) {
		var swanchor = document.getElementById('lexiconswitch');
		if (!swanchor || swanchor.className == 'gon') {
			document.getElementById('lexiconbox').innerHTML = '<b><u>' + orgTerm + '</u></b><br />' + description
		}
	}

	function lexiconbotoff (defaultHTML) {
		var swanchor = document.getElementById('lexiconswitch');
		if (!swanchor || swanchor.className == 'gon') {
			document.getElementById('lexiconbox').innerHTML=defaultHTML;
		}
	}

	function lexiconbotsticky (orgTerm, description) {
	var swanchor = document.getElementById('lexiconswitch');
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

	function lexiconbotnosticky (orgTerm, description) {
	var swanchor = document.getElementById('lexiconswitch');
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
		$encTerm = $this->trigger('fixLexiconbotJS', array($orgTerm));
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
				onmouseover="lexiconboton('$encTerm', '$desc')"
				onmouseout="lexiconbotoff('$this->defaultHTML'">$trimTerm</a>
LINK_MODE2;
				break;

			case 3:
				$link = <<<LINK_MODE3
<a class="mosinfopop"
				href="javascript:void(0)"
				onclick="return lexiconboton('$encTerm', '$desc')">$trimTerm</a>
LINK_MODE3;
				break;

			case 4:
				$link = <<<LINK_MODE4
<a class="mosinfopop"
				href="javascript:void(0)"
				onclick="return lexiconbotsticky('$encTerm', '$desc')"
				onmouseover="return lexiconbotnosticky('$encTerm', '$desc')"
				onmouseout="return nd()">$trimTerm</a>
LINK_MODE4;
				break;

			default:
				$link = <<<LINK_DEFAULT
<a class="mosinfopop"
				href="javascript:void(0)"
				onmouseover="return lexiconbotnosticky('$encTerm', '$desc')"
				onmouseout="return nd()" >$trimTerm</a>
LINK_DEFAULT;
				break;
		}
	    return $link;
	}

	function addSwitcher ($text) {
		if ($this->switchdone) return preg_replace('/\[lexiconswitch\]/i', '', $text);
		$this->switchdone = true;
		$keys = array('offlink', 'onlink', 'offalt', 'onalt', 'switchname');
		$defaults = array('plugins/content/lexiconbot/icon_off.png','plugins/content/lexiconbot/icon_on.png','Turn off inline lexicon','Turn on inline lexicon','');
		foreach ($keys as $i=>$key) $parm[$key] = $this->botparams->get($key, $defaults[$i]);
		$switcher = <<<SWITCHER_HTML

<!-- Lexicon buttons -->

{$parm['switchname']}

  <a href="javascript:void(0)" id="lexiconswitch" class="gon" onClick="gloss_toggleImage('tog1');gloss_toggleImage('tog2');gloss_swapClass(1,'none','mosinfopop','mosinfopop-off','a');gloss_swapClass(1,'none','gon','goff','a');return false;">
  <img src="{$parm['offlink']}" alt="{$parm['offalt']}" border="0" id="tog1"/></a><a href="javascript:void(0)" onClick="gloss_toggleImage('tog1');gloss_toggleImage('tog2');gloss_swapClass(1,'none','mosinfopop','mosinfopop-off','a');gloss_swapClass(1,'none','gon','goff','a');return false;"><img src="{$parm['onlink']}" alt="{$parm['onalt']}" border="0" id="tog2" style="display:none;"/></a>&nbsp;

<!-- Lexicon buttons end -->

SWITCHER_HTML;

		return preg_replace('/\[lexiconswitch\]/i', $switcher, $text);
	}

	function showLexicon (&$lexicon, $content) {
		foreach ($lexicon as $gkey=>$entry) {
			if ($entry['found']) $temparray[$entry['term']] = $entry['desc'];
		}
		if (count($temparray) == 0) return;
		ksort($temparray);

		define ('_headline', $this->botparams->get('headline', "Nomenclature"));
		define ('_head_term',$this->botparams->get('head_term', "Term"));
		define ('_head_explanation', $this->botparams->get('head_explanation', "Description"));

		$show_headline = $this->botparams->get('show_headline', 1);
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

		$lexicontable = '<table width="100%" cellpadding="1" cellspacing="0">';
		if ($show_headline) $lexicontable .= '<tr><td width="100%" style="border-bottom: 1px solid #C9C9C9;border-top: 1px solid #C9C9C9;"><b>' . _headline . '</b></td></tr>';
		$lexicontable .= '<tr><td><br><table border="' . $border . '" align="center" style="border: 1px solid #C9C9C9;" width="100%" cellpadding="' . $cellpadding . '" cellspacing="' . $cellspacing . '"><tr class="sectiontableheader"><td><b>' . _head_term . '</b></td><td><b>' . _head_explanation . '</b></td></tr>';
		foreach ($temparray as $keyword=>$definition) {
			$lexicontable .= '<tr class="sectiontableentry' . $style . '"><td width="auto">' . $keyword . '&nbsp;</td><td>' . $definition . '</td></tr>';
			$style = ($style % 2) + 1;
		}
		$lexicontable .= '</td></tr></table></td></tr><tr><td align="right"><a href="http://www.remository.com" target="_blank">&copy;2005 remository.com</a></td></tr></table>';
		return str_replace('{lexicon}', $lexicontable, $content);
	}

	function overlibInitCall () {
		$txt = "";
		$txt .= '<script language="javascript"> '."\n";
		$txt .= " if ( !document.getElementById('overDiv') ) { "."\n";
		$txt .= " document.writeln('<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:10000;\"></div>'); "."\n";
		$txt .= " document.writeln('<scr'+'ipt language=\"Javascript\" src=\"" .$this->live_site. "/includes/js/overlib_mini.js\"></scr'+'ipt>'); "."\n";
		$txt .= " } "."\n";
		$txt .= "</script> "."\n";
		return $txt;
	}

}

?>
