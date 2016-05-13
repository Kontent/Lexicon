<?php

class glossaryGlossary extends cmsapiDBTable {
	var $id = 0;
	var $name = '';
	var $description = '';
	var $published = 0;
	
	function glossaryGlossary ( &$db ) {
		$this->cmsapiDBTable( '#__glossaries', 'id', $db );
	}
}