<?php
/**
 * @version		$Id: link.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Link table class for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.1
 */
class FinderTableLink extends JTable
{
	var $link_id			= null;

	var $url				= null;

	var $route				= null;

	var $title				= null;

	var $description		= null;

	var $fulltxt			= null;

	var $indexdate			= null;

	var $size				= null;

	var $md5sum				= null;

	var $state				= null;

	var $access				= null;

	var $language			= null;

	var $publish_start_date	= null;

	var $publish_end_date	= null;

	var $start_date			= null;

	var $end_date			= null;

	var $type_id			= null;

	var $adapter_id			= null;

	function __construct(&$db)
	{
		parent::__construct('#__finder_links', 'link_id', $db);
	}
}