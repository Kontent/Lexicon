<?php

class glossaryGlossaryHTML {
	
	function view ($glossaries) {
		$interface =& cmsapiInterface::getInstance();
		$listhtml = '';
		foreach ($glossaries as $glossary) {
			$link = $interface->sefRelToAbs("index.php?option=com_glossary&letter=All&glossid=".$glossary->id);
			$listhtml .= <<<GLOSSARY_ITEM
		
			<div class="glossaryglossary">
				<a href="$link">
					$glossary->description
				</a>
			</div>
			
GLOSSARY_ITEM;

		}
		
		$heading = _GLOSSARY_GLOSSARY_LIST;
		return <<<LIST_GLOSSARIES
		
		<div id="glossaryglossarylist">
			<h3>$heading</h3>
			$listhtml
		</div>
		
LIST_GLOSSARIES;
		
	}
	
}