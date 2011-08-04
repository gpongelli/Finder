-- <?php /* $Id: upgradesql.2_0_0_RC2.mysql.utf8.php 981 2010-06-15 18:38:02Z robs $ */ defined('_JEXEC') or die ?>;

ALTER TABLE `#__jxfinder_links` CHANGE `object` `object` MEDIUMBLOB NOT NULL;