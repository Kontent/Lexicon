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
 * @version $Id: admin.lexicon.php 195 2008-04-09 11:59:02Z Roland $
 */

defined('_JEXEC') or die('Restricted access');
require_once("components/com_lexicon/class.lexicon.php");
require_once( JApplicationHelper::getPath('admin_html') );
require_once( JApplicationHelper::getPath('class') );
$database =& JFactory::getDBO();

$cid = JRequest::getVar( 'cid', array(0) );

if (is_array($cid)) {
	foreach ($cid as $k=>$v) $cid[$k] = intval($v);
	$firstkey = $cid[0];
}
else $cid = $firstkey = intval($cid);

$id = JRequest::getInt('id', 0, 'post');
$lexicon =& lexiconLexicon::getInstance();

switch ($task) {

  case "categories":
    $mainframe->redirect("index2.php?option=com_categories&section=com_lexicon");
    break;

  case "publish":
    publishLexicon( $cid, 1, $option );
    break;

  case "unpublish":
    publishLexicon( $cid, 0, $option );
    break;

  case "add":
    editLexicon( $option, $database, 0 );
    break;

  case "edit":
    editLexicon( $option, $database, $firstkey );
    break;

  case "remove":
    removeLexicon( $database, $cid, $option );
    break;

  case "cancel":
	cancelLexicon( $option );
	break;

  case "multinew":  // NICOLAES MULTINEW 
    multiLexicon( $option, $database, 0 );
    break;

  case "save":
    if ($id == -1)	// NICOLAES MULTINEW 
    	multisaveLexicon( $option, $database );
    else
    saveLexicon( $option, $database );
    break;

  case "config":
    showConfig( $option, $pane );
    break;
    
  case "savesettings":
    saveConfig ($option);
    break;

  default:
    showLexicon( $option, $database );
    break;
}

function showLexicon ( $option, &$db ) {
	global $mainframe;
  $database =& JFactory::getDBO();

  $catid = $mainframe->getUserStateFromRequest( $option.'catid', 'catid', 0, 'int' );
  $limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
  $limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
  $search= $mainframe->getUserStateFromRequest( $option.'.search','search','','string');
  $search = JString::strtolower( $search );

  $where = array();
  if ($catid > 0) {
	$where[] = "catid='$catid'";
  }
  if ($search) {
    $where[] = "LOWER(tterm) LIKE '%$search%'";
  }

  // get the total number of records
  $db->setQuery( "SELECT count(*) FROM #__lexicon ".(count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "") );
  $total = $db->loadResult();
  echo $db->getErrorMsg();

  $db->setQuery( "SELECT * FROM #__lexicon"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY tterm DESC"
    . "\nLIMIT $limitstart,$limit"
  );

  $rows = $db->loadObjectList();
  if ($db->getErrorNum()) {
    echo $db->stderr();
    return false;
  }

   jimport('joomla.html.pagination');
   $pageNav = new JPagination( $total, $limitstart, $limit );

  // Source: MOS - admin.weblinks.php
  $database->setQuery( "SELECT a.*, cc.title AS category"
	. "\nFROM #__lexicon AS a"
	. "\nLEFT JOIN #__categories AS cc ON cc.id = a.catid"
	. (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
	. "\nORDER BY a.tterm"
	. "\nLIMIT $pageNav->limitstart,$pageNav->limit"
  );

  $rows = $database->loadObjectList();
  if ($database->getErrorNum()) {
	echo $database->stderr();
	return false;
  }
  
  // get list of categories
  $categories[] = JHTML::_( 'select.option', '0', 'Select Category' );
  $categories[] = JHTML::_( 'select.option', '-1', '- All Categories' );
  $database->setQuery( "SELECT id AS value, title AS text FROM #__categories"
	. "\nWHERE section='com_lexicon' ORDER BY ordering" );
  $categories = array_merge( $categories, $database->loadObjectList() );

  $clist = JHTMLSelect::genericlist( $categories, 'catid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"',	'value', 'text', $catid );

  HTML_Lexicon::showLexiconEntries( $option, $rows, $search, $pageNav, $clist );
}

function removeLexicon( &$db, $cid, $option ) {
	global $mainframe;
  if (count( $cid )) {
    $cids = implode( ',', $cid );
    $db->setQuery( "DELETE FROM #__lexicon WHERE id IN ($cids)" );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
    }
  }
  $mainframe->redirect( "index2.php?option=$option" );
}

