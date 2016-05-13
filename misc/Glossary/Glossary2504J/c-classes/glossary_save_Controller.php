<?php

class glossary_save_Controller extends glossaryUserControllers {
	
	function glossary_save ($task) {
		$interface =& cmsapiInterface::getInstance();
		$my = $interface->getUser();
		$database = $interface->getDB();
		$gconfig =& cmsapiConfiguration::getInstance();
		if (!$gconfig->anonentry AND !($gconfig->allowentry AND $my->id)) die ('Illegal attempt to edit');
		$entry = new glossaryEntry($database);
		$entry->bind($_POST);
		$entry->published = $gconfig->autopublish ? 1 : 0;
		$entry->store();
		$database->setQuery("UPDATE #__glossary SET tletter = UPPER(SUBSTRING(tterm,1,1)) WHERE tletter = ''");
		$database->query();
		$interface->redirect($interface->getCfg('live_site')."/index.php?option=com_glossary&glossid=$entry->catid");		
	}

}
