<?php
/**
 * Lexicon file
 *
 * Based on Lexicon 2.01 by Martin Brampton modified by RolandD of www.csvimproved.com
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
 * @package Lexicon
 * @author Granholm Hosting
 * @link http://www.granholmhosting.com
 * @link http://www.csvimproved.com
 * @copyright Copyright (C) 2008 granholmhosting.com
 * @version $Id: lexicon.html.php 195 2008-04-09 11:59:02Z Roland $
 */

# Don't allow direct linking
defined('_JEXEC') or die('Restricted access');

class lexicon_row {
	var $tname = '';
	var $tmail = '';
	var $tpage = '';
	var $tloca = '';
	var $tterm = '';
	var $tlexicon = '';
}

class HTML_lexicon {

	function deleteHTML ($letter, $catid) {
		$database =& JFactory::getDBO();
		$Itemid = JRequest::getInt('Itemid');
		$id = intval(mosGetParam( $_REQUEST, 'id', 0 ));
		$submit = mosGetParam($_POST,'submit','');

		# Main Part of Subfunction
		$lexicon =& lexiconLexicon::getInstance();
		if ($lexicon->isEditor()){
			if ($submit) {
				$sql = "DELETE FROM #__lexicon WHERE id='$id'";
				$database->setquery($sql);
				$database->query();
				echo "<script type='text/javascript'> alert('"._LEXICON_DELMESSAGE."'); document.location.href='index.php?option=com_lexicon&func=display&Itemid=$Itemid&letter=$letter&catid=$catid';</script>";
			}
			else {
				$sql="SELECT * FROM #__lexicon WHERE id = '$id'";
				$database->setQuery($sql);
				$row = $database->loadObject();
				#Show the Original Entry
				echo "<table width='100%' border='0' cellspacing='1' cellpadding='4'>";
				echo "<tr><td width='30%' height='20' class='sectiontableheader'>"._LEXICON_TERM."</td>";
				echo "<td width='70%' height='20' class='sectiontableheader'>"._LEXICON_LEXICON."</td></tr>";
				echo "<tr class='sectiontableentry1'><td width='30%' valign='top'><b>$row->tname</b><br/>";
				if ($row->tloca) echo "<br /><span class='small'>"._LEXICON_FROM." $row->tloca</span>";
				if ($row->tmail) echo "<a href='mailto:$row->tmail'><img src='components/com_lexicon/images/email.gif' alt='$row->tmail' hspace='3' border='0'></a>";
				if ($row->tpage) echo "<a href='$row->tpage' target='_blank'><img src='components/com_lexicon/images/homepage.gif' alt='$row->tpage' hspace='3' border='0'></a>";
				echo "$row->tdate</td>";
				$origtext = preg_replace("/(\015\012)|(\015)|(\012)/","&nbsp;<br />", $row->tlexicon);
				echo "<td width='70%' valign='top'><span class='small'>$row->tterm<hr></span>$origtext</td></tr>";
				echo "</table>";
				echo "<form method='post' action='index.php?option=com_lexicon&Itemid=$Itemid&func=delete&id=$id'>";
				echo "<input type='hidden' name='catid' value='$catid'>";
				echo "<input type='hidden' name='letter' value='$letter'>";
				echo "<input class='button' type='submit' name='submit' value='"._LEXICON_ADELETE."'></form>";
			}
		}
		else {
			$url = sefRelToAbs("index.php?option=com_lexicon&Itemid=$Itemid");
			echo "<p><a href='$url'>Back</a></p>";
		}
	}
	
	function searchHTML () {
		
	}
	
