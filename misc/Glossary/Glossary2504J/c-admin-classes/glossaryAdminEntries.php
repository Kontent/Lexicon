<?php

class glossaryAdminEntries extends cmsapiAdminControllers {

	function listTask () {
		$database = $this->interface->getDB();
		$catid = $this->interface->getParam($_REQUEST, 'catid', 0);
		if ($catid) $where[] = "catid = $catid";
		$search = $this->interface->getParam($_REQUEST, 'search');
		if ($search) {
			$search = $database->getEscaped($search);
			$where[] = "tterm RLIKE '$search'";
		}
		$defn = $this->interface->getParam($_REQUEST, 'defn');
		if ($defn) {
			$defn = $database->getEscaped($defn);
			$where[] = "MATCH (tdefinition) AGAINST ('$defn' IN BOOLEAN MODE)";
		}
		if (isset($where)) $condition = " WHERE ".implode(' AND ', $where);
		else $condition = '';
		$database->setQuery("SELECT COUNT(*) FROM #__glossary".$condition);
		$total = $database->loadResult();
		$database->setQuery("SELECT * FROM #__glossary $condition LIMIT {$this->admin->limitstart}, {$this->admin->limit}");
		$entries = $database->loadObjectList();
		if (!$entries) $entries = array();
		$database->setQuery("SELECT id, name FROM #__glossaries");
		$glossaries = $database->loadObjectList();
		if (!$glossaries) $glossaries = array();
		$selectall = new stdClass();
		$selectall->id = 0;
		$selectall->name = _GLOSSARY_ALL_GLOSSARIES;
		array_unshift($glossaries, $selectall);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('listEntriesHTML', $this, $total, '');
		$view->view($entries, $glossaries, $search, $defn, $catid);
	}
	
	function editTask () {
		$database = $this->interface->getDB();
		$entry = new glossaryEntry($database);
		if ($this->idparm) $entry->load($this->idparm);
		$database->setQuery("SELECT * FROM #__glossaries");
		$glossaries = $database->loadObjectList();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editEntriesHTML', $this, 0, '');
		$view->edit($entry, $glossaries);
	}
	
	function addTask () {
		$this->editTask();
	}
	
	function saveTask () {
		$database = $this->interface->getDB();
		$entry = new glossaryEntry($database);
		$entry->published = 0;
		$entry->bind($_POST);
		$entry->tterm = trim($entry->tterm);
		$entry->tletter = trim($entry->tletter);
		$entry->tdefinition = trim($entry->tdefinition);
		if ($entry->tterm AND $entry->tdefinition) {
			$entry->store();
			if (!$entry->tletter) {
				$database->setQuery("UPDATE #__glossary SET tletter = UPPER(SUBSTRING(tterm,1,1)) WHERE tterm = '$entry->tterm'");
				$database->query();
			}
		}
		$this->interface->redirect("index2.php?option=com_glossary&act=entries");
	}
	
	function deleteTask () {
		$database = $this->interface->getDB();
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		foreach ($cfid as $key=>$value) $cfid[$key] = intval($value);
		$idlist = implode(',', $cfid);
		if ($idlist) {
			$database->setQuery("DELETE FROM #__glossary WHERE id IN ($idlist)");
			$database->query();
		}
		$this->interface->redirect("index2.php?option=com_glossary&act=entries");
	}
	
	function publishTask () {
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		$this->publishToggle('#__glossary', $cfid, 1);
		$this->interface->redirect("index2.php?option=com_glossary&act=entries");
	}

	function unpublishTask () {
		$cfid = $this->interface->getParam($_REQUEST, 'cfid', array());
		$this->publishToggle('#__glossary', $cfid, 0);
		$this->interface->redirect("index2.php?option=com_glossary&act=entries");
	}

}