function publishLexicon( $cid=null, $publish=1,  $option ) {
  global $mainframe;
  $database =& JFactory::getDBO();
  if (!is_array( $cid ) || count( $cid ) < 1) {
    $action = $publish ? 'publish' : 'unpublish';
    echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
    exit;
  }

  $cids = implode( ',', $cid );

  $database->setQuery( "UPDATE #__lexicon SET published='$publish' WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    exit();
  }
  
  $mainframe->redirect( "index2.php?option=$option" );
}

function editLexicon( $option, &$db, $id ) {
  global $mainframe;

  $row =& new mosLexicon( $db );

  if ($id) {
    $db->setQuery( "SELECT * FROM #__lexicon WHERE id = $id" );
    $rows = $db->loadObjectList();;
    $row = $rows[0];
  } else {
    // initialise new record
    $row->published = 0;
  }

  // make the select list for the image positions
  $yesno[] = JHTML::_( 'select.option', '0', 'No' );
  $yesno[] = JHTML::_( 'select.option', '1', 'Yes' );

  // Source: MOS - admin.weblinks.php
  // get list of categories
	$categories[] = JHTML::_( 'select.option', '0', 'Select Category' );
	$db->setQuery( "SELECT id AS value, title AS text FROM #__categories"
	  . "\nWHERE section='com_lexicon' ORDER BY ordering" );
	$categories = array_merge( $categories, $db->loadObjectList() );
	
	if (count( $categories ) < 1) {
	  $mainframe->redirect( "index2.php?option=categories&section=$option",
	    'You must add a category for this section first.' );
	}
//	echo 'c:'.intval( $row->catid );
	$clist = JHTMLSelect::genericlist( $categories, 'catid', 'class="inputbox" size="1"', 'value', 'text', intval( $row->catid ) );

  // build the html select list
  $publist = JHTMLSelect::genericlist( $yesno, 'published', 'class="inputbox" size="2"', 'value', 'text', $row->published );

  HTML_Lexicon::editLexicon( $option, $row, $publist, $clist );
}

// NICOLAES
function multiLexicon( $option, &$db ) {
  global $mosConfig_absolute_path, $mosConfig_live_site;

  $yesno[] = JHTML::_( 'select.option', '0', 'No' );
  $yesno[] = JHTML::_( 'select.option', '1', 'Yes' );

	$categories[] = JHTML::_( 'select.option', '0', 'Select Category' );
	$db->setQuery( "SELECT id AS value, name AS text FROM #__categories"
	  . "\nWHERE section='com_lexicon' ORDER BY ordering" );
	$categories = array_merge( $categories, $db->loadObjectList() );

	if (count( $categories ) < 1) {
	  $mainframe->redirect( "index2.php?option=categories&section=$option",
	    'You must add a category for this section first.' );
	}

  $default_category= $categories[1]->value; // NICOLAES First one in the list

  HTML_Lexicon::multiLexicon( $option, $yesno, $categories, $default_category);
}