	function commentHTML ($letter, $catid) {
		global $Itemid, $database;
		# Javascript for SmilieInsert and Form Check
		?>
		<script type="text/javascript">
		function validate(){
			if (document.lexiconForm.tcomment.value==''){ }
			else {
				document.lexiconForm.action = 'index.php';
				document.lexiconForm.submit();
			}
		}
		</script>
		<tr><td colspan="2">
		<?php
		# Main Part of Subfunction
		$lexicon =& lexiconLexicon::getInstance();
		if ($lexicon->isEditor()){
			$id=intval(JRequest::getVar('id',0 ));
			if (JRequest::getVar('opt','' )=='del'){
				$sql = "UPDATE #__lexicon SET tcomment='' WHERE id=$id";
				$database->setQuery($sql);
				$database->query();
				echo "<script> alert('"._LEXICON_COMMENTDELETED."'); document.location.href='index.php?option=com_lexicon&func=display&letter=$letter&Itemid=$Itemid&catid=$catid';</script>";
			}
			else {
				$tcomment = JRequest::getVar('tcomment','');
				if ($tcomment) {
					$tcomment = $database->getEscaped($tcomment);
					$sql = "UPDATE #__lexicon SET tcomment='$tcomment' WHERE id=$id";
					$database->setQuery($sql);
					$database->query();
					echo "<script> alert('"._LEXICON_COMMENTSAVED."'); document.location.href='index.php?option=com_lexicon&func=display&letter=$letter&Itemid=$Itemid&catid=$catid';</script>";
				}
				else {
					$tname = JRequest::getVar('tname','');
					$sql="SELECT * FROM #__lexicon WHERE id = '$id'";
					$database->setQuery($sql);
					$row = $database->loadObject();
					#Show the Original Entry
					echo "<table width='100%' border='0' cellspacing='1' cellpadding='4'>";
					echo "<tr><td width='30%' height='20' class='sectiontableheader'>"._LEXICON_NAME."</td>";
					echo "<td width='70%' height='20' class='sectiontableheader'>"._LEXICON_ENTRY."</td></tr>";
					echo "<tr class='sectiontableentry1'><td width='30%' valign='top'><b>".$row->tterm."</b>";
					if ($tname<>"") echo "<br /><span class='small'>"._LEXICON_AUTHOR.": ".$row->tname."</span>";
					echo "</td>";

					echo "<td width='70%' valign='top'><span class='small'>"._LEXICON_SIGNEDON." $row->tdate<hr></span>$row->tlexicon</td></tr>";
					echo "<tr class='sectiontableentry1'><td width='30%' valign='top'>";
					if ($row->tloca<>"") echo _LEXICON_FROM."<span class='small'>: ".$row->tloca."</span><br>";
					if ($row->tmail<>"") echo "<a href='mailto:".$row->tmail."'><img src='components/com_lexicon/images/email.gif' alt='".$row->tmail."' hspace='3' border='0'></a>";
					if ($row->tpage<>"") echo "<a href='".$row->tpage."' target='_blank'><img src='components/com_lexicon/images/homepage.gif' alt='".$row->tpage."' hspace='3' border='0'></a>";
					echo "</td></tr>";
					# Admins Comment here
					echo "<form name='lexiconForm' action='index.php' target='_top' method='post'>";
					echo "<input type='hidden' name='id' value='$id' />";
					echo "<input type='hidden' name='letter' value='$letter' />";
					echo "<input type='hidden' name='catid' value='$catid' />";
					echo "<input type='hidden' name='option' value='com_lexicon' />";
					echo "<input type='hidden' name='Itemid' value='$Itemid' />";
					echo "<input type='hidden' name='func' value='comment' />";
					echo "<tr class='sectiontableentry2'><td valign='top'><b>"._LEXICON_ADMINSCOMMENT."</b><br /><br />";
					echo "</td>";
					echo "<td valign='top'><textarea cols='40' rows='8' name='tcomment' class='inputbox'>".$row->tcomment."</textarea></td></tr>";
					echo "<tr><td><input type='button' name='send' value='"._LEXICON_SENDFORM."' class='button' onclick='submit()' /></td>";
					echo "<td align='right'><input type='reset' value='"._LEXICON_CLEARFORM."' name='reset' class='button' /></td></tr></table></form>";
				}
			}
		}
		else echo "<p><a href='index.php?option=com_lexicon&Itemid=$Itemid'>Back</a></p>";
		echo '</td></tr>';
	}
	
