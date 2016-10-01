DROP TABLE IF EXISTS `onn`.`tokens`;
CREATE TABLE  `onn`.`tokens` (
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `token` varchar(32) NOT NULL default '',
  `howoften` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`email`),
  UNIQUE KEY `TokenIndex` (`token`)
) TYPE=MyISAM;
