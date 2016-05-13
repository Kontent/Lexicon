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
 * @version $Id: class.lexicon.php 178 2008-04-05 13:48:30Z Roland $
 */

defined('_JEXEC') or die('Restricted access');

class mosLexicon extends JTable {
  var $id=null;
  var $tname=null;
  var $tmail=null;
  var $tpage=null;
  var $tloca=null;
  var $tterm=null;
  var $tlexicon=null;
  var $tdate=null;
  var $tcomment=null;
  var $tedit=null;
  var $teditdate=null;
  var $published=null;
  var $catid=null;
  var $checked_out=null;

  function mosLexicon( &$db ) {
   parent::__construct( '#__lexicon', 'id', $db );
  }

}
?>