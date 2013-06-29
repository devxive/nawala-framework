# Dump of table xirm_dbtest
# ------------------------------------------------------------

DROP TABLE IF EXISTS `xirm_dbtest`;

CREATE TABLE `xirm_dbtest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;