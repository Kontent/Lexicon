<?php
/**
 * Lexicon toolbar file
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
 * @version $Id: toolbar.lexicon.php 178 2008-04-05 13:48:30Z Roland $
 */
defined('_JEXEC') or die('Restricted access');

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task) {
  case "add":
  case "multinew":
    menulexicon::NEW_MENU();
    break;

  case "edit":
    menulexicon::EDIT_MENU();
    break;

  case "config":
    menulexicon::CONFIG_MENU();
    break;

  case "about":
    menulexicon::ABOUT_MENU();
    break;

  default:
	menulexicon::DEFAULT_MENU();
    break;
}
?>
