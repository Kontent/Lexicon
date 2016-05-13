				CREATE TABLE IF NOT EXISTS `#__cmsapi_configurations` (
					`component` varchar(100) NOT NULL,
					`instance` int(10) NOT NULL default 0,
					`configuration` mediumtext NOT NULL default '',
					PRIMARY KEY  (`component`)
					) ENGINE=MyISAM;
