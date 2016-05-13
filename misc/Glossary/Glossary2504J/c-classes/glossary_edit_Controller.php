<?php

class glossary_edit_Controller extends glossaryUserControllers {
	
	function glossary_edit ($task) {
		$interface =& cmsapiInterface::getInstance();
		$my = $interface->getUser();
		$database = $interface->getDB();
		$gconfig =& cmsapiConfiguration::getInstance();
		if (!$gconfig->anonentry AND !($gconfig->allowentry AND $my->id)) die ('Illegal attempt to edit');
		$glossid = $interface->getParam($_REQUEST, 'glossid', 0);

		$id = $interface->getParam($_REQUEST, 'id', 0);
		$gitem = new glossaryEntry($database);
		if ($id) $gitem->load($id);
		else $gitem->catid = $glossid;
		
		if ($my->id) {
			$database->setQuery("SELECT name, email FROM #__users WHERE id = $my->id");
			$userentry = $database->loadObjectList();
			if ($userentry) {
				$my->email = $userentry[0]->email;
				$my->name = $userentry[0]->name;
			}
		}
		
		require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossaryEditHTML.php');
		$editor = new glossaryEditHTML;
		$editor->edit($gitem, $my, $gconfig);
	}
	
}