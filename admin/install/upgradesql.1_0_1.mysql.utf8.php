-- <?php /* $Id: upgradesql.1_0_1.mysql.utf8.php 260 2008-11-07 23:41:14Z eddieajau $ */ defined('_JEXEC') or die ?>;

--
-- Change #__jxfinder_adapters.enabled to #__jxfinder_adapters.state tinyint(4) with default 1.
--
ALTER TABLE `#__jxfinder_adapters` CHANGE COLUMN `enabled` `state` tinyint(4) NOT NULL DEFAULT 1;

--
-- Update #__jxfinder_adapters.state to set all adapters active.
--
UPDATE `#__jxfinder_adapters` SET `state` = 1;

--
-- Update any Finder menu items to the correct component id.
--
UPDATE `#__menu` SET `componentid` = (SELECT `id` FROM `#__components` WHERE `option` = 'com_finder') WHERE `link` LIKE 'index.php?option=com_finder&%';
