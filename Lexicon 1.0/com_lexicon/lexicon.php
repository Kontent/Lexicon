<?php
/**
 * Lexicon file
 *
 * Based on Glossary 2.01 by Martin Brampton modified by RolandD of www.csvimproved.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @copyright Copyright (C) 2008 playshakespeare.com

 * @version $Id: lexicon.php 195 2008-04-09 11:59:02Z Roland $
 */

# Don't allow direct linking
defined('_JEXEC') or die('Restricted access');

global $mainframe;

$database =& JFactory::getDBO();

# Get the right language if it exists
$siteroot = JPATH_SITE;
$language =& JFactory::getLanguage();
$lang = $language->getBackwardLang();
if (file_exists($siteroot.'/components/com_lexicon/languages/'.$lang.'.php')) {
	require_once($siteroot.'/components/com_lexicon/languages/'.$lang.'.php');
	include_once($siteroot.'/components/com_lexicon/languages/english.php');
} else {
	require_once($siteroot.'/components/com_lexicon/languages/english.php');
}

# Variables - Don't change anything here!!!
require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mainframe->getPath( 'class' ) );
$dfversion = "V1.0";
$lexicon =& lexiconLexicon::getInstance();
if ($lexicon->gl_utf8) {
	header( 'Content-Type: text/html;charset=UTF-8' );
	$database->setQuery("SET NAMES 'utf8'");
	$database->query();
}


function LexiconABC($letter, $catid, $page=1){
    $Itemid = JRequest::getVar('Itemid');
	$lexicon =& lexiconLexicon::getInstance();
	$myabc = $lexicon->abcplus_key($catid);
	$nav = '<div align="center">';
	foreach ($lexicon->abcplus($catid) as $i=>$ltrval) {
		$key = $myabc[$i];
		if ($letter == $key) $nav .= "<b>$ltrval</b>";
		else {
			$urlkey = urlencode($key);
			$url = "index.php?option=com_lexicon&func=display&letter=$urlkey&Itemid=$Itemid&catid=$catid&page=$page";
			$nav .= "<a href='$url'>$ltrval</a>";
		}
		$nav .= ' | ';
	}
	return substr($nav,0,strlen($nav)-3)."\n</div>\n\n";  // end of HTML
}

# Functions of Lexicon
function LexiconHeader($letter, $catid=0, $term='') {
    global $mainframe;
	
	$database =& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
    ?>
    <table class='contentpaneopen' width="100%">
    <tr><td><div class='componentheading'>
    <?php
	if ($catid) {     
	    $database->setQuery("SELECT title FROM #__categories WHERE id = '$catid'");
	    $cat_title = $database->loadResult();
	    echo $cat_title;
		?>
		</div></td></tr>
		<?php
		$lexicon =& lexiconLexicon::getInstance();
		if ($lexicon->gl_showcatdescriptions) {
		  echo "<tr><td colspan='2'>";
		  $database->setQuery("SELECT description FROM #__categories WHERE id = '$catid'");
	      echo $database->loadResult();
		  echo "<br /></td></tr>";
		}
		?>		
		</table>
		<table class='contentpaneopen' width="100%">
		<tr><td colspan="2">
		<?php echo "<font size='2'><strong>"._LEXICON_SELECT_SEARCH."</strong></font>"; ?>
		</td></tr>
		<tr><td><form action='index.php' method='get'>
		<input type='hidden' name='option' value='com_lexicon' />
		<input type='hidden' name='Itemid' value='<?php echo $Itemid; ?>' />
		<input type='hidden' name='catid' value='<?php echo $catid; ?>' />
		<input type='hidden' name='func' value='display' />
		<input type="text" class="inputbox" name="search" size="10" value="<?php if ($lexicon->search) echo $lexicon->search; else echo _LEXICON_SEARCHSTRING ?>" />
		<br />
		<input type="radio" name="search_type" value="1" <?php if ($lexicon->search_type == 1) echo 'checked="checked"' ?> /><?php echo _LEXICON_SEARCH_BEGINS ?>
		<input type="radio" name="search_type" value="2" <?php if ($lexicon->search_type == 2) echo 'checked="checked"' ?> /><?php echo _LEXICON_SEARCH_CONTAINS ?>
		<input type="radio" name="search_type" value="3" <?php if ($lexicon->search_type == 3) echo 'checked="checked"' ?> /><?php echo _LEXICON_SEARCH_EXACT ?>
        <br />
		<input type="submit" class="button" value="<?php echo _LEXICON_SEARCHBUTTON ?>" />
		</form></td>
		<td align='right'>
		<?php
	    if ($lexicon->gl_showcategories) {
	    	$url = "index.php?option=com_lexicon&Itemid=$Itemid";
      		echo'<a href="'.$url.'">'._LEXICON_VIEW.'</a>';
      	}
		# BZE: only show, if entries are allowed or is_editor

	    if (($lexicon->gl_allowentry) OR ($lexicon->isEditor())) {
	    	$url_letter = urlencode($letter);
	    	$url = "index.php?option=com_lexicon&letter=$url_letter&catid=$catid&Itemid=$Itemid&func=submit";
			echo '<br /><a href="'.$url.'">'._LEXICON_SUBMIT.'</a>';
		}
	    echo '</td></tr>';
	    if ($lexicon->gl_show_alphabet) {
		    echo '<tr><td colspan="2"><br />';
		    echo LexiconABC($letter, $catid);
		    echo '<br /><br /></td></tr>';
	    }
	}
	else {
    	echo _LEXICON_TITLE;
    	$cat_title = '';
    	?>
		</div></td></tr>
		<?php
	}
	$title = _LEXICON_TITLE;
	if ($cat_title) $title = $cat_title.' - '.$title;
	if ($term) $title = $term.' - '.$title;
	if ($letter) $title = $letter.' - '.$title;
	$mainframe->setPageTitle($title);
}

