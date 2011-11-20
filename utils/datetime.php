<?php
// class.izDateTime.php
// version 2.0.0, 2004-07-29
//
// Description
//
// A PHP library of helpful date methods.  This class does not
// need to be instantiated.
//
// Author
//
// Andrew Collington, 2004
// php@amnuts.com, http://php.amnuts.com/
//
// Feedback
//
// There is message board at the following address:
//
//    http://php.amnuts.com/forums/index.php
//
// Please use that to post up any comments, questions, bug reports, etc.  You
// can also use the board to show off your use of the script.
//
// License
//
// This class is available free of charge for personal or non-profit work.  If
// you are using it in a commercial setting, please contact php@amnuts.com for
// payment and licensing terms.
//
// Support
//
// If you like this script, or any of my others, then please take a moment
// to consider giving a donation.  This will encourage me to make updates and
// create new scripts which I would make available to you.  If you would like
// to donate anything, then there is a link from my website to PayPal.
//

class izDateTime extends Object
{
    /**
     * Compares two dates.
     *
     * Returns:
     *
     *     < 0 if date1 is less than date2;
     *     > 0 if date1 is greater than date2;
     *     0 if they are equal. 
     *
     * @return int
     * @param  string|timestamp $date1
     * @param  string|timestamp $date2
     */
    public static function compareDates($date1, $date2)
    {
        if (!is_numeric($date1)) {
            $date1 = izDateTime::timeStringToStamp($date1);
        }
        if (!is_numeric($date2)) {
            $date2 = izDateTime::timeStringToStamp($date2);
        }
        if ($date1 < $date2) {
            return -1;
        } else if ($date1 > $date2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Converts a date/time string to Unix timestamp
     *
     * @return timestamp
     * @param  string $string
     */
    public static function timeStringToStamp($string)
    {
        return strtotime($string);
    }

    /**
     * Converts Unix timestamp to a date/time string using format given
     *
     * Special options can be passed for the format parameter.  These are
     * set format types.  The options currently include:
     *
     *     o mysql
     *
     * If the time parameter isn't supplied, then the current local time
     * will be used.
     *
     * @return string
     * @param  integer $time
     * @param  string  $format
     */
    public static function timeStampToString($time = 0, $format = 'Y-m-d H:i:s')
    {
        if ($format == 'mysql') {
            $format = 'Y-m-d H:i:s';
        }
        
        if ($time == 0) {
            $time = time();
        }
        
        return date($format, $time);
    }

    /**
     * Converts a Unix timestamp or date/time string to a specific format.
     *
     * Special options can be passed for the format parameter.  These are
     * set format types.  The options currently include:
     *
     *     o mysql
     *
     * If the time parameter isn't supplied, then the current local time
     * will be used.
     *
     * @return string
     * @param  integer|string $time
     * @param  string         $format
     * @see    timeStringToStamp()
     * @see    timeStampToString()
     */
    public static function timeFormat($time = 0, $format = 'Y-m-d H:i:s')
    {
        if (!is_numeric($time)) {
            $time = izDateTime::timeStringToStamp($time);
        }
        
        if ($time == 0) {
            $time = time();
        }

        return izDateTime::timeStampToString($time, $format);
    }

    /**
     * Converts a Unix timestamp or date/time string to a human-readable 
     * format, such as '1 day, 2 hours, 42 mins, and 52 secs'
     *
     * Based on the word_time() function from PG+ (http://pgplus.ewtoo.org)
     *
     * @return string
     * @param  integer|string $time
     * @see    timeStringToStamp()
     */
    public static function timeToHumanReadable($time = 0)
    {
        if (!is_numeric($time)) {
            $time = izDateTime::timeStringToStamp($time);
        }

        if ($time == 0) {
            return 'no time at all';
        } else {
            if ($time < 0) {
                $neg = 1;
                $time = 0 - $time;
            } else {
                $neg = 0;
            }
    
            $days = $time / 86400;
            $days = floor($days);
            $hrs  = ($time / 3600) % 24;
            $mins = ($time / 60) % 60;
            $secs = $time % 60;
    
            $timestring = '';
            if ($neg) {
                $timestring .= 'negative ';
            }
            if ($days) {
                $timestring .= "$days day" . ($days == 1 ? '' : 's');
                if ($hrs || $mins || $secs) {
                    $timestring .= ', ';
                }
            }
            if ($hrs) {
                $timestring .= "$hrs hour" . ($hrs == 1 ? '' : 's');
                if ($mins && $secs) {
                    $timestring .= ', ';
                }
                if (($mins && !$secs) || (!$mins && $secs)) {
                    $timestring .= ' and ';
                }
            }
            if ($mins) {
                $timestring .= "$mins min" . ($mins == 1 ? '' : 's');
                if ($mins && $secs) {
                    $timestring .= ', ';
                }
                if ($secs) {
                    $timestring .= ' and ';
                }
            }
            if ($secs) {
                $timestring .= "$secs sec" . ($secs == 1 ? '' : 's');
            }
            return $timestring;
        }
    }

    /**
     * Give a slightly more fuzzy time string. such as: yesterday at 3:51pm
     *     
     *
     * @return string
     * @param  integer|string $time
     * @see    timeStringToStamp()
     */
    public static function fuzzyTimeString($time = 0)
    {
        if (!is_numeric($time)) {
            $time = izDateTime::timeStringToStamp($time);
        }

        $now = time();
        $sodTime = mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));
        $sodNow  = mktime(0, 0, 0, date('m', $now), date('d', $now), date('Y', $now));
        
        if ($sodNow == $sodTime) {
            return 'today at ' . date('g:ia', $time); // check 'today'
        } else if (($sodNow - $sodTime) <= 86400) {
            return 'yesterday at ' . date('g:ia', $time); // check 'yesterday'
        } else if (($sodNow - $sodTime) <= 432000) {
            return date('l \a\\t g:ia', $time); // give a day name if within the last 5 days
        } else if (date('Y', $now) == date('Y', $time)) {
            return date('M j \a\\t g:ia', $time); // miss off the year if it's this year
        } else {
            return date('M j, Y \a\\t g:ia', $time); // return the date as normal
        }
    }
}
?>