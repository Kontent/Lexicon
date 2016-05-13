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

class listGlossariesHTML extends cmsapiAdminHTML {
	var $site = '';

	function listGlossariesHTML (&$controller, $limit, $clist) {
		cmsapiAdminHTML::cmsapiAdminHTML($controller, $limit, $clist);
		$interface =& cmsapiInterface::getInstance();
		$this->site = $interface->getCfg('live_site');
	}

	function view ($glossaries, $search) {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$interface =& cmsapiInterface::getInstance();
		$listing = '';
		foreach ($glossaries as $i=>$glossary) {
			$link = "index2.php?option=com_$cname&act=glossaries&task=edit&id=$glossary->id";
			$listing .= <<<LIST_ITEM
			
				<tr>
					<td>
						<input type="checkbox" id="cb$i" name="cfid[]" value="$glossary->id" onclick="isChecked(this.checked);" />
					</td>
					<td><a href="$link">$glossary->name</a></td>
					<td>$glossary->description</td>
					<td>$glossary->published</td>
				</tr>
			
LIST_ITEM;

		}
		$title = _GLOSSARY_LIST_GLOSSARIES;
		$icon = "../components/com_glossary/images/glosslogo.png";
		$count = count($glossaries);
		
		$gl_name = _GLOSSARY_NAME;
		$gl_description = _GLOSSARY_DESCRIPTION;
		$gl_published = _GLOSSARY_PUBLISHED;
		
		echo <<<GLOSSARY_LIST
		
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
   		<tr>
			<td width="75%" colspan="3">
			<div class="title header">
				<img src="$icon" alt="" /> 
				<span class="sectionname">&nbsp;$compname - $title</span>
			</div>
			</td>
			<td width="25%">
			</td>
    	</tr>
    	</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<tr>
					<th width="5" align="left">
						<input type="checkbox" name="toggle" value="" onclick="checkAll($count);" />
					</th>
					<th>$gl_name</th>
					<th>$gl_description</th>
					<th>$gl_published</th>
				</tr>
			</thead>
			
GLOSSARY_LIST;
			
		$this->pageNav->listFormEnd('com_'.$cname);
		echo <<<GLOSSARY_LIST
					
			<tbody>
				$listing
			</tbody>
		</table>
		</form>				
				
GLOSSARY_LIST;

	}
}