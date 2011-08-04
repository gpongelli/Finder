<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Index model class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
// @TODO: Determine which JModel variant is best to extend and move forward
class FinderModelIndex extends JModel
{
	/**
	 * @var		boolean		Flag to indicate model state initialization.
	 */
	protected $__state_set;

	/**
	 * @var		array		Container for index information.
	 */
	protected $_index;

	/**
	 * @var		object		Container for JPagination object.
	 */
	protected $_pagination;

	/**
	 * @var		integer		The number of visible items in the list.
	 */
	protected $_list_count;

	/**
	 * @var		integer		The total number of items.
	 */
	protected $_list_total;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	public function getState($property = null)
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$application	= &JFactory::getApplication('administrator');
			$context		= 'com_finder.index.';

			$this->setState('adapter.id', JRequest::getInt('adapter_id'));

			// Load the filter state.
			$this->setState('filter.search', $application->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
			$this->setState('filter.state', $application->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));
			$this->setState('filter.type', $application->getUserStateFromRequest($context.'filter.type', 'filter_type', 0, 'int'));

			// Load the list state.
			$this->setState('list.start', $application->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
			$this->setState('list.limit', $application->getUserStateFromRequest($context.'list.limit', 'limit', $application->getCfg('list_limit', 25), 'int'));
			$this->setState('list.ordering', $application->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'l.link_id', 'cmd'));
			$this->setState('list.direction', $application->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

			// Load the check parameters.
			if ($this->state->get('filter.state') === '*') {
				$this->setState('check.state', false);
			} else {
				$this->setState('check.state', true);
			}

			// Load the parameters.
			$this->setState('params', JComponentHelper::getParams('com_finder'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of indexed URLs.
	 *
	 * @return	mixed	False on failure, array on success.
	 * @since	1.0
	 */
	public function getData()
	{
		$false = false;

		if (!empty($this->_index)) {
			return $this->_index;
		}

		// Load the index data.
		$return	= $this->_getList($this->_getIndexQuery(), $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$this->_index = $return;

		return $return;
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @access	public
	 * @return	object	A JPagination object.
	 * @since	1.0
	 */
	public function getPagination()
	{
		if (!empty($this->_pagination)) {
			return $this->_pagination;
		}

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getCount(), $this->getState('list.start'), $this->getState('list.limit'));

		return $this->_pagination;
	}

	/**
	 * Method to get the number of relevant links.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	public function getCount()
	{
		$false = false;

		if (!empty($this->_list_count)) {
			return $this->_list_count;
		}

		$sql = clone($this->_getIndexQuery());
		$sql->clear('select')->clear('order');
		$sql->select('COUNT(l.link_id)');

		// Load the indexed data count.
		$this->_db->setQuery($sql);
		$return = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$this->_list_count = (int)$return;

		return $this->_list_count;
	}

	/**
	 * Method to get the total number of items.
	 *
	 * @access	public
	 * @return	mixed	False on failure, integer on success.
	 * @since	1.0
	 */
	public function getTotal()
	{
		if (!empty($this->_list_total)) {
			return $this->_list_total;
		}

		$sql = clone($this->_getIndexQuery());
		$sql->clear('select')->clear('order')->clear('where');
		$sql->select('COUNT(l.link_id)');

		// Load the label total data.
		$this->_db->setQuery($sql);
		$return = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_list_total = (int)$return;

		return $this->_list_total;
	}

	/**
	 * Method to delete links from the index.
	 *
	 * @access	public
	 * @param	array	$link_ids	An array of link ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	public function delete($link_ids)
	{
		// Include the indexer.
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'indexer'.DS.'indexer.php';

		// Iterate the links to delete each one.
		foreach ($link_ids as $link_id)
		{
			// Delete the link.
			$return = FinderIndexer::remove($link_id);

			// Check for an error.
			if (JError::isError($return)) {
				$this->setError($return->getMessage());
				return false;
			}
		}

		// Optimize the index.
		FinderIndexer::optimize();

		return true;
	}

	/**
	 * Method to purge the index, deleting all links.
	 *
	 * @return	boolean		True on success, false on failure.
	 */
	public function purge()
	{
		// Truncate the links table.
		$this->_db->setQuery('TRUNCATE TABLE #__jxfinder_links');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Truncate the links terms tables.
		for ($i = 0; $i <= 15; $i++)
		{
			// Get the mapping table suffix.
			$suffix = dechex($i);

			$this->_db->setQuery('TRUNCATE TABLE #__jxfinder_links_terms'.$suffix);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				// Throw database error exception.
				throw new Exception($this->_db->getErrorMsg(), 500);
			}
		}

		// Truncate the terms table.
		$this->_db->setQuery('TRUNCATE TABLE #__jxfinder_terms');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Truncate the taxonomy map table.
		$this->_db->setQuery('TRUNCATE TABLE #__jxfinder_taxonomy_map');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Delete all the taxonomy nodes except the root.
		$this->_db->setQuery('DELETE FROM #__jxfinder_taxonomy WHERE id > 1');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Truncate the tokens tables.
		$this->_db->setQuery('TRUNCATE TABLE `#__jxfinder_tokens`');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Truncate the tokens aggregate table.
		$this->_db->setQuery('TRUNCATE TABLE `#__jxfinder_tokens_aggregate`');
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		return true;
	}

	/**
	 * Method to published links in the index.
	 *
	 * @access	public
	 * @param	array	$link_ids	An array of link ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	public function publish($link_ids)
	{
		// Set the links states to unpublished.
		$query	= 'UPDATE #__jxfinder_links SET published = 1'
				. ' WHERE link_id = '.implode(' OR link_id = ', $link_ids);

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish links in the index.
	 *
	 * @access	public
	 * @param	array	$link_ids	An array of link ids.
	 * @return	bool	Returns true on success, false on failure.
	 * @since	1.0
	 */
	public function unpublish($link_ids)
	{
		// Set the links states to unpublished.
		$query	= 'UPDATE #__jxfinder_links SET published = 0'
				. ' WHERE link_id = '.implode(' OR link_id = ', $link_ids);

		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	protected function _getIndexQuery()
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('l.link_id, l.title, l.type_id, l.url, l.indexdate, l.state, l.published');
		$query->select('l.publish_start_date, l.publish_end_date, l.start_date, l.end_date');
		$query->select('t.title AS t_title');
		$query->from('#__jxfinder_links AS l');
		$query->join('INNER', '#__jxfinder_types AS t ON t.id = l.type_id');

		// Check the type filter.
		if ($this->getState('filter.type') !== 0) {
			$query->where('l.type_id = '.(int)$this->getState('filter.type'));
		}

		// Check for state filter.
		if ($this->getState('check.state')) {
			$query->where('l.published = '.(int)$this->getState('filter.state'));
		}

		// Check the search phrase.
		if ($this->getState('filter.search') != '')
		{
			$search = $this->_db->getEscaped($this->getState('filter.search'));
			$query->where('l.title LIKE "%'.$this->_db->getEscaped($search).'%"' .
						' OR l.url LIKE "%'.$this->_db->getEscaped($search).'%"' .
				 		' OR l.indexdate LIKE "%'.$this->_db->getEscaped($search).'%"');
		}

		$query->order($this->_db->getEscaped($this->getState('list.ordering')).' '.$this->getState('list.direction'));

		return $query;
	}

	protected function _getUrlQuery($cid)
	{
		$query	= 'SELECT url FROM #__jxfinder_links'
				. ' WHERE link_id = '.implode(' OR link_id = ', $cid);

		return $query;
	}
}