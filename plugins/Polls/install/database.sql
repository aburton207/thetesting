CREATE TABLE IF NOT EXISTS `polls`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` TEXT COLLATE utf8_unicode_ci NOT NULL,
    `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `expire_at` date NULL,
    `status` ENUM('active', 'inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
    `total_views` INT(11) NOT NULL DEFAULT '0',
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 1; --#

CREATE TABLE IF NOT EXISTS `poll_answers`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `title` TEXT NOT NULL,
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8; --#

CREATE TABLE IF NOT EXISTS `poll_votes`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `poll_answer_id` INT(11) NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 1; --#

CREATE TABLE IF NOT EXISTS `poll_settings`(
    `setting_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    `setting_value` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
    `type` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'app',
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    UNIQUE KEY `setting_name`(`setting_name`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci; --#

INSERT INTO `notification_settings` (`event`, `category`, `enable_email`, `enable_web`, `notify_to_team`, `notify_to_team_members`, `notify_to_terms`, `sort`, `deleted`) VALUES 
('poll_created', 'poll', 0, 1, '', '', '', 99, 1); --#

ALTER TABLE `notifications` ADD `plugin_poll_id` INT(11) NOT NULL AFTER `deleted`; --#

INSERT INTO `email_templates`(`id`, `template_name`, `email_subject`, `default_message`, `custom_message`, `template_type`, `language`, `deleted`) VALUES 
(NULL,'poll_created','New poll created','<div style="background-color: #eeeeef; padding: 50px 0; "> <div style="max-width:640px; margin:0 auto; "> <div style="color: #fff; text-align: center; background-color:#33333e; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;"><h1>Poll #{POLL_ID}</h1></div><div style="padding: 20px; background-color: rgb(255, 255, 255);"><p style=""><span style="line-height: 18.5714px; font-weight: bold;">Title: {POLL_TITLE}</span><span style="line-height: 18.5714px;"><br></span></p><p style=""><span style="line-height: 18.5714px;">{POLL_DESCRIPTION}</span><br></p> <p style="">This poll will be expired {POLL_EXPIRE_AT}. Please check it out.</p><p style=""><br></p> <p style=""><span style="color: rgb(85, 85, 85); font-size: 14px; line-height: 20px;"><a style="background-color: #00b393; padding: 10px 15px; color: #ffffff;" href="{POLL_URL}" target="_blank">Show Poll</a></span></p> <p style=""><br></p>   </div>  </div> </div>',"","default","",0); --#