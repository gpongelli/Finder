<?php
/**
 * @package		JXtended.Finder
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Configuration view class for Finder.
 *
 * @package		JXtended.Finder
 * @subpackage	com_finder
 * @version		1.0
 */
class FinderViewConfig extends JView
{
	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$form			= $this->get('Form');
		$component		= $this->get('Component');
		$this->import	= $this->get('Import');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Bind the form to the data.
		if ($form && $component->params) {
			$form->bind($component->params);
		}

		// Prepare the view.
		$this->document->addStyleSheet('components/com_finder/media/css/finder.css');

		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

		$this->assignRef('form',		$form);
		$this->assignRef('component',	$component);

		$this->document->setTitle(JText::_('JGLOBAL_EDIT_PREFERENCES'));

		parent::display($tpl);
		JRequest::setVar('hidemainmenu', true);
	}
}
