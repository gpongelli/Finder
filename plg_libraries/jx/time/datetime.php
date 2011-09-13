<?php
/**
 * @version		$Id: datetime.php 473 2009-09-27 18:29:59Z louis $
 * @package		JXtended.Libraries
 * @subpackage	Time
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * A class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 *
 * @package		JXtended.Libraries
 * @subpackage	Time
 * @since		2.0
 */
class JDateTime extends DateTime
{
	/**
	 * The format string to be applied when using the __toString() magic method.
	 *
	 * @var		string
	 * @since	2.0
	 */
    public static $format = 'Y-m-d H:i:s';

	/**
	 * Placeholder for a DateTimeZone object with GMT as the time zone.
	 *
	 * @var		object
	 * @since	2.0
	 */
	protected static $gmt;

	/**
	 * Placeholder for a DateTimeZone object with the default server
	 * time zone as the time zone.
	 *
	 * @var		object
	 * @since	2.0
	 */
	protected static $stz;

	/**
	 * An array of offsets and time zone strings representing the available
	 * options from Joomla! 1.5 and below.
	 *
	 * @deprecated	Deprecated since 2.0
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected static $offsets = array(
		-12 => 'Etc/GMT-12',
		-11 => 'Pacific/Midway',
		-10 => 'Pacific/Honolulu',
		-9.5 => 'Pacific/Marquesas',
		-9 => 'US/Alaska',
		-8 => 'US/Pacific',
		-7 => 'US/Mountain',
		-6 => 'US/Central',
		-5 => 'US/Eastern',
		-4.5 => 'America/Caracas',
		-4 => 'America/Barbados',
		-3.5 => 'Canada/Newfoundland',
		-3 => 'America/Buenos_Aires',
		-2 => 'Atlantic/South_Georgia',
		-1 => 'Atlantic/Azores',
		0 => 'Europe/London',
		1 => 'Europe/Amsterdam',
		2 => 'Europe/Istanbul',
		3 => 'Asia/Riyadh',
		3.5 => 'Asia/Tehran',
		4 => 'Asia/Muscat',
		4.5 => 'Asia/Kabul',
		5 => 'Asia/Karachi',
		5.5 => 'Asia/Calcutta',
		5.75 => 'Asia/Katmandu',
		6 => 'Asia/Dhaka',
		6.30 => 'Indian/Cocos',
		7 => 'Asia/Bangkok',
		8 => 'Australia/Perth',
		8.75 => 'Australia/West',
		9 => 'Asia/Tokyo',
		9.5 => 'Australia/Adelaide',
		10 => 'Australia/Brisbane',
		10.5 => 'Australia/Lord_Howe',
		11 => 'Pacific/Kosrae',
		11.30 => 'Pacific/Norfolk',
		12 => 'Pacific/Auckland',
		12.75 => 'Pacific/Chatham',
		13 => 'Pacific/Tongatapu',
		14 => 'Pacific/Kiritimati'
	);

	/**
	 * The DateTimeZone object for usage in rending dates as strings.
	 *
	 * @var		object
	 * @since	2.0
	 */
	protected $_tz;

	/**
	 * Constructor.
	 *
	 * @param	string	String in a format accepted by strtotime(), defaults to "now".
	 * @param	mixed	Time zone to be used for the date.
	 * @return	void
	 * @since	1.0
	 *
	 * @throws	JException
	 */
    public function __construct($date = null, $tz = null)
    {
		// Create the base GMT and server time zone objects.
		if (empty(self::$gmt) || empty(self::$stz))
		{
			self::$gmt = new DateTimeZone('GMT');
			self::$stz = new DateTimeZone(date_default_timezone_get());
		}

    	// If the time zone object is not set, attempt to build it.
		if (!$tz instanceof DateTimeZone)
		{
			if($tz === null) {
			    $tz = self::$gmt;
			}
			// Translate from offset.
			elseif (is_numeric($tz)) {
			    $tz = new DateTimeZone(self::$offsets[$tz]);
			}
			elseif (is_string($tz)) {
			    $tz = new DateTimeZone($tz);
			}
		}

		// Call the DateTime constructor.
        parent::__construct($date, $tz);

		// Set the timezone object for access later.
		$this->_tz = $tz;
    }

	/**
	 * Magic method to render the date object in the format specified in the public
	 * static member JDate::$format.
	 *
	 * @return	string	The date as a formatted string.
	 * @since	2.0
	 */
    public function __toString()
    {
        return (string) parent::format(self::$format);
    }

	/**
	 * Get the time offset from GMT in hours or seconds.
	 *
	 * @param	boolean	True to return the value in hours.
	 * @return	float	The time offset from GMT either in hours in seconds.
	 * @since	2.0
	 */
	public function getOffsetFromGMT($hours = false)
	{
		return (float) $hours ? ($this->_tz->getOffset($this) / 3600) : $this->_tz->getOffset($this);
	}

	/**
	 * Gets the date as a formatted string.
	 *
	 * @param	string	The date format specification string (see {@link PHP_MANUAL#date})
	 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
	 * @return	string	The date string in the specified format format.
	 * @since	2.0
	 */
	public function format($format, $local = true)
	{
		// If the returned time should not be local use GMT.
		if (!$local)
		{
			parent::setTimezone(self::$gmt);
			$return = parent::format($format);
			parent::setTimezone($this->_tz);
		}
		else {
			$return = parent::format($format);
		}

		return $return;
	}

	/**
	 * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
	 * can be found at the IETF Web site.
	 *
	 * @link http://www.ietf.org/rfc/rfc2822.txt
	 *
	 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
	 * @return	string	The date string in RFC 822 format.
	 * @since	1.0
	 */
	public function toRFC822($local = false)
	{
		return $this->format(DATE_RFC822, $local);
	}