	function submitHTML ($letter, $catid) {
		global $mainframe;
		$database =& JFactory::getDBO();
		$Itemid = JRequest::getInt('Itemid');
		
        require(JPATH_SITE."/administrator/components/com_lexicon/config.lexicon.php");
		$id= JRequest::getInt('id',0 );
		# Check if Registered Users only
		$lexicon =& lexiconLexicon::getInstance();
		if (!$lexicon->gl_anonentry AND !$lexicon->isUser()) echo _LEXICON_ONLYREGISTERED;
		else {
			# Javascript for SmilieInsert and Form Check
			?>
			<script type="text/javascript">
			function validate(){
				if ((document.lexiconForm.tname.value=='') || (document.lexiconForm.tterm.value=='') || (document.lexiconForm.tlexicon.value=='') || (document.lexiconForm.catid.value=='0')){
					alert("<?php echo _LEXICON_VALIDATE; ?>");
					return false;
				}
				else {
					return true;
				}
			}
			</script>
			<tr><td colspan="2">
			<form name='lexiconForm' action='index.php' target='_top' method='post' onsubmit='return validate()'>
			<table align='center' width='90%' cellpadding='0' cellspacing='4' border='0'>
			<?php
			# Check if User is Admin and if he wants to edit
			if ((($lexicon->isEditor()) OR ($lexicon->isAdmin())) AND ($id)) {
				echo "<tr><td colspan='2'><input type='hidden' name='id' value='$id' /></td></tr>";
				$sql="SELECT * FROM #__lexicon WHERE id='$id'";
				$database->setQuery($sql);
				$row = $database->loadObject();
			}
			// get list of categories
			$categories[] = JHTML::_( 'select.option', '0', _SEL_CATEGORY );
			$database->setQuery( "SELECT id AS value, title AS text FROM #__categories WHERE section='com_lexicon' ORDER BY ordering" );
			$categories = array_merge( $categories, $database->loadObjectList() );
			if (count( $categories ) < 1) {
				$mainframe->redirect( "index.php?option=com_lexicon&itemid=".$Itemid, 'No categories exist. They must be created first. Please notify the administrator.' );
			}
			if (!isset($row)) $row =& new lexicon_row;
			$clist = JHTMLSelect::genericlist( $categories, 'catid', 'class="inputbox" size="1"','value', 'text', intval($catid));
			echo "<tr><td><input type='hidden' name='option' value='com_lexicon' />";
			echo "<input type='hidden' name='letter' value='$letter' />";
			echo "<input type='hidden' name='Itemid' value='$Itemid' />";
			echo "<input type='hidden' name='func' value='entry' /></td></tr>";
			echo "<tr><td width='130'>"._LEXICON_ENTERNAME."</td><td><input type='text' name='tname' style='width:245px;' class='inputbox' value='$row->tname' /></td></tr>";
			echo "<tr><td width='130'>"._LEXICON_ENTERMAIL."</td><td><input type='text' name='tmail' style='width:245px;' class='inputbox' value='$row->tmail' /></td></tr>";
			echo "<tr><td width='130'>"._LEXICON_ENTERPAGE."</td><td><input type='text' name='tpage' style='width:245px;' class='inputbox' value='$row->tpage' /></td></tr>";
			echo "<tr><td width='130'>"._LEXICON_ENTERLOCA."</td><td><input type='text' name='tloca' style='width:245px;' class='inputbox' value='$row->tloca' /></td></tr>";
			echo "<tr><td width='130'>&nbsp;</td><td>&nbsp;</td></tr>";
			echo "<tr><td width='130'>"._LEXICON_LEXICON."</td><td>$clist</td></tr>";
			echo "<tr><td width='130'>"._LEXICON_ENTERTERM."</td><td><input type='text' name='tterm' style='width:245px;' class='inputbox' value='$row->tterm' /></td></tr>";
			echo "<tr><td width='130' valign='top'>"._LEXICON_ENTERLEXICON."<br /><br />";
			echo "</td><td valign='top' width='420'>";
			
			if ($lexicon->gl_useeditor) {
			  getEditorContents( 'editor1', 'tlexicon' );
			  editorArea( 'editor1', $row->tlexicon, 'tlexicon', '400', '100', '50', '20' );
			} 
			else {		
			  echo "<textarea style='width:245px;' rows='8' cols='40' name='tlexicon' class='inputbox'>$row->tlexicon</textarea>";
			}
			echo "</td></tr>";
			echo "<tr><td width='130'><input type='submit' name='send' value='"._LEXICON_SENDFORM."' class='button' /></td>";
			echo "<td align='right'><input type='reset' value='"._LEXICON_CLEARFORM."' name='reset' class='button' /></td></tr></table></form>";
			echo '</td></tr>';
			# Close RegUserOnly Check
		}
	}
	
}

?>