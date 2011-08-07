<?php
/**
 * @version		$Id$
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jx('jx.application.component.modellist');

/**
 * Suggestions model class for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderModelSuggestions extends JModelList
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the _getStoreId() method and caching data structures.
	 *
	 * @var		string
	 */
	protected $_context = 'com_finder.suggestions';

	/**
	 * Method to get an array of data items.
	 *
	 * @return	array	An array of data items.
	 */
	public function getItems()
	{
		// Get the items.
		$items	= &parent::getItems();

		// Convert them to a simple array.
		foreach ($items as $k => $v) {
			$items[$k] = $v->term;
		}

		return $items;
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getListQuery()
	{
		$sql = new JDatabaseQuery();
		$sql->select('t.term');
		$sql->from('#__finder_terms AS t');
		$sql->where('t.term LIKE "'.$this->_db->getEscaped($this->getState('input'), true).'%"');
		$sql->where('t.common = 0');
		$sql->order('t.links DESC');
		$sql->order('t.weight DESC');

		return $sql;
	}

	/**
	 * Method to get a store id based on model the configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	An identifier string to generate the store id.
	 * @return	string	A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Add the search query state.
		$id .= ':'.$this->getState('input');
		$id .= ':'.$this->getState('language');

		// Add the list state.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');

		// Add the user access state.
		$id .= ':'.$this->getState('user.aid');

		return parent::_getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		// Get the configuration options.
		$app		= JFactory::getApplication();
		$params		= $app->getParams('com_finder');
		$user		= JFactory::getUser();

		// Get the query input.
		$this->setState('input', JRequest::getString('q', '', 'request'));
		$this->setState('language', JRequest::getString('l', '', 'request'));

		// Load the list state.
		$this->setState('list.start', 0);
		$this->setState('list.limit', 10);

		// Load the parameters.
		$this->setState('params', $params);

		// Load the user state.
		$this->setState('user.id', (int)$user->get('id'));
		$this->setState('user.aid',	(int)$user->get('aid'));
	}
}