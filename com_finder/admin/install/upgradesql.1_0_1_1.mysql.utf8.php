-- <?php /* $Id: upgradesql.1_0_1_1.mysql.utf8.php 437 2009-05-20 22:51:11Z eddieajau $ */ defined('_JEXEC') or die ?>;

-- --------------------------------------------------------
-- Drop 1.1.0 tables that might have been created during the install.
-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__jxfinder_filters`;
DROP TABLE IF EXISTS `#__jxfinder_taxonomy`;
DROP TABLE IF EXISTS `#__jxfinder_taxonomy_map`;