function saveLexicon( $option, &$db ) {
	global $mainframe;
  $database =& JFactory::getDBO();
  $row =& new mosLexicon( $db );
  
 
  if (!$row->bind( $_POST )) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  $row->tdefinition = JRequest::getVar( 'tdefinition', '', 'post', 'string', JREQUEST_ALLOWRAW );  
  $row->_tbl_key = "id";
  
  if (!$row->store()) {
   echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  
  $database->setQuery ("UPDATE #__lexicon SET tletter = UPPER(SUBSTRING(tterm,1,1)) WHERE id = $row->id");
  $database->query();

  $mainframe->redirect( "index2.php?option=$option" );
}

// NICOLAES
function multisaveLexicon( $option, &$db ) {
  global $my,$mainframe;
  $database =& JFactory::getDBO();

 $valid_entries=0;

 for ($i=0;$i<20;$i++) {
  		$row =& new mosLexicon( $db );
  		if ( ($row->bind( $_POST['m'.$i] )) && ($row->tterm) ) {
  				 $valid_entries++;
  				 print_r($row);
  				 
			   $row->_tbl_key = "id";
			   $row->tdefinition = JRequest::getVar( 'm'.$i.'[tdefinition]', '', 'post', 'string', JREQUEST_ALLOWRAW );  
		
		  	   // NICOLAES
		  		$row->tterm = str_replace("'","&#39;",$row->tterm);
		  
			  if (!$row->store()) {
			   echo "<script> alert('".addslashes($row->getError())."'); window.history.go(-1); </script>\n";
		    	exit();
		  		}
		  
		  		$database->setQuery ("UPDATE #__lexicon SET tletter = UPPER(SUBSTRING(tterm,1,1)) WHERE id = $row->id");
		  		$database->query();
  		}
 		unset($row);
 	}
  		
 print_r($_POST);
  		
//  $mainframe->redirect( "index2.php?option=$option", "Stored $valid_entries term(s)");
}



/**
* Cancels an edit operation
* @param string The current url option
*/
function cancelLexicon( $option ) {
	global $mainframe;
	$mainframe->redirect( "index2.php?option=$option&task=view", "Edit Canceled" );
}

############################################################################

function showConfig( $option, &$pane ) {
  $database =& JFactory::getDBO();
  require(JPATH_COMPONENT."/config.lexicon.php");
?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      if (form.gl_perpage.value == ""){
        alert( "You must set entries per page greater 0!" );
      } else {
        submitform( pressbutton );
      }
    }
    </script>
      <?php
	  
	  $yesno[]    = JHTML::_( 'select.option', '0', 'No' );
	  $yesno[]    = JHTML::_( 'select.option', '1', 'Yes' );
        $multiselections[] 	= JHTML::_( 'select.option', '3', '3' );
        $multiselections[] 	= JHTML::_( 'select.option', '5', '5' );
        $multiselections[] 	= JHTML::_( 'select.option', '10', '10' );
        $multiselections[] 	= JHTML::_( 'select.option', '15', '15' );
        $multiselections[] 	= JHTML::_( 'select.option', '20', '20' );	  
	  $mybegin[]    = JHTML::_( 'select.option', 'all', 'Show all entries' );
	  $mybegin[]    = JHTML::_( 'select.option', 'nothing', 'Show nothing' );
	  $mybegin[]    = JHTML::_( 'select.option', 'first', 'Letter of first found entry' );
		$sql = "SELECT id, title FROM #__categories WHERE section='com_lexicon'";
		$database->setQuery($sql);
		$categories = $database->loadObjectList();
		
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('tabs');
      ?>
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%" class="sectionname">
	    <img src="components/com_lexicon/images/logo.png" valign="top">&nbsp;Lexicon
      </td>
    </tr>
  </table>	
  <form action="index2.php" method="post" name="adminForm" id="adminForm">
  <script language="javascript" src="js/dhtml.js"></script>
  <?php
  echo $pane->startPane( 'pane' );
  echo $pane->startPanel( 'Backend', 'panel1' );
  ?>
  <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminform">
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Show the alphabet:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_show_alphabet = JHTMLSelect::genericlist( $yesno, 'gl_show_alphabet', 'class="inputbox" size="1"', 'value', 'text', $gl_show_alphabet );
        echo $yn_gl_show_alphabet;
      ?>
      </td>
      <td align="left" valign="top">Main lexicon screen to show alphabet linking entries</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Strip accents:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_strip_accents = JHTMLSelect::genericlist( $yesno, 'gl_strip_accents', 'class="inputbox" size="1"', 'value', 'text', $gl_strip_accents );
        echo $yn_gl_strip_accents;
      ?>
      </td>
      <td align="left" valign="top">Ignore accents when building the alphabet</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Autopublish entries:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_autopublish = JHTMLSelect::genericlist( $yesno, 'gl_autopublish', 'class="inputbox" size="1"', 'value', 'text', $gl_autopublish );
        echo $yn_gl_autopublish;
      ?>
      </td>
      <td align="left" valign="top">Autopublish new entries to the lexicon.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Notify webmaster:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_notify = JHTMLSelect::genericlist( $yesno, 'gl_notify', 'class="inputbox" size="1"', 'value', 'text', $gl_notify );
        echo $yn_gl_notify;
      ?>
      </td>
      <td align="left" valign="top">Notify webmaster when new entries arrive.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Webmaster's email:</strong></td>
      <td align="left" valign="top"><input type="text" name="gl_notify_email" value="<? echo "$gl_notify_email"; ?>"></td>
      <td align="left" valign="top">Email address, where notifications are send to.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Thank user:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_thankuser = JHTMLSelect::genericlist( $yesno, 'gl_thankuser', 'class="inputbox" size="1"', 'value', 'text', $gl_thankuser );
        echo $yn_gl_thankuser;
      ?>
      </td>
      <td align="left" valign="top">Send 'Thank You' mail to the user.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Editors in multinew:</strong></td>
      <td align="left" valign="top">
      <?php   // NICOLAES
        $yn_gl_multieditors = JHTMLSelect::genericlist( $multiselections, 'gl_multieditors', 'class="inputbox" size="1"', 'value', 'text', $gl_multieditors );
        echo $yn_gl_multieditors;
      ?>
      </td>
      <td align="left" valign="top">Number of entries on the Add Multiple page</td>
    </tr>  </table>
  <?php 
	  echo $pane->endPanel();
	  echo $pane->startPanel( 'Frontend', 'panel2' );
  ?>
  
  
  <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminform">
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Default category:</strong></td>
      <td align="left" valign="top">
      <?php
        echo JHTMLSelect::genericlist( $categories, 'gl_defaultcat', 'class="inputbox" size="1"', 'id', 'title', $gl_defaultcat );
      ?>
      </td>
      <td align="left" valign="top">Category assumed if none specified.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Entries per Page:</strong></td>
      <td align="left" valign="top"><input type="text" name="gl_perpage" value="<? echo "$gl_perpage"; ?>"></td>
      <td align="left" valign="top">Number of entries shown per page.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Allow Entries:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_allowentry = JHTMLSelect::genericlist( $yesno, 'gl_allowentry', 'class="inputbox" size="1"', 'value', 'text', $gl_allowentry );
        echo $yn_gl_allowentry;
      ?>
      </td>
      <td align="left" valign="top">Allow the users to write new entries. (Editors, Publishers, Admins and Super Admins are allways allowed to add entries.)</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Anonymous Entries:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_anonentry = JHTMLSelect::genericlist( $yesno, 'gl_anonentry', 'class="inputbox" size="1"', 'value', 'text', $gl_anonentry );
        echo $yn_gl_anonentry;
      ?>
      </td>
      <td align="left" valign="top">Allow unregistered users to write entries.</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Show Author:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_hideauthor = JHTMLSelect::genericlist( $yesno, 'gl_hideauthor', 'class="inputbox" size="1"', 'value', 'text', $gl_hideauthor );
        echo $yn_gl_hideauthor;
      ?>
      </td>
      <td align="left" valign="top">Show author details like name, location etc.</td>
    </tr>
	<tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Use default Editor:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_useeditor = JHTMLSelect::genericlist( $yesno, 'gl_useeditor', 'class="inputbox" size="1"', 'value', 'text', $gl_useeditor );
        echo $yn_gl_useeditor;
      ?>
      </td>
      <td align="left" valign="top">Yes to use the default editor to add entries or No to use simple textarea</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Show Categories:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_showcategories = JHTMLSelect::genericlist( $yesno, 'gl_showcategories', 'class="inputbox" size="1"', 'value', 'text', $gl_showcategories );
        echo $yn_gl_showcategories;
      ?>
      </td>
      <td align="left" valign="top">If disabled the lexicon will only show the first published category</td>
    </tr>
	<tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Show Category Desciptions:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_showcatdescriptions =JHTMLSelect::genericlist( $yesno, 'gl_showcatdescriptions', 'class="inputbox" size="1"', 'value', 'text', $gl_showcatdescriptions );
        echo $yn_gl_showcatdescriptions;
      ?>
      </td>
      <td align="left" valign="top">If disabled the lexicon descriptions will not be display on frontend</td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Begin with:</strong></td>
      <td align="left" valign="top">
      <?php
        $sel_gl_beginwith= JHTMLSelect::genericlist($mybegin,'gl_beginwith','class="inputbox"','value','text',$gl_beginwith);
        echo $sel_gl_beginwith;
      ?>
      </td>
      <td align="left" valign="top">What shall the user see first, when open a categrory</td>
    </tr>
	<tr align="center" valign="middle">
      <td align="left" valign="top"><strong>Show number of entries:</strong></td>
      <td align="left" valign="top">
      <?php
        $yn_gl_shownumberofentries = JHTMLSelect::genericlist( $yesno, 'gl_shownumberofentries', 'class="inputbox" size="1"', 'value', 'text', $gl_shownumberofentries );
        echo $yn_gl_shownumberofentries;
      ?>
      </td>
      <td align="left" valign="top">YES shows the number of entries on the frontpage.</td>
    </tr>
  </table>
  <?php
  	  echo $pane->endPanel();
	  echo $pane->endPane();
 ?>
  </div>
  <input type="hidden" name="id" value="">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="option" value="<?php echo $option; ?>">
</form>
<?php
}

