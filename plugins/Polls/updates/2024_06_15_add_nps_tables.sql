CREATE TABLE IF NOT EXISTS `nps_surveys`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` TEXT COLLATE utf8_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8_unicode_ci NULL,
    `created_at` DATETIME NOT NULL,
    `status` ENUM('active', 'inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `nps_questions`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `survey_id` INT(11) NOT NULL,
    `question_text` TEXT COLLATE utf8_unicode_ci NOT NULL,
    `sort_order` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `nps_responses`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `survey_id` INT(11) NOT NULL,
    `question_id` INT(11) NOT NULL,
    `score` TINYINT(4) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;