function LexiconFooter($letter, $catid) {
    global $dfversion;
	
	$database =& JFactory::getDBO();
	$func = JRequest::getVar('func');
	
    echo '<tr><td colspan="2">';
    echo '<br /><br />';
	$lexicon =& lexiconLexicon::getInstance();
	if ($catid AND $func <> 'submit' AND $lexicon->gl_show_alphabet) {
	    echo LexiconABC($letter, $catid);
	    echo '<br/><br/>';
	}
	?>
    </td></tr>
	<tr><td align="center" colspan="2"><span class="small"><a href="http://www.granholmhosting.com/" target="_blank">
	Lexicon <?php echo $dfversion; ?>
	</a></span></td></tr></table>
	<?php
}

function is_email($email) {
    return preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email);
}

function textwrap($text, $width = 75) {
	if ($text) return preg_replace("/([^\n\r ?&\.\/<>\"\\-]{".$width."})/i"," \\1\n",$text);
}

function showTerms($catid, $letter) {
	$database =& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
	$lexicon =& lexiconLexicon::getInstance();
	if (!$letter AND !$lexicon->search) {
	  	switch ($lexicon->gl_beginwith){
			case 'all':
  				$letter='All';
  				echo "<font size='4'><strong>"._LEXICON_ALL."</strong></font>";
  				break;
  			case 'first':
  				$sql="SELECT UPPER(SUBSTR(tterm,1,1)) AS tletter, tterm FROM #__lexicon WHERE published = 1 AND catid=$catid ORDER BY tterm ASC LIMIT 1";
  				$database->setquery($sql);
  				$row = $database->loadObject();
  				if ($row) echo "<font size='4'><strong>$row->tletter</strong></font>";
  				break;
  			default:
  				$letter='[nothing]';
  				if ($lexicon->gl_show_alphabet) {
  					echo '<tr><td colspan="2">';
  					echo "<font size='2'><strong>"._LEXICON_SELECT."</strong></font>";
  					echo '</td></tr>';
  				}
  				return;
  				break;
  		}
  	}
	else {
		echo '<tr><td>';
  		if ($letter=='All') {
		  echo "<font size='4'><strong>"._LEXICON_ALL."</strong></font>";
		}
  		elseif ($letter AND $letter != '[nothing]') {
			echo "<font size='4'><strong>".$letter."</strong></font>";
		}
		echo '</td></tr>';
	}
	# Feststellen der Anzahl der verfügbaren Datensätze
	$count_query  = "SELECT COUNT(id) FROM #__lexicon WHERE published = 1 AND catid=$catid";
	
  	if ($lexicon->search) {
  		if ($lexicon->search_type == 1) $lexicon->search = '^'.$lexicon->search;
  		if ($lexicon->search_type == 3) $sql_letter = " AND tterm = '$lexicon->search'";
  		else $sql_letter = " AND tterm RLIKE '$lexicon->search'";
  	}
	elseif ($letter AND $letter !='All' AND $letter != '[nothing]') $sql_letter = " AND ".$lexicon->letterSQL($letter);
	else $sql_letter = '';
	$count_query .= $sql_letter;
	$database->setquery($count_query);
	$count = $database->loadResult();
	# Manage page breaks
	if (!isset($lexicon->gl_perpage) OR $lexicon->gl_perpage < 2) $lexicon->gl_perpage = 20;
	$total_pages = floor($count / $lexicon->gl_perpage);
	$last_page   = $count % $lexicon->gl_perpage;
	if ($last_page>0) $total_pages++;
	# Finding actual page now
	$page = JRequest::getInt('page', 1);
	$page = max(min($page, $total_pages),1);
	# BZE show number of entries
	if ($lexicon->gl_shownumberofentries) {
		echo '<tr><td colspan="2">';
	   	printf (_LEXICON_BEFOREENTRIES.' %d '._LEXICON_AFTERENTRIES, $count);
		echo '</td></tr>';
	}
	
	if ($letter != '[nothing]') {
		if ($lexicon->search) $start = 0;
		else {	
			echo '<tr><td>';
			echo _LEXICON_PAGES.' ';
			# Determine the page
			$previous_page = $page - 1;
			$url_letter = urlencode($letter);
			if ($previous_page > 0) echo '<a href="index.php?option=com_lexicon&func=display&letter=$url_letter&page=$previous_page&catid=$catid&Itemid=$Itemid"><b>«</b></a>';
	  		
	  		#Ausgeben der einzelnen Seiten
	  		for ($i=1; $i <= $total_pages; $i++) {
				if ($i==$page) echo "$i ";
				else {
					$url = "index.php?option=com_lexicon&func=display&letter=$url_letter&page=$i&catid=$catid&Itemid=$Itemid";
					echo '<a href="'.$url."\">$i</a> ";
        		}
	  		}
	  		# Ausgeben der Seite vorwärts Funktion
	  		$seitevor = $page + 1;
	  		if ($seitevor<=$total_pages) {
				$url = "index.php?option=com_lexicon&func=display&letter=$url_letter&Itemid=$Itemid&catid=$catid&page=$seitevor";
				echo '<a href="'.$url.'"><b>»</b></a> ';
  	  		}
	  		# Limit und Seite Vor- & Rueckfunktionen
	  		$start = ( $page - 1 ) * $lexicon->gl_perpage;
			echo '</td></tr>';
		}
	  	// Database Query
		?>
	  	<tr><td width="30%" height="20" class="sectiontableheader"> <?php echo _LEXICON_TERM; ?> </td>
	  	<td width="70%" height="20" class="sectiontableheader"> <?php echo _LEXICON_LEXICON; ?> </td></tr>
		<?php
	}
	$line = 1;
	$sqlsuffix = "WHERE published=1 AND catid=$catid".$sql_letter;
  	// get the total number of records
  	if (!isset($start)) $start = 0;
	$query1=" SELECT * FROM #__lexicon $sqlsuffix ORDER BY tterm LIMIT $start,$lexicon->gl_perpage ";
	$database->setQuery($query1);
	$items = $database->loadObjectList();
	if ($items) {
		foreach ($items as $row1) {
			$linecolour = ($line % 2) + 1;
			showOneTerm($row1->id, $linecolour, $row1);
			$line++;
		}
	}
}

