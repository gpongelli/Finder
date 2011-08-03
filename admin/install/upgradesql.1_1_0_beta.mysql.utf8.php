-- <?php /* $Id: upgradesql.1_1_0_beta.mysql.utf8.php 323 2008-12-17 06:20:04Z robs $ */ defined('_JEXEC') or die ?>;

-- --------------------------------------------------------
-- Fix the version history table.
-- --------------------------------------------------------

-- Create a copy of the #__jxfinder table.

CREATE TABLE IF NOT EXISTS `#__jxfinder_bak` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(16) NOT NULL COMMENT 'Version number',
  `installed_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Date-time installed',
  `log` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `idx_version` (`version`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Version history';

-- Copy any existing rows in #__jxfinder to #__jxfinder_bak

INSERT INTO `#__jxfinder_bak` (`version`, `installed_date`, `log`) SELECT `version`, `installed_date`, `log` FROM `#__jxfinder`;

-- Remove #__jxfinder

DROP TABLE `#__jxfinder`;

-- Create a new #__jxfinder with the new structure.

CREATE TABLE IF NOT EXISTS `#__jxfinder` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(16) NOT NULL COMMENT 'Version number',
  `installed_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Date-time installed',
  `log` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `idx_version` (`version`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Version history';

-- Copy any rows from the backup table over.

INSERT INTO #__jxfinder (`version`, `installed_date`, `log`) SELECT `version`, `installed_date`, `log` FROM `#__jxfinder_bak` ORDER BY `installed_date`;

-- Drop the backup table.

DROP TABLE `#__jxfinder_bak`;

-- --------------------------------------------------------
-- Remove the adapters table.
-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__jxfinder_adapters`;

-- --------------------------------------------------------
-- Add the taxonomy table and data.
-- --------------------------------------------------------

--
-- Table structure for table `#__jxfinder_taxonomy`
--

CREATE TABLE IF NOT EXISTS `#__jxfinder_taxonomy` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL default '1',
  `access` tinyint(4) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `state` (`state`),
  KEY `ordering` (`ordering`),
  KEY `access` (`access`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Dumping data for table `#__jxfinder_taxonomy`
--

INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(1, 0, 'ROOT', 'root', 0, 0, 0);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(2, 1, 'Publication', 'publication', 1, 0, 1);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(3, 1, 'Issue', 'issue', 1, 0, 2);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(4, 1, 'Section', 'section', 1, 0, 3);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(5, 1, 'Category', 'category', 1, 0, 4);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(6, 1, 'Author', 'author', 1, 0, 5);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(7, 1, 'Label', 'label', 1, 0, 6);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(8, 1, 'Event', 'event', 1, 0, 7);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(9, 1, 'Venue', 'venue', 1, 0, 8);
INSERT IGNORE INTO `#__jxfinder_taxonomy` (`id`, `parent_id`, `title`, `alias`, `state`, `access`, `ordering`) VALUES(10, 1, 'Type', 'type', 1, 0, 9);

-- --------------------------------------------------------
-- Add the taxonomy map table.
-- --------------------------------------------------------

--
-- Table structure for table `#__jxfinder_taxonomy_map`
--

CREATE TABLE IF NOT EXISTS `#__jxfinder_taxonomy_map` (
  `link_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`,`node_id`),
  KEY `link_id` (`link_id`),
  KEY `node_id` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- Add the filters table.
-- --------------------------------------------------------

--
-- Table structure for table `#__jxfinder_filters`
--

CREATE TABLE IF NOT EXISTS `#__jxfinder_filters` (
  `filter_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `map_count` int(11) unsigned NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`filter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Add the new types.
-- --------------------------------------------------------


INSERT IGNORE INTO `#__jxfinder_types` (`id`, `title`, `alias`, `mime`) VALUES (6, 'Web Link', 'weblink', '');
INSERT IGNORE INTO `#__jxfinder_types` (`id`, `title`, `alias`, `mime`) VALUES (7, 'PDF', 'pdf', 'pdf');
INSERT IGNORE INTO `#__jxfinder_types` (`id`, `title`, `alias`, `mime`) VALUES (8, 'Label', 'label', '');
INSERT IGNORE INTO `#__jxfinder_types` (`id`, `title`, `alias`, `mime`) VALUES (9, 'Event', 'event', '');
INSERT IGNORE INTO `#__jxfinder_types` (`id`, `title`, `alias`, `mime`) VALUES (10, 'Venue', 'venue', '');