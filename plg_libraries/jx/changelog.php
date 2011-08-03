<?php
/**
 * @version		$Id: changelog.php 461 2009-09-23 06:42:42Z louis $
 * @package		JXtended.Libraries
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

?>
Changelog

2.0.0
----------
 - Removed Webservices package.
 - Removed CAPTCHA package.
 - Removed BBCode library.
 + Added component setup helper
 + Added JHttp client class.
 + Added JX JavaScript namespace.
 ^ Changed jximport() to jx()
 ^ Renamed JXQuery to JQuery to remain synched with the core.
 ^ Renamed JXImage to JImage

1.0.7
----------
 ^ Improved JXTemplate class
 + Added access field property to JXFormField::render array elements
 + Added default field property to JXFormField::render array elements
 + Added control.name field property to JXFormField::render array elements
 + Added control.id field property to JXFormField::render array elements
 + Added default.access field property to JXFormField::render array elements
 + Added error checking to JXImage::scale
 + Added JXQuery::innerJoin
 + Added JXQuery::outerJoin
 + Added JXQuery::leftJoin
 + Added JXQuery::rightJoin

1.0.6
----------
 + Added decorator support to the forms library

1.0.5
----------
 + Added JXLang JavaScript support for HTML JDocument types

1.0.4
----------
 # Enabled upgrade method
 # Allowed JXPagination to set pagination request variables and use page number or offset numbers.

1.0.3
----------
 # Updated Form Calendar field to be compatible with the JParameter Calendar field.

1.0.2
----------

 # textarea_editor now takes an attribute for the editor="desired|alternative" (if none provided the default is used)
 # Allowed JXPagination to set either frontend or backend style modes
 # Fixed hard-coded $task in JXPagination::orderUpIcon and JXPagination::orderDownIcon
 # Removed unused $condition argument in JXPagination::orderUpIcon and JXPagination::orderDownIcon
 # Fixed back background and font path in captcha library.
 ^ Added white-list and black-list options to JXFormView::renderToTable
 ^ Added JXFormView::render method
 ^ Added JXFormModel::filter method
 ! JXModel::checkin now returns a JException
