-- <?php /* $Id: upgradesql.1_1_0_beta3.mysql.utf8.php 981 2010-06-15 18:38:02Z robs $ */ defined('_JEXEC') or die ?>;

-- --------------------------------------------------------
-- Fix the taxonomy table.
-- --------------------------------------------------------

ALTER IGNORE TABLE `#__jxfinder_taxonomy` ADD `inactive` TINYINT NOT NULL DEFAULT '0' AFTER `access`;

ALTER IGNORE TABLE `#__jxfinder_taxonomy` ADD `version` INT(10) UNSIGNED NOT NULL DEFAULT '0';

-- --------------------------------------------------------
-- Fix the filters table.
-- --------------------------------------------------------

ALTER IGNORE TABLE `#__jxfinder_filters` ADD COLUMN `params` MEDIUMTEXT COMMENT 'Ini based settings' AFTER `data`;

-- --------------------------------------------------------
-- Fix the links table.
-- --------------------------------------------------------

ALTER IGNORE TABLE `#__jxfinder_terms` CHANGE `term` `term` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER IGNORE TABLE `#__jxfinder_terms` ADD `stemmed` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `term`;