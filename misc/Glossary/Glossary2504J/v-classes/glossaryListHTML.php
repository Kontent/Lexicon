<?php

class glossaryListHTML {
	
	function view ($entries, $letter, $total) {
		
		$termhead = _GLOSSARY_TERM_HEAD;
		$defhead = _GLOSSARY_DEFINITION_HEAD;

		$listhtml = '';
		$interface =& cmsapiInterface::getInstance();
		foreach ($entries as $entry) {
			$link = $interface->sefRelToAbs("index.php?option=com_glossary&id=".$entry->id);
			$listhtml .= <<<LIST_ENTRY
			
			<tr>
				<td width="25%"><a href="$link">$entry->tterm</a></td>
				<td>
					<div>$entry->tdefinition</div>
					<div>$entry->tcomment</div>
				</td>
			</tr>
			
LIST_ENTRY;

		}
		return <<<GLOSS_LIST
		
		<h2>$letter</h2>
		<table id="glossarylist">
			<thead>
				<tr>
					<th width="25%">$termhead</th>
					<th>$defhead</th>
				</tr>
			</thead>
			<tbody>
				$listhtml
			</tbody>
		</table>
		
GLOSS_LIST;

	}
	
}