CREATE TABLE `Tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `tid` int(10) unsigned NOT NULL DEFAULT '1',
  `l` int(10) unsigned NOT NULL,
  `r` int(10) unsigned NOT NULL,
  `value` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM