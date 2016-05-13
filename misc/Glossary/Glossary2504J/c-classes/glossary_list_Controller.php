<?php

class glossary_list_Controller extends glossaryUserControllers {
	
	function glossary_list ($task) {
		$interface =& cmsapiInterface::getInstance();
		$database = $interface->getDB();
		$my = $interface->getUser();
		$gconfig = cmsapiConfiguration::getInstance();

		$id = $interface->getParam($_REQUEST, 'id', 0);
		if ($id) {
			$database->setQuery("SELECT * FROM #__glossary WHERE id = $id");
			$entries = $database->loadObjectList();
			if ($entries) {
				$glossid = $entries[0]->catid;
				$total = 1;
			}
		}
		if (empty($glossid)) $glossid = $interface->getParam($_REQUEST, 'glossid', 0);
		if (!$glossid) {
			$sql = "SELECT id FROM #__glossaries ORDER BY isdefault DESC, id";
			$database->setQuery($sql);
			$glossid = $database->loadResult();
		}
		$glossary = new glossaryGlossary($database);
		if ($glossid) $glossary->load($glossid);

		$glosshtml = '';
		if ($gconfig->showcategories) {
			$database->setQuery("SELECT * FROM #__glossaries WHERE id != $glossid");
			$glossaries = $database->loadObjectList();
			if ($glossaries) {
				require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossaryGlossaryHTML.php');
				$glister = new glossaryGlossaryHTML();
				$glosshtml = $glister->view($glossaries);
			}
		}

		require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossarySearchHTML.php');
		$searchword = $interface->getParam($_REQUEST, 'glossarysearchword');
		$searchword = $database->getEscaped($searchword);
		$searchmethod = $interface->getParam($_REQUEST, 'glossarysearchmethod', 1);
		$searching = new glossarySearchHTML;
		$searchhtml = $searching->view($glossary, $searchword, $searchmethod);
		
		$letter = urldecode($interface->getParam($_REQUEST, 'letter', 'All'));
		$letter = $database->getEscaped($letter);

		if ($gconfig->show_alphabet) {
			require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossaryAlphabetHTML.php');
			if ($glossary->id) $sql = "SELECT DISTINCT tletter FROM #__glossary WHERE catid=$glossary->id ORDER BY tletter";
			else $sql = "SELECT DISTINCT tletter FROM #__glossary ORDER BY tletter";
			$database->setQuery($sql);
			$letters = $database->loadResultArray();
			$alphabet = new glossaryAlphabetHTML();
			$alphabethtml = $alphabet->view($glossary, $letters, $letter);
		}
		else $alphabethtml = '';
		
		if ($glossid) $where[] = "catid = $glossid";
		if ($letter AND 'All' != $letter) $where[] = "tletter = '$letter'";
		if ($searchword) $where[] = $this->searchSQL($searchword, $searchmethod);

		$sql = 'SELECT COUNT(*) FROM #__glossary';
		if ($gconfig->shownumberofentries) {
			$database->setQuery($sql." WHERE catid = $glossid");
			$grandtotal = $database->loadResult();
		}
		else $grandtotal = 0;
		
		if (empty($total)) {
			if (isset($where)) $sql .= ' WHERE '.implode(' AND ', $where);
			$database->setQuery($sql);
			$total = $database->loadResult();
		}
		
		if ($total) {
			if (empty($entries)) {
				$sql = "SELECT * FROM #__glossary";
				if (isset($where)) $sql .= ' WHERE '.implode(' AND ', $where);
				$page = $interface->getParam($_REQUEST, 'page', 1);
				require_once ($interface->getCfg('absolute_path').'/components/com_glossary/p-classes/cmsapiUserPage.php');
				$querystring = "&task=list&glossid=$glossid&letter=".urlencode($letter);
				if ($searchword) $querystring .= "&glossarysearchword=$searchword&glossarysearchmethod=$searchmethod";
				$pagecontrol =& new cmsapiUserPage ( $total, $my, $gconfig->perpage, $page, $querystring );
				$sql .= ' ORDER BY tterm';
				$sql .= " LIMIT $pagecontrol->startItem, $pagecontrol->itemsperpage";
				$database->setQuery($sql);
				$entries = $database->loadObjectList();
			}
			else $pagecontrol = null;
			require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossaryListHTML.php');
			$listing = new glossaryListHTML;
			$listhtml = $listing->view($entries, $letter, $grandtotal);
		}
		else {
			$listhtml = _GLOSSARY_IS_EMPTY;
			$pagecontrol = null;
		}
		
		$allowentry = ($gconfig->anonentry OR ($gconfig->allowentry AND $my->id));
		require_once ($interface->getCfg('absolute_path').'/components/com_glossary/v-classes/glossaryUserHTML.php');
		$lister = new glossaryUserHTML;
		$lister->view($glossary, $grandtotal, $allowentry, $glosshtml, $searchhtml, $alphabethtml, $listhtml, $pagecontrol);
	}
	
	function searchSQL ($word, $method) {
  		if (3 == $method) $word = "^$word$";
  		if (1 == $method) $word = '^'.$word;
  		return "tterm RLIKE '$word'";
	}
	
}