	/**
	 * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
	 * and it can be found at the IETF Web site.
	 *
	 * @link http://www.ietf.org/rfc/rfc3339.txt
	 *
	 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
	 * @return	string	The date string in ISO 8601 format.
	 * @since	1.0
	 */
	public function toISO8601($local = false)
	{
		return $this->format(DATE_ISO8601, $local);
	}

	/**
	 * Gets the date as an MySQL datetime string.
	 *
	 * @link http://dev.mysql.com/doc/refman/5.0/en/datetime.html
	 *
	 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
	 * @return	string	The date string in MySQL datetime format.
	 * @since	1.0
	 */
	public function toMySQL($local = false)
	{
		return $this->format('Y-m-d H:i:s', $local);
	}

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @return	integer	The date as a UNIX timestamp.
	 * @since	1.0
	 */
	public function toUnix()
	{
		return (int) parent::format('U');
	}

	/**
	 * Method to wrap the setTimezone() function and set the internal
	 * time zone object.
	 *
	 * @param	object	The new DateTimeZone object.
	 * @return	object	The old DateTimeZone object.
	 * @since	2.0
	 */
	public function setTimezone(DateTimeZone $tz)
	{
		$this->_tz = $tz;
		return parent::setTimezone($tz);
	}

	/**
	 * Set the date offset (in hours).
	 *
	 * @deprecated	Deprecated since 2.0
	 *
	 * @param	float	The offset in hours.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function setOffset($offset)
	{
		// Only set the timezone if the offset exists.
		if (isset(self::$offsets[$offset]))
		{
			$this->_tz = new DateTimeZone(self::$offsets[$offset]);
			$this->setTimezone($this->_tz);
			return true;
		}

		return false;
	}

	/**
	 * Gets the date in a specific format
	 *
	 * Returns a string formatted according to the given format. Month and weekday names and
	 * other language dependent strings respect the current locale
	 *
	 * @deprecated	Deprecated since 2.0
	 *
	 * @param	string	The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
	 * @return	string	The date as a formatted string.
	 * @since	1.0
	 */
	public function toFormat($format = '%Y-%m-%d %H:%M:%S', $local = true)
	{
		// Generate the timestamp.
		$time = (int) parent::format('U');

		// If the returned time should be local add the GMT offset.
		if ($local) {
			$time += $this->getOffsetFromGMT();
		}

		// Manually modify the month and day strings in the format.
		if (strpos($format, '%a') !== false) {
			$format = str_replace('%a', $this->_dayToString(date('w', $time), true), $format);
		}
		if (strpos($format, '%A') !== false) {
			$format = str_replace('%A', $this->_dayToString(date('w', $time)), $format);
		}
		if (strpos($format, '%b') !== false) {
			$format = str_replace('%b', $this->_monthToString(date('n', $time), true), $format);
		}
		if (strpos($format, '%B') !== false) {
			$format = str_replace('%B', $this->_monthToString(date('n', $time)), $format);
		}

		// Generate the formatted string.
		$date = strftime($format, $time);

		return $date;
	}

	/**
	 * Translates month number to a string.
	 *
	 * @deprecated	Deprecated since 2.0
	 *
	 * @param	integer	The numeric month of the year.
	 * @param	boolean	Return the abreviated month string?
	 * @return	string	The month of the year.
	 * @since	1.0
	 */
	protected function _monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:  return $abbr ? JText::_('JANUARY_SHORT')	: JText::_('JANUARY');
			case 2:  return $abbr ? JText::_('FEBRUARY_SHORT')	: JText::_('FEBRUARY');
			case 3:  return $abbr ? JText::_('MARCH_SHORT')		: JText::_('MARCH');
			case 4:  return $abbr ? JText::_('APRIL_SHORT')		: JText::_('APRIL');
			case 5:  return $abbr ? JText::_('MAY_SHORT')		: JText::_('MAY');
			case 6:  return $abbr ? JText::_('JUNE_SHORT')		: JText::_('JUNE');
			case 7:  return $abbr ? JText::_('JULY_SHORT')		: JText::_('JULY');
			case 8:  return $abbr ? JText::_('AUGUST_SHORT')	: JText::_('AUGUST');
			case 9:  return $abbr ? JText::_('SEPTEMBER_SHORT')	: JText::_('SEPTEMBER');
			case 10: return $abbr ? JText::_('OCTOBER_SHORT')	: JText::_('OCTOBER');
			case 11: return $abbr ? JText::_('NOVEMBER_SHORT')	: JText::_('NOVEMBER');
			case 12: return $abbr ? JText::_('DECEMBER_SHORT')	: JText::_('DECEMBER');
		}
	}

	/**
	 * Translates day of week number to a string.
	 *
	 * @deprecated	Deprecated since 2.0
	 *
	 * @param	integer	The numeric day of the week.
	 * @param	boolean	Return the abreviated day string?
	 * @return	string	The day of the week.
	 * @since	1.0
	 */
	protected function _dayToString($day, $abbr = false)
	{
		switch ($day)
		{
			case 0: return $abbr ? JText::_('SUN') : JText::_('SUNDAY');
			case 1: return $abbr ? JText::_('MON') : JText::_('MONDAY');
			case 2: return $abbr ? JText::_('TUE') : JText::_('TUESDAY');
			case 3: return $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
			case 4: return $abbr ? JText::_('THU') : JText::_('THURSDAY');
			case 5: return $abbr ? JText::_('FRI') : JText::_('FRIDAY');
			case 6: return $abbr ? JText::_('SAT') : JText::_('SATURDAY');
		}
	}
}
