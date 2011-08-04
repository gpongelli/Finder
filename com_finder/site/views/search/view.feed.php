<?php
/**
 * @version		$Id: view.feed.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC.  All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Search feed view class for the Finder package.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 */
class FinderViewSearch extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param	string	$tpl	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 */
	public function display($tpl = null)
	{
		// Adjust the list limit to the feed limit.
		JRequest::setVar('limit', JFactory::getApplication()->getCfg('feed_limit'));

		// Get view data.
		$state		= $this->get('State');
		$params		= $state->get('params');
		$query		= $this->get('Query');
		$results	= $this->get('Results');

		// Push out the query data.
		JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
		$suggested = JHtml::_('query.suggested', $query);
		$explained = JHtml::_('query.explained', $query);

		// Set the document title.
		$this->document->setTitle($params->get('page_title'));

		// Configure the document description.
		if (!empty($explained)) {
			$this->document->setDescription(html_entity_decode(strip_tags($explained), ENT_QUOTES, 'UTF-8'));
		}

		// Set the document link.
		$this->document->link = JRoute::_($query->toURI());

		// Convert the results to feed entries.
		foreach ($results as $result)
		{
			// Convert the result to a feed entry.
			$item = new JFeedItem();
			$item->title 		= $result->title;
			$item->link 		= JRoute::_($result->route);
			$item->description 	= $result->description;
			$item->date			= intval($result->start_date) ? JHtml::date($result->start_date, '%A %d %B %Y') : $result->indexdate;

			// Get the taxonomy data.
			$taxonomy = $result->getTaxonomy();

			// Add the category to the feed if available.
			if (isset($taxonomy['Category'])) {
				$node = array_pop($taxonomy['Category']);
				$item->category = $node->title;
			}

			// loads item info into rss array
			$this->document->addItem($item);
		}
	}
}