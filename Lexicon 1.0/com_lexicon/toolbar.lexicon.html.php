<?php
/**
 * Lexicon menu file
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

 * @version $Id: toolbar.lexicon.html.php 178 2008-04-05 13:48:30Z Roland $
 */
defined('_JEXEC') or die('Restricted access');


class menulexicon {

  
function NEW_MENU() {
    JToolBarHelper::save();
	JToolBarHelper::spacer();
    JToolBarHelper::cancel();
    JToolBarHelper::spacer();
  }

  
function EDIT_MENU() {
	JToolBarHelper::save();
    JToolBarHelper::spacer();
	JToolBarHelper::cancel();
    JToolBarHelper::spacer();
  }

  
function CONFIG_MENU() {
    JToolBarHelper::save( 'savesettings', 'Save' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }

  
function ABOUT_MENU() {
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }

   
function DEFAULT_MENU() {
	JToolBarHelper::publishList();
	JToolBarHelper::spacer();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
    JToolBarHelper::addNew();
	JToolBarHelper::spacer();
    JToolBarHelper::editList();
	JToolBarHelper::spacer();
    JToolBarHelper::deleteList();
    JToolBarHelper::spacer();
  }

}
?>
