<?php

/**************************************************************
* This file is part of Glossary
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* More details in glossary.php
*/

class listEntriesHTML extends cmsapiAdminHTML {
	var $site = '';

	function listEntriesHTML (&$controller, $limit, $clist) {
		cmsapiAdminHTML::cmsapiAdminHTML($controller, $limit, $clist);
		$interface =& cmsapiInterface::getInstance();
		$this->site = $interface->getCfg('live_site');
	}

	function view ($entries, $glossaries, $search, $defn, $catid) {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$listing = '';
		foreach ($entries as $i=>$entry) {
			$link = <<<EDIT_LINK
index2.php?option=com_$cname&act=entries&task=edit&id=$entry->id
EDIT_LINK;
			$listing .= <<<LIST_ITEM
			
				<tr>
					<td>
						<input type="checkbox" id="cb$i" name="cfid[]" value="$entry->id" onclick="isChecked(this.checked);" />
					</td>
					<td><a href="$link">$entry->tterm</a></td>
					<td>$entry->tletter</td>
					<td width="60%"><input type="text" class="inputbox glossdefinition" value="$entry->tdefinition" /></td>
					<td>$entry->tdate</td>
					<td>$entry->published</td>
				</tr>
			
LIST_ITEM;

		}
		$title = _GLOSSARY_LIST_ENTRIES;
		$icon = "../components/com_glossary/images/glosslogo.png";
		$count = count($entries);
		
		$gl_term = _GLOSSARY_TERM;
		$gl_letter = _GLOSSARY_LETTER;
		$gl_definition = _GLOSSARY_DEFINITION;
		$gl_date = _GLOSSARY_DATE;
		$gl_published = _GLOSSARY_PUBLISHED;
		
		$selector = $this->glossarySelect($glossaries, $catid);
		$filterterm = $this->makeFilterTerm($search);
		$filterdefn = $this->makeFilterDefinition($defn);
		$refresh = _GLOSSARY_REFRESH;
		
		echo <<<GLOSSARY_HEAD
		
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
   		<tr>
			<td class="glossarytitle">
			<div class="title header">
				<img src="$icon" alt="" /> 
				<span class="sectionname">&nbsp;$compname - $title</span>
			</div>
			</td>
    	</tr>
    	<tr>
    		<td></td>
    		<td>$filterterm</td>
    		<td>$filterdefn</td>
    		<td>$selector</td>
    		<td>
    			<noscript><input type="submit" value="$refresh" /></noscript>
    		</td>
    	</tr>
    	</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<tr>
					<th width="5" align="left">
						<input type="checkbox" name="toggle" value="" onclick="checkAll($count);" />
					</th>
					<th>$gl_term</th>
					<th>$gl_letter</th>
					<th>$gl_definition</th>
					<th>$gl_date</th>
					<th>$gl_published</th>
				</tr>
			</thead>
			
GLOSSARY_HEAD;

		$this->pageNav->listFormEnd('com_'.$cname);
		if (!$listing) $listing = '<tr><td></td></tr>';
		echo <<<GLOSSARY_LIST
		
			<tbody>
				$listing
			</tbody>
		</table>
		</form>
				
GLOSSARY_LIST;

	}
	
	function glossarySelect($glossaries, $catid) {
		$options = '';
		foreach ($glossaries as $glossary) {
			$selected = ($catid == $glossary->id) ? 'selected="selected"' : '';
			$options .= <<<GLOSS_OPTION
	
			<option value="$glossary->id" $selected>$glossary->name</option>
		
GLOSS_OPTION;

		}
		return <<<SELECT_GLOSS
		
		<select name="catid" id="glossaryselect" onchange="document.adminForm.submit();">
			$options
		</select>
		
SELECT_GLOSS;
		
	}
	
	function makeFilterTerm($search) {
		$legendterm = _GLOSSARY_FILTER_TERM;
		return <<<FILTER_HTML
		
		$legendterm
		<input type="text" name="search" value="$search" size="30" onchange="document.adminForm.submit();" />
		
FILTER_HTML;

	}
	
	function makeFilterDefinition($defn) {
		$legenddefn = _GLOSSARY_FILTER_DEFINITION;
		return <<<FILTER_HTML
		
		$legenddefn
		<input type="text" name="defn" value="$defn" size="30" onchange="document.adminForm.submit();" />
		
FILTER_HTML;

	}
	
}