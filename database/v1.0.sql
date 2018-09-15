CREATE TABLE `cb_photos_albumns` (
    `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `language` CHAR(5) NULL,
    `uri` VARCHAR(200) NULL,
    `date_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` TINYINT(1) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COMMENT='version:1.0';

CREATE TABLE `cb_photos_associations` ( 
    `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `file_id` INT(10) UNSIGNED NOT NULL,
    `album_id` INT(5) UNSIGNED NOT NULL,
    `position` SMALLINT(4) UNSIGNED NOT NULL,
    `views` SMALLINT(5) UNSIGNED NOT NULL,
    `status` TINYINT(1) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COMMENT='version:1.0';
