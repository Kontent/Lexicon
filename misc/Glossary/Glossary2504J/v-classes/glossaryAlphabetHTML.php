<?php

class glossaryAlphabetHTML {
	
	function view ($glossary, $letters, $currentletter) {
		$interface =& cmsapiInterface::getInstance();
		$baselink = 'index.php?option=com_glossary&task=list';
		if ($glossary->id) $baselink .= '&glossid='.$glossary->id;
		$baselink .= '&letter=';
		$letterhtml[] = 'All' == $currentletter ? 'All' : "<a href=\"{$baselink}All\">All</a>";
		foreach ($letters as $letter) {
			if ($currentletter == $letter) $letterhtml[] = $letter;
			else {
				$lettercode = urlencode($letter);
				$link = $interface->sefRelToAbs($baselink.$lettercode);
				$letterhtml [] = <<<LETTER_LINK

			<a href="$link">$letter</a>
			
LETTER_LINK;

			}
		}
		$alphabet = implode (' | ', $letterhtml);
		return <<<LIST_ALPHA
		
		<div class="glossaryalphabet">
			$alphabet
		</div>
		
LIST_ALPHA;
		
	}
	
}