				CREATE TABLE IF NOT EXISTS `#__glossaries` (
					`id` int(10) NOT NULL auto_increment,
					`name` varchar(120) NOT NULL default '',
					`description` varchar(255) NOT NULL default '',
					`published` tinyint(1) UNSIGNED NOT NULL default 0,
					`isdefault` tinyint(1) UNSIGNED NOT NULL default 0,
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
