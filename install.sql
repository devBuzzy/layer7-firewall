CREATE TABLE IF NOT EXISTS `ipblacklist` (
  `ip` varchar(16) NOT NULL,
  `expire` int(11) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`ip`),
  KEY `expire` (`expire`),
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `visits` (
  `ip` varchar(16) NOT NULL,
  `time` int(11) NOT NULL,
  KEY `ip` (`ip`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
