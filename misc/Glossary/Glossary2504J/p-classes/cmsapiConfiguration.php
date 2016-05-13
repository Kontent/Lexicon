<?php

class cmsapiConfiguration {

	function cmsapiConfiguration () {
		$interface = cmsapiInterface::getInstance();
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->component = $cname;
		require($interface->getCfg('absolute_path')."/components/com_{$cname}/com_{$cname}_install_settings.php");
		$this->save();
	}
	
    function &getInstance () {
        static $instance;
        if (!is_object($instance)) {
			$cname = strtolower(_THIS_COMPONENT_NAME);
			$interface = cmsapiInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery("SELECT configuration FROM #__cmsapi_configurations WHERE component = '$cname'");
			$configdata = $database->loadResult();
			if ($configdata) {
				$configdata = base64_decode($configdata);
				$instance = unserialize($configdata);
			}
			else $instance = new cmsapiConfiguration();
		}
        return $instance;
    }
	
	function save () {
		$configdata = base64_encode(serialize($this));
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$interface = cmsapiInterface::getInstance();
		$database = $interface->getDB();
		// Need to construct SQL dynamically
		$database->setQuery("INSERT INTO #__cmsapi_configurations (component, configuration) VALUES ('$cname', '$configdata') ON DUPLICATE KEY UPDATE configuration = '$configdata'");
		$database->query();
	}
	
}