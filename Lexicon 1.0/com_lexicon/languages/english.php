<?php
/**
 * Language file
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

 * @version $Id: english.php 195 2008-04-09 11:59:02Z Roland $
 */


// Header language lexicons
if (!defined("_LEXICON_TITLE")) DEFINE("_LEXICON_TITLE","Lexicon");
if (!defined("_LEXICON_SELECT")) DEFINE("_LEXICON_SELECT","You can view entries by selecting an initial letter.");
if (!defined("_LEXICON_SELECT_SEARCH")) DEFINE("_LEXICON_SELECT_SEARCH","You can always search for entries (regexp permitted).");
if (!defined("_LEXICON_BEFOREENTRIES")) DEFINE("_LEXICON_BEFOREENTRIES","There are");
if (!defined("_LEXICON_AFTERENTRIES")) DEFINE("_LEXICON_AFTERENTRIES","entries in the lexicon.");
if (!defined("_LEXICON_PAGES")) DEFINE("_LEXICON_PAGES","Pages:");
if (!defined("_LEXICON_ONLYREGISTERED")) DEFINE("_LEXICON_ONLYREGISTERED","Only registered users can submit terms.<br />Please log in or register.");

// Default category description
if (!defined("_LEXICON_DEFAULT_CATEGORY")) DEFINE("_LEXICON_DEFAULT_CATEGORY","Terms that are on use on this site.");

// LEXICON language lexicons
if (!defined("_LEXICON_TERM")) DEFINE("_LEXICON_TERM","Term");
if (!defined("_LEXICON_TERMS")) DEFINE("_LEXICON_TERMS","Terms");
if (!defined("_LEXICON_AUTHOR")) DEFINE("_LEXICON_AUTHOR","Author");
if (!defined("_LEXICON_LEXICON")) DEFINE("_LEXICON_LEXICON","Lexicon");
if (!defined("_LEXICON_FROM")) DEFINE("_LEXICON_FROM","From");
if (!defined("_LEXICON_LEXICON")) DEFINE("_LEXICON_LEXICON","Lexicon");
if (!defined("_LEXICON_SEARCH")) DEFINE("_LEXICON_SEARCH","Search");
if (!defined("_LEXICON_ALL")) DEFINE("_LEXICON_ALL","All");
if (!defined("_LEXICON_OTHER")) DEFINE("_LEXICON_OTHER","Other");
if (!defined("_LEXICON_NEW")) DEFINE("_LEXICON_NEW","New");
if (!defined("_LEXICON_SIGNEDON")) DEFINE("_LEXICON_SIGNEDON","Created");
if (!defined("_LEXICON_ADMINSCOMMENT")) DEFINE("_LEXICON_ADMINSCOMMENT","Comments");
if (!defined("_LEXICON_VIEW")) DEFINE("_LEXICON_VIEW","View lexicon");
if (!defined("_LEXICON_ENTRY")) DEFINE("_LEXICON_ENTRY","Lexicon");
if (!defined("_LEXICON_NAME")) DEFINE("_LEXICON_NAME","Term");
if (!defined("_LEXICON_SUBMIT")) DEFINE("_LEXICON_SUBMIT","Submit Term");
if (!defined("_E_REGISTERED")) DEFINE("_E_REGISTERED","Registered Users Only");

