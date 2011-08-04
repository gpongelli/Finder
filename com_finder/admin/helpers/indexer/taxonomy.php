<?php
/**
 * @version		$Id: taxonomy.php 965 2010-05-06 22:22:59Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

/**
 * Stemmer base class for the Finder indexer package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderIndexerTaxonomy
{
	/**
	 * @var		array		An internal cache of taxonomy branch data.
	 */
	public static $branches = array();

	/**
	 * @var		array		An internal cache of taxonomy node data.
	 */
	public static $nodes = array();

	/**
	 * Method to add a branch to the taxonomy tree.
	 *
	 * @param	string		The title of the branch.
	 * @param	integer		The published state of the branch.
	 * @param	integer		The access state of the branch.
	 * @return	integer		The id of the branch.
	 * @throws	Exception on database error.
	 */
	public static function addBranch($title, $state = 1, $access = 0)
	{
		// Check to see if the branch is in the cache.
		if (isset(self::$branches[$title])) {
			return self::$branches[$title]->id;
		}

		// Check to see if the branch is in the table.
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT * FROM `#__jxfinder_taxonomy`' .
			' WHERE `parent_id` = 1' .
			' AND `title` = '.$db->quote($title)
		);

		// Get the result.
		$result = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Check if the database matches the input data.
		if (!empty($result) && $result->state == $state && $result->access == $access)
		{
			// The data matches, add the item to the cache.
			self::$branches[$title] = $result;

			return self::$branches[$title]->id;
		}

		// The database did not match the input. This could be because the
		// state has changed or because the branch does not exist. Let's figure
		// out which case is true and deal with it.
		if (empty($data))
		{
			// Prepare the branch object.
			$branch				= new JObject();
			$branch->parent_id	= 1;
			$branch->title		= $title;
			$branch->state		= (int)$state;
			$branch->access		= (int)$access;
		}
		else
		{
			// Prepare the branch object.
			$branch				= new JObject();
			$branch->id			= (int)$result->id;
			$branch->parent_id	= (int)$result->parent_id;
			$branch->title		= $result->title;
			$branch->state		= (int)$result->title;
			$branch->access		= (int)$result->access;
			$branch->ordering	= (int)$result->ordering;
		}

		// Store the branch.
		self::storeNode($branch);

		// Add the branch to the cache.
		self::$branches[$title] = $branch;

		return self::$branches[$title]->id;
	}

	/**
	 * Method to add a node to the taxonomy tree.
	 *
	 * @param	string		The title of the branch to store the node in.
	 * @param	string		The title of the node.
	 * @param	integer		The published state of the node.
	 * @param	integer		The access state of the node.
	 * @return	integer		The id of the node.
	 * @throws	Exception on database error.
	 */
	public static function addNode($branch, $title, $state = 1, $access = 0)
	{
		// Check to see if the node is in the cache.
		if (isset(self::$nodes[$branch][$title])) {
			return self::$nodes[$branch][$title]->id;
		}

		// Get the branch id, inserted it if it does not exist.
		$branchId = self::addBranch($branch);

		// Check to see if the node is in the table.
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT *' .
			' FROM `#__jxfinder_taxonomy`' .
			' WHERE `title` = '.$db->quote($title) .
			' AND `parent_id` = '.(int)$branchId
		);

		// Get the result.
		$result = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Check if the database matches the input data.
		if (!empty($result) && $result->state == $state && $result->access == $access)
		{
			// The data matches, add the item to the cache.
			self::$nodes[$branch][$title] = $result;

			return self::$nodes[$branch][$title]->id;
		}

		// The database did not match the input. This could be because the
		// state has changed or because the node does not exist. Let's figure
		// out which case is true and deal with it.
		if (empty($data))
		{
			// Prepare the node object.
			$node				= new JObject();
			$node->parent_id	= (int)$branchId;
			$node->title		= $title;
			$node->state		= (int)$state;
			$node->access		= (int)$access;
		}
		else
		{
			// Prepare the node object.
			$node				= new JObject();
			$node->id			= (int)$result->id;
			$node->parent_id	= (int)$result->parent_id;
			$node->title		= $result->title;
			$node->state		= (int)$result->title;
			$node->access		= (int)$result->access;
			$node->ordering		= (int)$result->ordering;
		}

		// Store the node.
		self::storeNode($node);

		// Add the node to the cache.
		self::$nodes[$branch][$title] = $node;

		return self::$nodes[$branch][$title]->id;
	}

	/**
	 * Method to add a map entry between a link and a taxonomy node.
	 *
	 * @param	integer		The link to map to.
	 * @param	integer		The node to map to.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public static function addMap($linkId, $nodeId)
	{
		// Insert the map.
		$db	= JFactory::getDBO();
		$db->setQuery(
			'REPLACE INTO `#__jxfinder_taxonomy_map` SET' .
			' `link_id` = '.(int)$linkId.',' .
			' `node_id` = '.(int)$nodeId
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return true;
	}

	/**
	 * Method to get the title of all taxonomy branches.
	 *
	 * @return	array		An array of branch titles.
	 * @throws	Exception on database error.
	 */
	public static function getBranchTitles()
	{
		$db = JFactory::getDBO();

		// Create a query to get the taxonomy branch titles.
		$query = new JDatabaseQuery();
		$query->select('title');
		$query->from('#__jxfinder_taxonomy');
		$query->where('parent_id = 1');
		$query->where('state = 1');
		$query->where('access <= '.(int)JFactory::getUser()->get('aid'));

		// Get the branch titles.
		$db->setQuery($query);
		$results = $db->loadResultArray();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return $results;
	}

	/**
	 * Method to find a taxonomy node in a branch.
	 *
	 * @param	string		The branch to search.
	 * @param	string		The title of the node.
	 * @return	mixed		Integer id on success, null on no match.
	 * @throws	Exception on database error.
	 */
	public static function getNodeByTitle($branch, $title)
	{
		$db = JFactory::getDBO();

		// Create a query to get the node.
		$query = new JDatabaseQuery();
		$query->select('t1.*');
		$query->from('#__jxfinder_taxonomy AS t1');
		$query->join('INNER', '#__jxfinder_taxonomy AS t2 ON t2.id = t1.parent_id');
		$query->where('t1.access <= '.(int)JFactory::getUser()->get('aid'));
		$query->where('t1.state = 1');
		$query->where('t1.title LIKE "'.$db->getEscaped($title).'%"');
		$query->where('t2.access <= '.(int)JFactory::getUser()->get('aid'));
		$query->where('t2.state = 1');
		$query->where('t2.title = '.$db->quote($branch));

		// Get the node.
		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return $result;
	}

	/**
	 * Method to remove map entries for a link.
	 *
	 * @param	integer		The link to remove.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public static function removeMaps($linkId)
	{
		// Delete the maps.
		$db	= JFactory::getDBO();
		$db->setQuery(
			'DELETE FROM `#__jxfinder_taxonomy_map`' .
			' WHERE `link_id` = '.(int)$linkId
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return true;
	}

	/**
	 * Method to remove orphaned taxonomy nodes and branches.
	 *
	 * @return	integer		The number of deleted rows.
	 * @throws	Exception on database error.
	 */
	public static function removeOrphanNodes()
	{
		// Delete all orphaned nodes.
		$db	= JFactory::getDBO();
		$db->setQuery(
			'DELETE t.*' .
			' FROM `#__jxfinder_taxonomy` AS t' .
			' LEFT JOIN `#__jxfinder_taxonomy_map` AS m ON m.node_id = t.id' .
			' WHERE t.parent_id > 1' .
			' AND m.link_id IS NULL'
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return $db->getAffectedRows();
	}

	/**
	 * Method to store a node to the database.
	 *
	 * This method will accept either a branch or a node.
	 *
	 * @param	object		The item to store.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	protected static function storeNode($item)
	{
		$db	= JFactory::getDBO();

		// Check if we are updating or inserting the item.
		if (empty($item->id)) {
			// Insert the item.
			$db->insertObject('#__jxfinder_taxonomy', $item, 'id');
		}
		else {
			// Update the item.
			$db->updateObject('#__jxfinder_taxonomy', $item, 'id');
		}

		// Check for a database error.
		if ($db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($db->getErrorMsg(), 500);
		}

		return true;
	}
}