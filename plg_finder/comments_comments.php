<?php
/**
 * @version		$Id: comments_comments.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderComments_Comments
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_comments_comments');
$lang->load('plg_finder_comments_comments.custom');

/**
 * Finder adapter for Comments Comments.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderComments_Comments
 */
class plgFinderComments_Comments extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Comments_Comments';

	/**
	 * Method to load comments data for content items.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onPrepareFinderContent(FinderIndexerResult &$item)
	{
		static $installed, $parser;

		// Check if the extension is installed.
		if (is_null($installed))
		{
			$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->quote($this->_db->replacePrefix('#__jxcomments_comments')));
			$installed = (bool)$this->_db->loadResult();
		}

		// Return an empty array if not installed.
		if ($installed === false) {
			return array();
		}

		// Load the BBCode parser.
		if (!$parser && is_callable('jximport'))
		{
			// Import the BBCode parser.
			jximport('jxtended.html.bbcode');

			// Instantiate BBCode parser.
			$parser = JXBBCode::getInstance(array(
				'smiley_path' => JPATH_ROOT.'/media/jxtended/img/smilies/default',
				'smiley_url' => JURI::base().'media/jxtended/img/smilies/default'
			));
		}

		// Get the comments for the item.
		$sql = new JDatabaseQuery();
		$sql->select('c.id, c.name, c.url, c.email, c.subject, c.body');
		$sql->from('#__jxcomments_comments AS c');
		$sql->join('INNER', '#__jxcomments_threads AS t ON t.id = c.thread_id');
		$sql->where('c.published = 1');
		$sql->where('t.status = 1');
		$sql->where('t.page_url = '.$this->_db->quote($item->url));

		// Load the results.
		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			throw new Exception($this->_db->getErrorMsg(), 500);
		}

		// Check the results.
		if (empty($results)) {
			return true;
		}

		// Create a container for the comments text.
		$comments = array();

		// Prepare the comment text for the item.
		foreach ($results as $result)
		{
			// Add the comment subject and body to the array.
			$comments[] = $result->subject;
			$comments[] = $parser ? $parser->parse($result->body) : $result->body;
		}

		// Push the entire array into the item into the misc context.
		$item->comments = $comments;

		return true;
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _index(FinderIndexerResult $item)
	{

	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success.
	 */
	protected function _setup()
	{
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param	mixed		A JDatabaseQuery object or null.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : new JDatabaseQuery();

		return null;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function _getURL($id)
	{
		return true;
	}
}