<?php

class glossarySearchHTML {
	var $glossary = null;
	
	function view ($glossary, $term, $method) {
		$intro = _GLOSSARY_SEARCH_INTRO;
		$begins = _GLOSSARY_BEGINS_WITH;
		$contains = _GLOSSARY_TERM_CONTAINS;
		$exact = _GLOSSARY_EXACT_TERM;
		$search = $term ? $term : _GLOSSARY_SEARCH_SEARCH;
		$go = _GLOSSARY_GO;
		for ($i=1; $i<4; $i++) {
			$fieldname = 'check'.$i;
			$$fieldname = ($method == $i) ? 'checked="checked"' : '';	
		}
		return <<<GLOSSARY_SEARCH
		
		<div id="glossarysearch">
			<form action="index.php" method="post" id="glossarysearchform">
				<div id="glossarysearchheading">
					$intro
				</div>
				<input type="text" name="glossarysearchword" id="glossarysearchword" value="$search" size="20" />
				<div id="glossarysearchmethod">
					<input type="radio" name="glossarysearchmethod" value="1" $check1 />$begins
					<input type="radio" name="glossarysearchmethod" value="2" $check2 />$contains
					<input type="radio" name="glossarysearchmethod" value="3" $check3 />$exact
				</div>
				<div>
					<input type="submit" class="button" value="$go" />
					<input type="hidden" name="option" value="com_glossary" />
					<input type="hidden" name="task" value="list" />
					<input type="hidden" name="glossid" value="$glossary->id" />
				</div>
			</form>
		</div>
		
GLOSSARY_SEARCH;

	}
}