<?php

class glossaryAdminConfig extends cmsapiAdminControllers {
	var $spec = array();
	
	function makeSpec () {
		$this->spec[0][0] = _GLOSSARY_USER_CONFIG;
		$this->spec[0][1] = array (
		// 'utf8' => array (_GLOSSARY_UTF8, _GLOSSARY_DESC_UTF8, 'yesno', null),
		'language' => array(_GLOSSARY_LANGUAGE, _GLOSSARY_DESC_LANGUAGE, 'input', ''),
		'show_alphabet' => array(_GLOSSARY_SHOW_ALPHABET, _GLOSSARY_DESC_SHOW_ALPHABET, 'yesno', null),
		'perpage' => array(_GLOSSARY_PER_PAGE, _GLOSSARY_DESC_PER_PAGE, 'input', 10),
		'pagespread' => array(_GLOSSARY_PAGE_SPREAD, _GLOSSARY_DESC_PAGE_SPREAD, 'input', 4),
		'allowentry' => array(_GLOSSARY_ALLOWENTRY, _GLOSSARY_DESC_ALLOWENTRY, 'yesno', null),
		'anonentry' => array(_GLOSSARY_ANONENTRY, _GLOSSARY_DESC_ANONENTRY, 'yesno', null),
		'hideauthor' => array(_GLOSSARY_HIDEAUTHOR, _GLOSSARY_DESC_HIDEAUTHOR, 'yesno', null),
		'useeditor' => array(_GLOSSARY_USEEDITOR, _GLOSSARY_DESC_USEEDITOR, 'yesno', null),
		'showcategories' => array(_GLOSSARY_SHOWCATEGORIES, _GLOSSARY_DESC_SHOWCATEGORIES, 'yesno', null),
		'showcatdescriptions' => array(_GLOSSARY_SHOWCATDESCRIPTIONS, _GLOSSARY_DESC_SHOWCATDESCRIPTIONS, 'yesno', null),
		'shownumberofentries' => array(_GLOSSARY_SHOWNUMBEROFENTRIES, _GLOSSARY_DESC_SHOWNUMBEROFENTRIES, 'yesno', null)
		);		
		$this->spec[1][0] = _GLOSSARY_ADMIN_CONFIG;
		$this->spec[1][1] = array (
		// 'utf8-admin' => array(_GLOSSARY_UTF8_ADMIN, _GLOSSARY_DESC_UTF8_ADMIN, 'yesno', null),
		'strip_accents' => array(_GLOSSARY_STRIP_ACCENTS, _GLOSSARY_DESC_STRIP_ACCENTS, 'yesno', null),
		'autopublish' => array(_GLOSSARY_AUTOPUBLISH, _GLOSSARY_DESC_AUTOPUBLISH, 'yesno', null),
		'notify' => array(_GLOSSARY_NOTIFY, _GLOSSARY_DESC_NOTIFY, 'yesno', null),
		'notify_email' => array(_GLOSSARY_NOTIFY_EMAIL, _GLOSSARY_DESC_NOTIFY_EMAIL, 'input', 40),
		'thankuser' => array(_GLOSSARY_THANK_USER, _GLOSSARY_DESC_THANKUSER, 'yesno', null)
		);		
	}

	function listTask () {
		$this->makeSpec();
		$config = cmsapiConfiguration::getInstance();
		$view = $this->admin->newHTMLClassCheck ('listConfigHTML', $this, 0, '');
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($this->spec, $config);
	}
	
	function saveTask () {
		$this->makeSpec();
		$config = cmsapiConfiguration::getInstance();
		// Set yes/no values to zero - no response from browser unless 1 selected
		foreach ($this->spec as $subspec) {
			foreach ($subspec[1] as $fieldname=>$info) {
				if ('yesno' == $info[2]) $config->$fieldname = empty($_POST[$fieldname]) ? 0 : 1;
				elseif (isset($_POST[$fieldname])) $config->$fieldname = $_POST[$fieldname];
			}
		}
		$config->save();
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->interface->redirect( "index2.php?option=com_$cname&act=cpanel", _CMSAPI_CONFIG_COMP );
	}
	
}