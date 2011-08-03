<?php
/**
 * @version		$Id: sketchy.php 472 2009-09-27 18:19:30Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Media
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * Image Filter class to make an image appear "sketchy".
 *
 * @package		JXtended.Libraries
 * @subpackage	Media
 * @version		1.0
 */
class JImageFilter_Sketchy extends JImageFilter
{
	function execute(&$handle)
	{
		// Make sure the file handle is valid.
		if ((!is_resource($handle) || get_resource_type($handle) != 'gd'))
		{
			$this->setError('Invalid File Handle');
			return false;
		}

		// Ensure the imagefilter function is available.
		if (!function_exists('imagefilter'))
		{
			$this->setError('Image Filter Function Not Available');
			return false;
		}

		// Perform sketchy filter.
		imagefilter($handle, IMG_FILTER_MEAN_REMOVAL);

		return true;
	}
}
