<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * System plugin class for Finder.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Finder
 * @since       2.5
 */
class plgSystemFinder extends JPlugin
{
	/**
	 * Method to catch the onAfterDispatch event.
	 *
	 * This is where we setup the click-through content highlighting for Finder
	 * search results. The highlighting is done with JavaScript so we just
	 * need to check a few parameters and the JHtml behavior will do the rest.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   2.5
	 */
	public function onAfterDispatch()
	{
		// Check that we are in the site application.
		if (JFactory::getApplication()->isAdmin())
		{
			return true;
		}

		// Check if the highlighter is enabled.
		if (!JComponentHelper::getParams('com_finder')->get('highlight_content_search_terms', 1))
		{
			return true;
		}

		// Check if the highlighter should be activated in this environment.
		if (JFactory::getDocument()->getType() !== 'html' || JRequest::getCmd('tmpl') === 'component')
		{
			return true;
		}

		// Get the terms to highlight from the request.
		$terms = JRequest::getVar('qh', null, 'request', 'base64');
		$terms = $terms ? @unserialize(@base64_decode($terms)) : null;

		// Check the terms.
		if (empty($terms))
		{
			return true;
		}

		// Activate the highlighter.
		JHtml::addIncludePath(JPATH_SITE.'/components/com_finder/helpers/html');
		JHtml::stylesheet('plugins/system/finder/media/css/finder.css', false, false, false);
		JHtml::_('finder.highlighter', $terms);

		// Adjust the component buffer.
		$doc = JFactory::getDocument();
		$buf = $doc->getBuffer('component');
		$buf = '<br id="finder-highlighter-start" />'.$buf.'<br id="finder-highlighter-end" />';
		$doc->setBuffer($buf, 'component');

		return true;
	}
}