function showOneTerm ($id, $linecolour, &$row1) {
	$database =& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
	if ($row1 == null) {
		$justone = true;
		$sql = "SELECT * FROM #__lexicon WHERE id=$id";
		$database->setQuery($sql);
		$database->loadObject($row1);
		echo "<table width='100%' border='0' cellspacing='1' cellpadding='4'>";
	}
	else $justone = false;
	if ($row1) {	
		$lexicon =& lexiconLexicon::getInstance();
		$url = 'index.php?option=com_lexicon&func=view&Itemid='.$Itemid.'&catid='.$row1->catid.'&term='.urlencode($row1->tterm);
		$showterm = '<a href="'.$url.'">'.$row1->tterm.'</a>';
		echo "<tr class='sectiontableentry".$linecolour."'><td width='30%' valign='top'><a name='$row1->id'></a><b>$showterm</b>";
		if($lexicon->gl_hideauthor){
			$row1->tname = textwrap($row1->tname,20);
			if ($row1->tname<>""){
				if ($row1->tpage<>"") {
					# Check if URL is in right format
					if (substr($row1->tpage,0,7)!="http://") $row1->tpage="http://$row1->tpage";
					echo "<br /><a href='$row1->tpage' target='_blank'><span class='small'>"._LEXICON_AUTHOR.": $row1->tname</span></a>";
				}
				else echo '<br /><span class="small">'._LEXICON_AUTHOR.": $row1->tname</span>";
			}
		}
		echo '</td><td valign="top">';
		echo textwrap($row1->tdefinition,80);
		if ($row1->tcomment) {
			$origcomment = $row1->tcomment;
			echo "<hr /><span class='small'><b>"._LEXICON_ADMINSCOMMENT.":</b> $origcomment</span>";
		}
		echo "</td></tr>";
		echo "<tr class='sectiontableentry".$linecolour."'><td width='30%' valign='top'>";
		echo "&nbsp;</td>";
		echo "<td width='70%' valign='top'>";
		if ($lexicon->isEditor()) {
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>";
			$letter = substr($row1->tterm,0,1);
			$url_letter = urlencode($letter);
			echo "<td align='left'><b>"._LEXICON_ADMIN.":</b> ";
			echo "<a href='index.php?option=com_lexicon&Itemid=$Itemid&func=comment&letter=$url_letter&id=$row1->id&catid=$row1->catid'>"._LEXICON_ACOMMENT."</a> - ";
			if ($row1->tcomment) echo "<a href='index.php?option=com_lexicon&letter=$url_letter&Itemid=$Itemid&func=comment&opt=del&id=$row1->id&catid=$row1->catid'>"._LEXICON_ACOMMENTDEL."</a> - ";
			echo "<a href='index.php?option=com_lexicon&Itemid=$Itemid&func=submit&letter=$url_letter&id=$row1->id&catid=$row1->catid'>"._LEXICON_AEDIT."</a> - ";
			echo "<a href='index.php?option=com_lexicon&Itemid=$Itemid&func=delete&letter=$url_letter&id=$row1->id&catid=$row1->catid'>"._LEXICON_ADELETE."</a></td>";
			echo '</tr></table>';
		}
		echo '</td></tr>';
	}
	if ($justone) echo '</table>';
}

