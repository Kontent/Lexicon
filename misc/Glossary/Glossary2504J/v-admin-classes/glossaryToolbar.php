<?php

class glossaryToolbar {
	var $act;
	var $task;
	// Create an instance, get the controlling parameters from the request
	function glossaryToolbar () {
		$interface =& cmsapiInterface::getInstance();
		if ($this->act = $interface->getParam ($_REQUEST, 'act', 'about'));
		else $this->act = 'about';
		if ($this->task = $interface->getParam($_REQUEST, 'task', 'list'));
		else $this->task = 'list';
		$this->makeBar();
	}
	
	// create a toolbar based on the parameters found in $_REQUEST
	function makeBar () {
		$this->start();
		$act = $this->act;
		if (method_exists($this,$act)) $this->$act();
		$this->finish();
	}
	// Any initial actions
	function start () {
		cmsapiMenuBar::startTable();
		if ('cpanel' != $this->act) cmsapiMenuBar::custom ('cpanel', 'back.png', 'back_f2.png', _CMSAPI_CPANEL_RETURN, false );
	}

	function finish () {
		cmsapiMenuBar::spacer();
		cmsapiMenuBar::endTable();
	}

	// The cancel option is always formed the same way
	function cancel_Button () {
		cmsapiMenuBar::custom( 'list', 'cancel.png', 'cancel_f2.png', _CMSAPI_CANCEL, false );
	}

	function entries () {
		if ($this->task == 'add') $this->addMenu('Entries');
		elseif ($this->task == 'edit' OR $this->task == 'apply') $this->editMenu('Entry');
		else $this->listMenu('');
	}

	function glossaries () {
		if ($this->task == 'add') $this->addMenu('Glossary');
		elseif ($this->task == 'edit' OR $this->task == 'apply') $this->editMenu('Glossary');
		else $this->listMenu('');
	}

	function config () {
		cmsapiMenuBar::save( 'save', 'Save Config' );
	}

	// The menu for adding something is always the same apart from the text
	function addMenu ($entity) {
		cmsapiMenuBar::save( 'save', 'Save '.$entity );
		$this->cancel_Button();
	}
	// The menu for editing something is always the same apart from the text
	function editMenu ($entity) {
		cmsapiMenuBar::save( 'save', 'Save '.$entity );
		if ('Container' == $entity) cmsapiMenuBar::save( 'apply', 'Apply '.$entity );
		$this->cancel_Button();
	}
	// The menu for a list of items is always the same apart from the text
	function listMenu ($entity) {
		cmsapiMenuBar::publishList( 'publish', 'Publish '.$entity );
		cmsapiMenuBar::unpublishList( 'unpublish', 'UnPublish '.$entity );
		cmsapiMenuBar::addNew( 'add', 'Add '.$entity );
		cmsapiMenuBar::editList( 'edit', 'Edit '.$entity );
		cmsapiMenuBar::deleteList( '', 'delete', 'Delete '.$entity );
	}
	
}