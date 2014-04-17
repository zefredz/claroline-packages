CREATE TABLE IF NOT EXISTS `__CL_MAIN__clkrng_keyring` (
    `service` varchar(255) NOT NULL,
    `host` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    PRIMARY KEY (`service`, `host`),
    KEY (`service`),
    KEY (`host`)
) ENGINE=MyISAM;