function lexiconUserSide () {
	global $mainframe;
	
	$database =& JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid');
	$lexicon =& lexiconLexicon::getInstance();

	$catid = JRequest::getInt('catid', $lexicon->gl_defaultcat );
	$func = JRequest::getVar('func','' );
	$letter = JRequest::getVar('letter', '');
	$letter = urldecode($letter);
	if ($letter) $letter = $database->getEscaped($letter);
	$lexicon->search = $database->getEscaped(JRequest::getVar('search', ''));
	$lexicon->search_type = JRequest::getInt('search_type', 1);
  	$lexicon->search_type = max(min(3, $lexicon->search_type),1);

    switch ($func) {
		case 'popup':
			// get the record
	        LexiconHeader($letter);
			$term = $database->getEscaped(JRequest::getVar('term', ''));
			$database->setQuery( "SELECT tterm, tdefinition FROM #__lexicon WHERE id='$term' AND published=1");
			$rows = $database->loadObjectList();
	        $row = $rows[0];
	        ?>
			<h1 class='contentHeading'>
			<? echo $row->tterm; ?>
			</h1>
	        <p>
			<? echo $row->tdefinition; ?>
	        <p><a href='javascript:history.go(-1);'><span class="small">Back</span></a>
	        <?
			break;
		#########################################################################################
		case 'search':
			LexiconHeader($letter, $catid);
			HTML_lexicon::searchHTML();
			break;
		#########################################################################################
		case 'delete':
			LexiconHeader($letter, $catid);
			HTML_lexicon::deleteHTML($letter, $catid);
			break;
		#########################################################################################
		case 'comment':
			LexiconHeader($letter, $catid);
			HTML_lexicon::commentHTML($letter, $catid);
			break;
		#########################################################################################
		case 'entry':
			$id = JRequest::getInt('id', 0);
			if (!$lexicon->gl_anonentry AND !$lexicon->isUser()) die (_LEXICON_ONLYREGISTERED);
			# Check if entry was edited by editor
			$tname = $database->getEscaped(JRequest::getVar('tname', ''));
			$tmail = $database->getEscaped(JRequest::getVar('tmail', ''));
			$tloca = $database->getEscaped(JRequest::getVar('tloca', ''));
			$tpage = $database->getEscaped(JRequest::getVar('tpage', ''));
			$tterm = JRequest::getVar('tterm', '');
			$tterm = $database->getEscaped($tterm);
			$tdefinition = $database->getEscaped(JRequest::getVar('tdefinition', ''));
			if ($tname == '' OR $tterm == '' OR $tdefinition == '' OR $catid ==0){
				LexiconHeader($letter, $catid);
				echo '<p><strong>'._LEXICON_VALIDATE.'</strong></p>';
				break;
			}
			if (($lexicon->isEditor()) AND ($id)) {
				$query1 = "UPDATE #__lexicon SET catid='$catid', tname='$tname', tmail='$tmail', tloca='$tloca', tpage='$tpage', tterm='$tterm', tletter=UPPER(SUBSTRING('$tterm',1,1)), tdefinition='$tdefinition' WHERE id=$id";
				$database->setQuery( $query1 );
				$database->query();
			}
			else {
				$tip   = getenv('REMOTE_ADDR');
				$tdate = date("y/m/d g:i:s");
				$query2 = "INSERT INTO #__lexicon SET catid='$catid',tname='$tname',tdate='$tdate',tmail='$tmail', tloca='$tloca', tpage='$tpage', tterm='$tterm', tletter=UPPER(SUBSTRING('$tterm',1,1)), tdefinition='$tdefinition'";
				if ($lexicon->gl_autopublish) $query2 .= ",published='1'";
				$database->setQuery( $query2 );
				$database->query();
				if ($lexicon->gl_notify AND is_email($lexicon->gl_notify_email) ) {
					$tmailtext = _LEXICON_ADMINMAIL."\r\n\r\nName: ".$tname."\r\nText: ".$tterm."\r\n\r\n"._LEXICON_MAILFOOTER;
					mail($lexicon->gl_notify_email,_LEXICON_ADMINMAILHEADER,$tmailtext,"From: ".$lexicon->gl_notify_email);
				}
				if ($lexicon->gl_thankuser AND is_email($tmail) ) {
					$tmailtext = _LEXICON_USERMAIL."\r\n\r\nName: ".$tname."\r\nText: ".$tterm."\r\n\r\n"._LEXICON_MAILFOOTER;
					mail($tmail,_LEXICON_USERMAILHEADER,$tmailtext,"From: ".$lexicon->gl_notify_email);
				}
			}
			$url_letter = urlencode($letter);
			echo "<script> alert('"._LEXICON_SAVED."'); document.location.href='index.php?option=com_lexicon&func=display&letter=$url_letter&Itemid=$Itemid&catid=$catid';</script>";
			break;
		#########################################################################################
		case 'submit':
			LexiconHeader($letter);
			if (($lexicon->gl_allowentry) OR ($lexicon->isEditor())) {
			  HTML_lexicon::submitHTML($letter, $catid);
			break;
			}
		#########################################################################################
		case 'display':
			LexiconHeader($letter, $catid);
			showTerms($catid, $letter);
			break;
		#########################################################################################
		case 'view':
			$term = mosGetParam($_REQUEST, 'term', '');
			$term = urldecode($term);
			if ($term) {
				$term = $database->getEscaped($term);
				$sql = "SELECT * FROM #__lexicon WHERE tterm='$term' AND catid=$catid";
				$database->setQuery($sql);
				$database->loadObject($row);
				LexiconHeader($letter, $catid, $term);
				if ($row) showOneTerm($row->id, 0, $row);
			}
			break;
		#########################################################################################
		default:
			$my = &JFactory::getUser(); 
			$func = '';
			if ($lexicon->gl_showcategories) {
				LexiconHeader($letter);
				$database->setQuery( "SELECT * FROM #__categories WHERE section='com_lexicon' AND published=1 ORDER BY ordering" );
				$categories = $database->loadObjectList();
				if ($categories) {
					foreach ($categories as $row2) {
						if ($row2->access<=$my->gid) {
							echo "<img src='images/M_images/arrow.png' /> <a href='index.php?option=com_lexicon&func=display&Itemid=$Itemid&catid=$row2->id'>$row2->title</a><br />";
							# BZE, description for categories
							if ($lexicon->gl_showcatdescriptions) {
							  echo "$row2->description<br />";
							}
							if($row2->count > 0) echo "<i>(".$row2->numitems." "._CHECKED_IN_ITEMS.")</i>";
						}
						else {
							echo $row2->name.' - '._E_REGISTERED;
						} ?>
						<br />
						<?php
					}
				}
			}
			else{
				$catid = $lexicon->gl_defaultcat;
				if (!$catid) {
					$sql = "SELECT id FROM #__categories WHERE section='com_lexicon' ORDER BY id LIMIT 1";
					$database->setQuery($sql);
					$catid = $database->loadResult();
				}
				LexiconHeader($letter, $catid);
				showTerms($catid, $letter);
			}
			break;
    }
    LexiconFooter($letter, $catid);
}

lexiconUserSide();

?>