############################################################################

function saveConfig ($option) {
	global $mainframe;
	$database =& JFactory::getDBO();
  $configfile = "components/com_lexicon/config.lexicon.php";
  @chmod ($configfile, 0766);
  $permission = is_writable($configfile);
  if (!$permission) {
    $mainframe->redirect("index2.php?option=$option&task=config", "Config file not writeable!");
    break;
  }

  $database->setQuery("UPDATE #__lexicon SET tletter = UPPER(SUBSTRING(tterm,1,1))");
  $database->query();
  
  $config = "<?php\n";
  
  $gl_utf8 = JRequest::getInt('gl_utf8', 0, 'post');
  $gl_admin_utf8 = JRequest::getInt('gl_admin_utf8', 0, 'post');
  $gl_allowentry = JRequest::getInt('gl_allowentry', 0, 'post');
  $gl_autopublish = JRequest::getInt('gl_autopublish', 0, 'post');
  $gl_notify = JRequest::getInt('gl_notify', 0, 'post');
  $gl_thankuser = JRequest::getInt('gl_thankuser', 0, 'post');
  $gl_perpage = JRequest::getInt('gl_perpage', 0, 'post');
  if ($gl_perpage < 2) $gl_perpage = 20;
  $gl_sorting = JRequest::getInt('gl_sorting', 0, 'post');
  $gl_showrating = JRequest::getInt('gl_showrating', 0, 'post');
  $gl_anonentry = JRequest::getInt('gl_anonentry', 0, 'post');
  $gl_hideauthor = JRequest::getInt('gl_hideauthor', 0, 'post');
  $gl_showcategories = JRequest::getInt('gl_showcategories', 0, 'post');
  $gl_shownumberofentries = JRequest::getInt('gl_shownumberofentries', 0, 'post');
  $gl_showcatdescriptions = JRequest::getInt('gl_showcatdescriptions', 0, 'post');
  $gl_useeditor = JRequest::getInt('gl_useeditor', 0, 'post');
  $gl_notify_email = JRequest::getVar('gl_notify_email', '', 'post');
  if (!preg_match('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $gl_notify_email)) $gl_notify_email = '';
  $gl_beginwith = JRequest::getVar('gl_beginwith', '', 'post');
  if ($gl_beginwith != 'all' AND $gl_beginwith != 'first') $gl_beginwith = 'nothing';
  $gl_defaultcat = JRequest::getInt('gl_defaultcat',0, 'post');
  $gl_show_alphabet = JRequest::getInt('gl_show_alphabet', 1, 'post');
  $gl_strip_accents = JRequest::getInt('gl_strip_accents', 0, 'post');
  $gl_multieditors = JRequest::getInt('gl_multieditors', 5,'post'); // NICOLAES
  
  
  $config .= "\$gl_utf8 = \"$gl_utf8\";\n";
  $config .= "\$gl_admin_utf8 = \"$gl_admin_utf8\";\n";
  $config .= "\$gl_allowentry = \"$gl_allowentry\";\n";
  $config .= "\$gl_defaultcat = \"$gl_defaultcat\";\n";
  $config .= "\$gl_autopublish = \"$gl_autopublish\";\n";
  
  $config .= "\$gl_notify = \"$gl_notify\";\n";
  $config .= "\$gl_notify_email = \"$gl_notify_email\";\n";
  $config .= "\$gl_thankuser = \"$gl_thankuser\";\n";
  $config .= "\$gl_perpage = \"$gl_perpage\";\n";
  $config .= "\$gl_sorting = \"$gl_sorting\";\n";
  $config .= "\$gl_showrating = \"$gl_hidedef\";\n";
  $config .= "\$gl_anonentry = \"$gl_anonentry\";\n";
  $config .= "\$gl_hideauthor = \"$gl_hideauthor\";\n";
  $config .= "\$gl_showcategories = \"$gl_showcategories\";\n";
  $config .= "\$gl_beginwith = \"$gl_beginwith\";\n";
  $config .= "\$gl_shownumberofentries = \"$gl_shownumberofentries\";\n";
  $config .= "\$gl_showcatdescriptions = \"$gl_showcatdescriptions\";\n";
  $config .= "\$gl_useeditor = \"$gl_useeditor\";\n";
  $config .= "\$gl_show_alphabet = \"$gl_show_alphabet\";\n";
  $config .= "\$gl_strip_accents = \"$gl_strip_accents\";\n";
  $config .= "\$gl_multieditors = \"$gl_multieditors\";\n"; // NICOLAES
  $config .= "?>";

  if ($fp = fopen("$configfile", "w")) {
    fputs($fp, $config, strlen($config));
    fclose ($fp);
  }
  $mainframe->redirect("index2.php?option=$option&task=config", "Settings saved");
}

############################################################################
?>
