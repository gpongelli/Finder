/**
 * @version		$Id: _jx.js 415 2009-07-16 19:40:36Z louis $
 * @package		JXtended.Libraries
 * @subpackage	JavaScript
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

// Only define the JXtended namespace if not defined.
if (typeof(JX) === 'undefined') {
	var JX = {};
}

// An object to hold each editor instance on page
JX.editors = {instances: {}};

// An object to hold various behavior options.
JX.Options = {};

// An object to hold various behavior classes.
JX.Classes = {};

// Object to hold translation strings.  Has two functions: _() which mirrors JText::_() and load() which loads an object.
JX.JText = {
	strings: {},
	'_': function(key, def) {
		return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;
	},
	load: function(object) {
		for (var key in object) {
			this.strings[key.toUpperCase()] = object[key];
		}
		return this;
	}
};

// Function to replace all request tokens on the page.
JX.replaceTokens = function(n) {
	var els = document.getElementsByTagName('input');
	for (var i = 0; i < els.length; i++) {
		if ((els[i].type == 'hidden') && (els[i].name.length == 32) && els[i].value == '1') {
			els[i].name = n;
		}
	}
};
