<?php

class glossaryUserHTML {
	
	function view ($glossary, $grandtotal, $allowentry, $glosshtml, $searchhtml, $alphabethtml, $listhtml, $pagecontrol) {
		$gconfig = cmsapiConfiguration::getInstance();
		$navigation = $pagecontrol ? $pagecontrol->showNavigation($gconfig->pagespread) : '';
		$itemcount = $grandtotal ? sprintf(_GLOSSARY_ITEM_COUNT, $grandtotal) : '';
		$interface =& cmsapiInterface::getInstance();
		if ($allowentry) {
			$addlink = $interface->sefRelToAbs("index.php?option=com_glossary&task=edit&id=0&glossid=".$glossary->id);
			$addtext = _GLOSSARY_ADD_ENTRY;
			$addhtml = <<<ADD_ENTRY_HTML

			<a href="$addlink">$addtext</a>
		
ADD_ENTRY_HTML;

		}
		else $addhtml = '';

		echo <<<GLOSSARY_DISPLAY
		
		<h2>$glossary->description</h2>
		$itemcount
		$addhtml
		$searchhtml
		$alphabethtml
		$navigation
		$listhtml
		$navigation
		$glosshtml
		<div id="glossarycredit" class="small">
			Glossary 2.5 is technology by <a href="http://guru-php.com">Guru PHP</a>
		</div>

GLOSSARY_DISPLAY;

	}
	
}