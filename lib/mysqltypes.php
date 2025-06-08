<?php

$hugeInt = "bigint(20) NOT NULL DEFAULT '0'";
$genericInt = "int(11) NOT NULL DEFAULT '0'";
$smallerInt = "int(8) NOT NULL DEFAULT '0'";
$bool = "tinyint(1) NOT NULL DEFAULT '0'";
$notNull = " NOT NULL DEFAULT ''";
$text = "text"; //NOT NULL breaks in certain versions/settings.
$postText = "mediumtext";
$varcolor = "varchar(7)".$notNull; // should be 6 but 7 for backwards compat with values that prepend #
$var32 = "varchar(32)".$notNull;
$var50 = "varchar(50)".$notNull;
$var128 = "varchar(128)".$notNull;
$var256 = "varchar(256)".$notNull;
$var1024 = "varchar(1024)".$notNull;
$AI = "int(11) NOT NULL AUTO_INCREMENT";
$keyID = "primary key (`id`)";