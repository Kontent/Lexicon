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

class listCpanelHTML extends cmsapiAdminHTML {
	var $site = '';
	var $path = '';

	function listCpanelHTML (&$controller, $limit, $clist) {
		cmsapiAdminHTML::cmsapiAdminHTML($controller, $limit, $clist);
		$interface =& cmsapiInterface::getInstance();
		$this->site = $interface->getCfg('live_site');
	}

	function display ($service) {
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$link = $this->site."/administrator/index2.php?option=com_{$cname}&act=".$service[1];
		echo "\n\t<div class='cmsapicpitem' style='height:68px; width:81px; padding:5px; border:1px solid #999; margin:2px; float:left'>";
		echo "\n\t\t<a href='$link'>";
		echo "\n\t\t<img style='border:0' src='$this->site/components/com_{$cname}/images/admin/{$service[2]}' height='24' width='24' alt='' />";
		echo "\n\t\t<div>{$service[0]}</div></a>";
		echo "\n\t<!-- End of cmsapicpitem-->";
		echo "\n\t</div>";
	}

	function view () {

		// Names should be replaced by symbols defined in language files
		// The following are only examples intended to be replaced by real options appropriate to the component
		$basic = array (
			array('Manage Glossaries', 'glossaries', 'categories.png'),
			array('Manage Entries', 'entries', 'addedit.png'),
			array('Configuration', 'config', 'config.png'),
			array('About', 'about', 'user.png'),
		);

		/* Shown as an example only
		$housekeeping = array (
			// array('Calculate file counts', 'counts', 'cpanel.png'),
			// array('Zero download counts', 'downloads', 'cpanel.png'),
		);
		*/

		$this->formStart('Control Panel');
		echo '</table>';

		echo "\n<div id='cmsapicpbasic' style='width:640px; padding:10px;'>";
		// Use symbol in place of text
		echo "\n\t<h3 style='float:left; width:150px'>".'Select:'."</h3>";
		foreach ($basic as $service) $this->display($service);
		echo "\n<!-- End of cmsapicpbasic -->";
		echo "\n</div>";

		/* Shown as an example only
		echo "\n<div id='cmsapicphkeep' style='clear:left; width:640px; padding:2px;'>";
		// Use symbol in place of text
		echo "\n\t<h3 style='float:left; width:150px'>".'Housekeeping'."</h3>";
		foreach ($housekeeping as $service) $this->display($service);
		echo "\n<!-- End of cmsapicphkeep -->";
		echo "\n</div>";
		*/

	}
}