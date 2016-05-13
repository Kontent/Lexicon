<?php

class glossaryAdminGlossaries extends cmsapiAdminControllers {

	function listTask () {
		$database = $this->interface->getDB();
		$search = $this->interface->getParam($_REQUEST, 'search', '');
		$search = $database->getEscaped($search);
		$sql = "SELECT COUNT(*) FROM #__glossaries";
		if ($search) $sql .= " WHERE name LIKE '%$search%' OR description LIKE '%$search%' ";
		$database->setQuery($sql);
		$total = $database->loadResult();
		$sql = "SELECT * FROM #__glossaries";
		if ($search) $sql .= " WHERE description LIKE '%$search%' ";
		$sql .= " LIMIT {$this->admin->limitstart}, {$this->admin->limit}";
		$database->setQuery($sql);
		$glossaries = $database->loadObjectList();
		if (!$glossaries) $glossaries = array();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('listGlossariesHTML', $this, $total, '');
		$view->view($glossaries, $search);
	}
	
	function editTask () {
		$database = $this->interface->getDB();
		$glossary = new glossaryGlossary($database);
		if ($this->idparm) $glossary->load($this->idparm);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editGlossariesHTML', $this, 0, '');
		$view->edit($glossary);
	}
	
	function addTask () {
		$this->editTask();
	}
	
	function saveTask () {
		$database = $this->interface->getDB();
		$glossary = new glossaryGlossary($database);
		$glossary->published = 0;
		$glossary->bind($_POST);
		$glossary->store();
		$this->interface->redirect("index2.php?option=com_glossary&act=glossaries");
	}
	
	function deleteTask () {
		$database = $this->interface->getDB();
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		foreach ($cfid as $key=>$value) $cfid[$key] = intval($value);
		$idlist = implode(',', $cfid);
		if ($idlist) {
			$database->setQuery("DELETE FROM #__glossaries WHERE id IN ($idlist)");
			$database->query();
		}
		$this->interface->redirect("index2.php?option=com_glossary&act=glossaries");
	}

	function publishTask () {
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		$this->publishToggle('#__glossaries', $cfid, 1);
		$this->interface->redirect("index2.php?option=com_glossary&act=glossaries");
	}

	function unpublishTask () {
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		$this->publishToggle('#__glossaries', $cfid, 0);
		$this->interface->redirect("index2.php?option=com_glossary&act=glossaries");
	}

}