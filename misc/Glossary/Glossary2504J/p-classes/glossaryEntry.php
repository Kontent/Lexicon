<?php

class glossaryEntry extends cmsapiDBTable {
	var $id = 0;
	var $tletter = '';
	var $tterm = '';
	var $tdefinition = '';
	var $tname = '';
	var $tloca = '';
	var $tmail = '';
	var $tpage = '';
	var $tdate = '';
	var $tcomment = '';
	var $tedit = '';
	var $teditdate = '';
	var $published = 0;
	var $catid = 0;
	
	function glossaryEntry ( &$db ) {
		$this->cmsapiDBTable( '#__glossary', 'id', $db );
	}
}