// Form language lexicons
if (!defined("_SEL_CATEGORY")) DEFINE("_SEL_CATEGORY", "Select category");
if (!defined("_LEXICON_VALIDATE")) DEFINE("_LEXICON_VALIDATE","Please enter at least your name, term, lexicon and a category.");
if (!defined("_LEXICON_ENTERNAME")) DEFINE("_LEXICON_ENTERNAME","Your Name:");
if (!defined("_LEXICON_ENTERMAIL")) DEFINE("_LEXICON_ENTERMAIL","Your E-Mail:");
if (!defined("_LEXICON_ENTERPAGE")) DEFINE("_LEXICON_ENTERPAGE","Your Homepage:");
if (!defined("_LEXICON_ENTERCOMMENT")) DEFINE("_LEXICON_ENTERCOMMENT","Your Comment:");
if (!defined("_LEXICON_ENTERLOCA")) DEFINE("_LEXICON_ENTERLOCA","Your Location:");
if (!defined("_LEXICON_ENTERTERM")) DEFINE("_LEXICON_ENTERTERM","The Term:");
if (!defined("_LEXICON_ENTERLEXICON")) DEFINE("_LEXICON_ENTERLEXICON","Definition:");
if (!defined("_LEXICON_SUBMITFORM")) DEFINE("_LEXICON_SUBMITFORM","Submit");
if (!defined("_LEXICON_SENDFORM")) DEFINE("_LEXICON_SENDFORM","Submit");
if (!defined("_LEXICON_CLEARFORM")) DEFINE("_LEXICON_CLEARFORM","Clear");

// Save language lexicons
if (!defined("_LEXICON_SAVED")) DEFINE("_LEXICON_SAVED","Entry saved to lexicon.");

// Search options
if (!defined("_LEXICON_SEARCH_BEGINS")) DEFINE ("_LEXICON_SEARCH_BEGINS", "Begins with");
if (!defined("_LEXICON_SEARCH_CONTAINS")) DEFINE ("_LEXICON_SEARCH_CONTAINS", "Contains");
if (!defined("_LEXICON_SEARCH_EXACT")) DEFINE ("_LEXICON_SEARCH_EXACT", "Exactly matches");

// Admin language lexicons
if (!defined("_LEXICON_DELENTRY")) DEFINE("_LEXICON_DELENTRY","Delete term");
if (!defined("_LEXICON_DELMESSAGE")) DEFINE("_LEXICON_DELMESSAGE","The term has been removed.");
if (!defined("_LEXICON_DEFVALIDATE")) DEFINE("_LEXICON_DEFVALIDATE","Please enter the lexicon.");
if (!defined("_LEXICON_COMMENTSAVED")) DEFINE("_LEXICON_COMMENTSAVED","Your comment has been saved.");
if (!defined("_LEXICON_COMMENTDELETED")) DEFINE("_LEXICON_COMMENTDELETED","Your comment has been deleted.");
if (!defined("_LEXICON_ADMIN")) DEFINE("_LEXICON_ADMIN","Admin");
if (!defined("_LEXICON_AEDIT")) DEFINE("_LEXICON_AEDIT","Edit");
if (!defined("_LEXICON_ACOMMENT")) DEFINE("_LEXICON_ACOMMENT","Comment");
if (!defined("_LEXICON_ACOMMENTDEL")) DEFINE("_LEXICON_ACOMMENTDEL","Delete Comment");
if (!defined("_LEXICON_ADELETE")) DEFINE("_LEXICON_ADELETE","Delete");

// Email language lexicons
if (!defined("_LEXICON_ADMINMAILHEADER")) DEFINE("_LEXICON_ADMINMAILHEADER","New lexicon entry");
if (!defined("_LEXICON_ADMINMAIL")) DEFINE("_LEXICON_ADMINMAIL","Hello Admin,\n\nA user has submitted a new term to your lexicon at ".JURI::base().":\n");
if (!defined("_LEXICON_USERMAILHEADER")) DEFINE("_LEXICON_USERMAILHEADER","Thank's for your submission to the lexicon.");
if (!defined("_LEXICON_USERMAIL")) DEFINE("_LEXICON_USERMAIL","Hello User,\n\nMany thanks for your submission to the lexicon at ".JURI::base().":\n It will be reviewed before being added to the site.\n");
if (!defined("_LEXICON_MAILFOOTER")) DEFINE("_LEXICON_MAILFOOTER","Please do not respond to this message as it is automatically generated and is for information purposes only.\n");

// update 1.9.0
if (!defined("_LEXICON_SEARCHSTRING")) DEFINE("_LEXICON_SEARCHSTRING","Search...");
if (!defined("_LEXICON_SEARCHBUTTON")) DEFINE("_LEXICON_SEARCHBUTTON","GO");

?>