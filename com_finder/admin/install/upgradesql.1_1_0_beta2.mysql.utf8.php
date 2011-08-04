-- <?php /* $Id: upgradesql.1_1_0_beta2.mysql.utf8.php 312 2008-12-03 23:24:06Z robs $ */ defined('_JEXEC') or die ?>;

-- --------------------------------------------------------
-- Fix the links table.
-- --------------------------------------------------------

ALTER IGNORE TABLE `#__jxfinder_links`
 ADD `language` VARCHAR(8) NOT NULL AFTER `access`;

ALTER IGNORE TABLE `#__jxfinder_links`
 ADD `publish_start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `language`;

ALTER IGNORE TABLE `#__jxfinder_links`
 ADD `publish_end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `publish_start_date`;

ALTER IGNORE TABLE `#__jxfinder_links`
 ADD `start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `publish_end_date`;

ALTER IGNORE TABLE `#__jxfinder_links`
 ADD `end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `start_date`;