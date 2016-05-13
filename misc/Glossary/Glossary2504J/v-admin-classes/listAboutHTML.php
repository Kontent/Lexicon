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

class listAboutHTML extends cmsapiAdminHTML {
	var $site = '';

	function listCpanelHTML (&$controller, $limit, $clist) {
		cmsapiAdminHTML::cmsapiAdminHTML($controller, $limit, $clist);
		$interface =& cmsapiInterface::getInstance();
		$this->site = $interface->getCfg('live_site');
	}

	function view () {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$title = _GLOSSARY_ABOUT_HEADING;
		$icon = "../components/com_glossary/images/glosslogo.png";
		echo <<<SHOW_ABOUT
		
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
		<p><b>Glossary</b><br>
        The glossary component was originally based on Arthur Konze's 
		Akobook Guestbook component and on the Weblinks component. It has
		subsequently been extensively modified by Martin Brampton, and 
		version 2.5 is a total rewrite, although retaining essentially the 
		same design.  Version 2.5 will be a starting point for further development.
		</p>

        <p><b>License</b><br>
        Glossary is free software but is strictly copyright; 
        you can redistribute it and/or modify it under the terms
        of the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU General
        Public License</a> as published by the Free Software Foundation. This program is
        distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
        even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
        See the GNU General Public License for more details.</p>
		<p>Up to version 1.3 the Glossary was developed by Michelle Farren; development
		was then carried forward up to version 1.5 by Sascha Claren.
		Bernhard Zechmann created 1.9.x versions ( <a href="http://www.zechmann.com" target="_blank">www.zechmann.com </a> )
		All upcoming versions are developed and released by Martin Brampton
		( <a href="http://www.remository.com" target="_blank">www.remository.com </a> ) 
		
		</p>
	    <div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="act" value="$this->act" />
			<input type="hidden" name="option" value="com_$cname" />
		</div>
		</form>
		
SHOW_ABOUT;

	}
}