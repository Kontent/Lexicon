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
 * @version $Id: install.lexicon.php 184 2008-04-06 08:29:27Z Roland $
 */


defined('_JEXEC') or die('Restricted access');

function changeIcon($name,$option,$icon) {
  $database =& JFactory::getDBO();
  $database->setQuery( "UPDATE #__components"
  ."\n SET admin_menu_img = '".$icon."'"
  ."\n WHERE name = '".$name."' AND `option` = '".$option."'");
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    exit();
  }	
}

function com_install() {
	$database =& JFactory::getDBO();
	$config =& JFactory::getConfig();
	$language =& JFactory::getLanguage();
	
	$siteroot = JPATH_SITE;
	$lang = $language->getBackwardLang();
	$mosConfig_live_site = JURI::root();	
	if (file_exists($siteroot.'/components/com_lexicon/languages/'.$lang.'.php')) {
		require_once($siteroot.'/components/com_lexicon/languages/'.$lang.'.php');
		include_once($siteroot.'/components/com_lexicon/languages/english.php');
	} else {
		require_once($siteroot.'/components/com_lexicon/languages/english.php');
	}
	
	$sql = "ALTER TABLE #__lexicon  ADD `tletter` char(1) NOT NULL default '' AFTER `id`;";
	$database->setQuery($sql);
	$database->query();
	$sql = "ALTER TABLE #__lexicon CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
	$database->setQuery($sql);
	// $database->query();
	$sql = "UPDATE #__lexicon SET tletter = UPPER(SUBSTRING(tterm,1,1))";
	$database->setQuery($sql);
	$database->query();

	$sql = "SELECT COUNT(id) FROM #__categories WHERE section='com_lexicon' AND published=1";
	$database->setQuery($sql);
	if ($database->loadResult() == 0) {
		$cat_title = _LEXICON_DEFAULT_CATEGORY;
    	$sql = "INSERT INTO `#__categories`
          (`title`, `name`, `section`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `editor`, `ordering`, `access`, `count`) VALUES
          ('Lexicon', 'Lexicon', 'com_lexicon', 'left', '$cat_title', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0)";
		$database->setQuery($sql);
		$database->query();
	}
	
	echo "Correcting images... ";
	changeIcon("View Terms","com_lexicon","content.png");
	changeIcon("Categories","com_lexicon","category.png");
	changeIcon("Edit Config","com_lexicon","config.png");
	changeIcon("Lexicon","com_lexicon","../administrator/components/com_lexicon/images/icon.png");
	echo "<b>OK</b><br />";

# Show installation result to user
?>
<center>
<table width="100%" border="0">
  <tr>
    <td><img src="components/com_lexicon/images/logo.png"></td>&nbsp;
    <td>
      <strong>Lexicon Component</strong><br/>
      <font class="small">&copy; Copyright 2008 by PlayShakespeare.com</font><br/>
      <br/>
      This component is released under the terms and conditions of the GNU General Public License</a>.
    </td>
  </tr>
  <tr>
    <td>
      <code>Installation: <font color="green">successful</font></code>
	  <br />
    </td>
  </tr>
  <tr><td>
  	Forked from the Glossary and Definition projects  
	<br />
	This component is released under the terms and conditions of the GNU General Public License.
  </td></tr>
</table>
</center>
<?
}
?>