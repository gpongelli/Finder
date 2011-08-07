<?php
/**
 * @version		$Id: about.php 981 2010-06-15 18:38:02Z robs $
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Filter model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @since		1.1
 */
class FinderModelAbout extends JModel
{
	function getData()
	{
		$db		= &$this->getDbo();
		$data	= new JObject;

		$db->setQuery(
			'SELECT COUNT(term_id) FROM #__finder_terms'
		);
		$data->term_count = $db->loadResult();

		$db->setQuery(
			'SELECT COUNT(link_id) FROM #__finder_links'
		);
		$data->link_count = $db->loadResult();

		$db->setQuery(
			'SELECT COUNT(id) FROM #__finder_taxonomy WHERE parent_id = 1'
		);
		$data->taxonomy_branch_count = $db->loadResult();

		$db->setQuery(
			'SELECT COUNT(id) FROM #__finder_taxonomy WHERE parent_id > 1'
		);
		$data->taxonomy_node_count = $db->loadResult();

		$db->setQuery(
			'SELECT t.title AS type_title, COUNT(a.link_id) AS link_count' .
			' FROM #__finder_links AS a' .
			' INNER JOIN #__finder_types AS t ON t.id = a.type_id' .
			' GROUP BY a.type_id'
		);
		$data->type_list = $db->loadObjectList();


		return $data;
	}
}