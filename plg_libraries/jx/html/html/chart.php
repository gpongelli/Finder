<?php
/**
 * @version		$Id: chart.php 266 2009-01-10 01:01:32Z louis $
 * @package		JXtended.Libraries
 * @subpackage	HTML
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * HTML helper class for rendering simple charts.
 *
 * @package 	JXtended.Libraries
 * @subpackage	HTML
 * @static
 */
class JHtmlChart
{
	function simple($data, $title = '', $type = 'p3', $translate = true)
	{
		$html	= '<table class="chart"><thead><tr><td>'.$title.'</td></tr></thead><tbody class="'.$type.'"><tr>';
		$total	= null;

		if (isset($data['total']))
		{
			$total = (int)$data['total'];
			unset($data['total']);
		}

		$ths	= array_keys($data);
		$tds	= array_values($data);

		// Process the table headings.
		for ($i = 0, $c = count($ths); $i < $c; $i++) {
			if ($translate) {
				$ths[$i] = JText::_($ths[$i]);
			}
			$html	.= '<th>'.$ths[$i].' ('.round(($tds[$i]/$total)*100, 0).'%)</th>';
		}

		$html	.= '</tr><tr>';

		// Process the table cells.
		for ($i = 0, $c = count($tds); $i < $c; $i++) {
			$html	.= '<td>'.($tds[$i] ? $tds[$i] : 0).'</td>';
		}

		$html	.= '</tr></tbody></table>';
		return $html;
	}
}