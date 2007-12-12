<?php # -$Id$

/**
 * CLAROLINE
 *
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLDATE
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Michel Carbone <michel_c12@yahoo.fr>
 */


/**
 * Class date
 */

class claroDate
{

    /**
     * @var $timeStamp time stamp
     */    

    var $timeStamp = 0; 

    /**
     * @var $defaultDateFormat default format of date
     */ 
    
    var $defaultDateFormat = 'Y-m-d H:i:s'; // ISO 8601 format


    /**
    * Constructor of a date
    * @param integer date : timestamp
    * @param string dateFormat : date format
    */
    function claroDate($date = null, $dateFormat = null)
    {
        if    ( is_null($date)       ) $this->timeStamp = mktime(); // + claroDate::timeZoneOffset();
        elseif( is_int($date)        ) $this->timeStamp = $date > 0 ? $date : 0;
        elseif( is_null($dateFormat) ) $this->timeStamp = strtotime($date);
        else                           $this->timeStamp = strtotime($date);
    }

    function clarodate_set()
    {
        $currentDate      = mktime();
        $defaultSecView   = date("s", mktime());
        $defaultMinView   = date("i", mktime());
        $defaultHourView  = date("G", mktime());
        $defaultDayView   = date("j", mktime());
        $defaultWeekView  = date("W", mktime());
        $defaultMonthView = date("n", mktime());
        $defaultYearView  = date("Y", mktime());
    }
    
    /**
    * calculate time offset from GMT time or from time zone define by $timeZone
    *
    * @param integer $setTimeZone
    * integer $timeZone : static variable, by default is null
    * @return integer timezone
    */
    function timeZoneOffset($setTimeZone = null)
    {
        static $timeZone = 0;
        if ( ! is_null($setTimeZone) ) $timeZone += $setTimeZone * 3600;
        return $timeZone;
    }

    /**
    * get the list of names of the days
    *
    * @param string $format : sort of display
    * @return array string 
    */
    function getDayNameList($format = CAL_DOW_LONG)
    {
        $listName = $format ==  CAL_DOW_LONG ? 'dayNameListLong' : 'dayNameListShort';
        return claroDate::_dateNameList($listName);
    }

    /**
    * get the list of the names of the months
    */
    function getMonthNameList($format = CAL_MONTH_GREGORIAN_LONG)
    {
        $listName = $format ==  CAL_MONTH_GREGORIAN_LONG ? 'monthNameListLong' : 'monthNameListShort';
        return claroDate::_dateNameList($listName);
    }

    /**
    * define the name of the days
    *
    * @param array string $dayNameListLong
    * @param array string $dayNameListShort
    * @return array string day name list
    */
    function setDayNameList($dayNameListLong, $dayNameListShort)
    {
        if ( count($dayNameListLong) == 7 && count($dayNameListShort) == 7)
        {
            claroDate::_dateNameList('dayNameListLong', $dayNameListLong);
            claroDate::_dateNameList('dayNameListShort', $dayNameListShort);
        }
        else
        {
            die("Wrong list count :". __LINE__);
        }
    }

    /**
    * define the names of the months
    *
    * @param array string $monthNameListLong
    * @param array string $monthNameListShort
    * @return array string month name list
    */
    function setMonthNameList($monthNameListLong, $monthNameListShort)
    {
        if ( count($monthNameListLong) == 12 && count($monthNameListShort) == 12)
        {
            claroDate::_dateNameList('monthNameListLong', $monthNameListLong);
            claroDate::_dateNameList('monthNameListShort', $monthNameListShort);
        }
        else
        {
            die("Wrong list count :". __LINE__);
        }
    }

    /**
    * return the date in the format yyyy-mm-dd hh:mm:ss if no format specified
    *
    * @param string $dateFormat
    * @return string date
    */
    function getFormatedDate($dateFormat = null)
    {
        if ( is_null($dateFormat) ) $dateFormat = $this->defaultDateFormat;

        return date($dateFormat, $this->timeStamp);
    }
    
    /**
    * @param integer time : timestamp
    * @param string dateFormat
    * @return string formated date
    */
    function getFormatedDateFromTimeStamp($time, $dateFormat = null)
    {
        if ( is_null($dateFormat) ) $dateFormat = 'Y M d';

        return date($dateFormat, $time);
    }

    /**
    * return the Unix time
    */
    function getTimeStamp()
    {
        return $this->timeStamp;
    }
    
    /**
    * return the seconds of the date that called the function
    *
    @return integer : number of seconds of the date
    */
    function getSec()
    {
        return (int) strftime( '%S' , $this->timeStamp);
    }

    /**
    * @param integer sec : integer of seconds to convert
    * @return integer of number of seconds
    */
    function secToSec($sec)
    {
        return (int) $sec;
    }

    /**
    *  the minutes of the date that called the function
    */
    function getMin()
    {
        return (int) strftime( '%M' , $this->timeStamp);
    }

    /**
    * @param integer $min : minutes to convert to seconds
    * @return integer : number of seconds
    */
    function minToSec($min)
    {
        return (int) $min*60;
    }

    /**
    * return the hours of the date that called the function
    */
    function getHour()
    {
        return (int) strftime( '%H ' , $this->timeStamp);
    }

    /**
    * @param integer $hour : integer hours to convert
    * @return integer of the number of hours in seconds
    */
    function hourToSec($hour)
    {
        return (int) $hour*60*60;
    }

    /**
    * get the day of the date that called the function in an int 
    * sunday .. saturday -> 0 .. 6
    */
    function getDayOfWeek()
    {
        return $this->_dayNameToDayNum( strftime( '%a' , $this->timeStamp) );
    }
    
    

    /**
    * return the day name of the date that called the function
    * @param string $format
    * @return string : name of the day in specified format
    */
    function getDayName($format = CAL_DOW_LONG)
    {
        $dayNameList = claroDate::getDayNameList($format);
        return $dayNameList[$this->getDayofWeek()];
    }

    /**
    * @param integer $timeStamp : integer hours to convert
    * @return string : name of the day
    */
    function getDayofWeekFromTimeStamp($timeStamp)
    {
        return strftime( '%a' , $timeStamp);
    }
    
    /**
    * @param integer $timeStamp : integer hours to convert
    * @return integer : number between 1 and 7 corresponding to the day of the week
    */
    function getNbrDayofWeekFromTimeStamp($timeStamp)
    {
        return clarodate::_dayNameToDayNum(clarodate::getDayofWeekFromTimeStamp($timeStamp));
    }
    
    /**
    * @return the number of the day
    * 1 .. 28, 29, 30, 31
    */
    function getDayOfMonth()
    {
        return (int) strftime( '%d' , $this->timeStamp);
    }

    
    function getDayCountInMonth()
    {
        return date('t', $this->getTimeStamp() );
    }

    /**
    * return the number of days from the 1st january of the year of the date that called the function
    * 1 .. 365-366
    */
    function getDayOfYear()
    {
        return (int) strftime( '%j' , $this->timeStamp);
    }

    /**
    * @return the current week of the year
    * 1 .. 52
    */
    function getWeekofYear()
    {
        return (int) strftime( '%W' , $this->timeStamp);
    }
    
    /**
    * @param integer $time : integer hours to convert
    * @return integer : number of the week in the year
    */
    function getWeekofYearFromTimeStamp($time)
    {
        return (int) strftime( '%W' , $time);
    }

    /**
    * return an int corresponding to the month of the date
    * 1 .. 12
    */
    function getMonth()
    {
        return (int) strftime( '%m' , $this->timeStamp);
    }


    /**
    * @param integer time : timestamp
    * @return integer the seconds as a decimal number from the time stamp sent in argument
    */
    function getSecFromTimeStamp($time)
    {
        $timeStamp=$time;
        return (int) strftime ('%S' , $timeStamp);
    }

    /**
    * @param integer time : timestamp
    * @return integer the minutes as a decimal number from the time stamp sent in argument
    */    
    function getMinFromTimeStamp($time)
    {
        $timeStamp=$time;
        return (int) strftime ('%M' , $timeStamp);
    }
    
    /**
    * @param integer time : timestamp
    * @return integer the hours as a decimal number from the time stamp sent in argument
    */
    function getHourFromTimeStamp($time)
    {
        $timeStamp=$time;
        return (int) strftime ('%H' , $timeStamp);
    }

    /**
    * @param integer $time : timestamp
    * @return integer the day as a decimal number from the time stamp sent in argument
    */
    function getDayFromTimeStamp($time)
    {
        $timeStamp=$time;
        return (int) strftime ('%d' , $timeStamp);
    }

    /**
    * @param integer $time : timestamp
    * @return integer the month as a decimal number from the time stamp sent in argument
    */    
    function getMonthFromTimeStamp($time)
    {
        $timeStamp=$time;
        return (int) strftime ('%m' , $timeStamp);
    }

    /**
    * @param integer $time : timestamp
    * @return integer the year as a decimal number from the time stamp sent in argument
    */
    function getYearFromTimeStamp($time)
    {
        $timeStamp=$time;
        //var_dump($time);
        return (int) strftime ('%Y' , $timeStamp);
    }
    
    /**
    * function that return the last day of the month
    *
    * @param integer $time : timestamp
    * @return integer number of days in the month including the $time
    */
    function getLastDayFromTimeStamp($time)
    {
        $Month = clarodate::getMonthFromTimeStamp($time);
        $year = clarodate::getYearFromTimeStamp($time);
        $lastday = mktime(0, 0, 0, $Month+1, 0, $year);
        return (int)strftime("%d", $lastday);
    }
    
    /**
    * function that return the timestamp of the first day of the month
    *
    * @param integer $time : time stamp of a date in the month
    * @return integer : a time stamp
    */
    function monthStartDateFromTimeStamp($time)
    {
        $refMonth = clarodate::getMonthFromTimeStamp($time);
        $refYear = clarodate::getYearFromTimeStamp($time);
        return $monthStartDate  = mktime(0,0,0,$refMonth,1,$refYear);

    }
    
    /**
    * function that return the timestamp of the day of the month of the date of the time stamp sent in argument
    *
    * @param integer $time : timestamp
    * @return integer : timestamp of the last day of the month
    */
    function monthEndDateFromTimeStamp($time)
    {
        $refMonth = clarodate::getMonthFromTimeStamp($time);
        $refYear = clarodate::getYearFromTimeStamp($time);
        return $monthendDate = mktime(0,0,0,$refMonth,clarodate::getLastDayFromTimeStamp($time),$refYear);
    }
 
    /**
    * return a new time stamp with the month offset sent in argument
    *
    * @param integer $date : timestamp
    * @param integer $offset : offset of month
    * @return integer : timestamp
    */ 
    function setMonthOffset($date,$offset)
    {
        $time = $date;
        $month = clarodate::getMonthFromTimeStamp($time);
        //echo $month;
        $month+=$offset;
        $year=$defaultYear;
        if($month==12 && $offset==1)
            $year+=1;
        else
            if($month==1 && $offset==-1)
            $year+=-1;
            $dateTimeStamp=mktime( $defaultHourView, $defaultMinView,$defaultSecView
                                    , $month, $defaultDayView, $year);
        return $dateTimeStamp;
    }

    /**
    * return the name of the month of the date 
    * January ... December
    *
    * @param string $format
    * @return string : name of the month
    */
    function getMonthName($format = CAL_MONTH_GREGORIAN_LONG)
    {
        $monthNameList = claroDate::getMonthNameList($format);
        return $monthNameList[$this->getMonth()-1];
    }
    
    /**
    * return the name of the month of the date 
    * January ... December
    *
    * @param integer $timestamp
    * @param string $format
    * @return string : name of the month
    */
    function getMonthNameFromTimeStamp($timeStamp, $format = CAL_MONTH_GREGORIAN_LONG)
    {
        $monthNameList = claroDate::getMonthNameList($format);
        return $monthNameList[claroDate::getMonthFromTimeStamp($timeStamp)-1];
    }
        
    /**
    * get an int corresponding to the year from the objet date
    */
    function getYear()
    {
        return (int) strftime( '%Y' , $this->timeStamp);
    }

    /**
    * function for switching from one month to the next or the preceding
    *
    * @param integer timestamp : reference date time
    * @param integer offset : specifie if month switch is positive or negative
    * @return integer timestamp 
    */
    function month_switch($timestamp, $offset)
    {
        if($offset ==1)
            $timestamp=$timestamp+86400;
        else if(offset==-1)
            $timestamp=$timestamp-86400;
        
        return  $timestamp;
    }

    /**
    * set a new month in the date
    *
    * @param $time date time in timestamp to modify
    * @param $offset number of second of offset
    * @return integer timestamp
    */
    function setTimeStampOffset($time, $offset)
    {
        $time;
        $offset;
        return (int)( $time+$offset);
    }

    /**
    * @return integer timestamp
    */
    function setNewMonth($offset)
    {
        $varmonth;
        $varyear;
        $this->varmonth;
        $this->varyear;
        $varmonth = $varmonth+$offset;
        $varyear = $varyear;
        $newdate=mktime(0,0,0,$varmonth,1,$varyear);
        return $newdate;
        }

    /**
    * tell if year is bisectile or not
    * 
    * @return boolean : true if is leap year
    */
    function isLeapYear()
    {
        return (bool) date('L', $this->timeStamp);
    }

    // COMPARISON METHODS

    /**
    * compare if equals two date objects
    * @param object claroDate
    * @return boolean
    */
    function equals($dateObject)
    {
        return $this->timeStamp == $dateObject->getTimeStamp();
    }

    /**
    * compare if $dateObject is after this
    * @param object claroDate
    * @return boolean
    */
    function after($dateObject)
    {
        return $this->timeStamp > $dateObject->getTimeStamp();
    }
    
    /**
    * compare if $dateObject is before this
    * @param object claroDate
    * @return boolean
    */
    function before($dateObject)
    {
        return $this->timeStamp < $dateObject->getTimeStamp();
    }

    /*
    * this function compare two $dateObject and return :
    *  0 if equals
    *  1 if $dateObject is greater than $this
    * -1 if $dateObject is minus than $this
    */
    function compare($dateObject)
    {
        if ( $this->timeStamp == $dateObject->getTimeStamp() )
        {
            return 0;
        }
        else 
        {
            return $this->timeStamp > $dateObject->getTimeStamp() ? 1 : -1;
        }
    }

    // ROLLING METHOD

    function rollSeconds($secCount)
    {
        return $this->_roolDateElement('sec', $secCount);
    }
    
    function rollSecondsFromTimeStamp($timeStamp, $secCount)
    {
        $secCountTS=$secCount;
        return $timeStamp + $secCountTS;    
    }

    function rollMinutes($minCount)
    {
        return $this->_roolDateElement('min', $minCount);
    }

    function rollMinutesFromTimeStamp($timeStamp, $minCount)
    {
        $minCountTS=$minCount*60;
        return $timeStamp + $minCountTS;    
    }
        
    function rollHours($hourCount)
    {
        return $this->_roolDateElement('hour', $hourCount);
    }
    
    function rollHoursFromTimeStamp($timeStamp, $hourCount)
    {
        $hourCountTS=$hourCount*60*60;
        return $timeStamp + $hourCountTS;    
    }
    
    function rollDays($dayCount)
    {
        return $this->_roolDateElement('day', $dayCount);
    }
    
    function rollDaysFromTimeStamp($timeStamp, $dayCount)
    {
        $dayCountTS=$dayCount*24*60*60;
        return $timeStamp + $dayCountTS;
    }

    function rollWeeks($weekCount)
    {
        return $this->_roolDateElement('week', $weekCount);
    }
    
    function rollWeeksFromTimeStamp($timeStamp, $weekCount)
    {
        $WeekCountTS=$weekCount*60*60*24*7;
        return $timeStamp + $weekCountTS;    
    }
    
    function rollMonths($monthCount)
    {
        return $this->_roolDateElement('month', $monthCount);
    }

    function rollYears($YearCount)
    {
        return $this->_roolDateElement('year', $YearCount);
    }

    /*
     * PRIVATE METHODS
     */

    function _roolDateElement($elmtType, $elmtCount)
    {
        $dateElementList             = $this->_getDateElements();
        $dateElementList[$elmtType] =  $dateElementList[$elmtType] 
                                     + (int) $elmtCount;

        return new ClaroDate( $this->_dateElements2TimeStamp($dateElementList) );
    }
  
    function _getDateElements()
    {
        $formatedDate = $this->getFormatedDate();

        list($year, $month, $day, 
             $hour, $min, $sec   ) = preg_split('/[-: ]/', $formatedDate);

        return array('year' => $year, 'month' => $month, 'day' => $day,
                     'hour' => $hour, 'min'   => $min,   'sec'=>$sec);
    }

    function _dateElements2TimeStamp($elmtList)
    {
        return mktime($elmtList['hour' ],  
                      $elmtList['min'  ],  
                      $elmtList['sec'  ],
                      $elmtList['month'],  
                      $elmtList['day'  ], 
                      $elmtList['year' ]);
    }

    /**
    * private function that convert short day name to a integer
    *
    * @param string $string : date name in short format
    * @return integer : day number
    */
    function _dayNameToDayNum($string)
    {
        $string = strtolower($string);

        switch ($string)

        {
            case 'sun':
                $dayNum = 0;
                break;
            case 'mon':
                $dayNum = 1;
                break;
            case 'tue':
                $dayNum = 2;
                break;
            case 'wed':
                $dayNum = 3;
                break;
            case 'thu':
                $dayNum = 4;
                break;
            case 'fri':
                $dayNum = 5;
                break;
            case 'sat':
                $dayNum = 6;
                break;
        }

        return $dayNum;
    }

    /**
    * private function that return name of the days month in short and long format
    */
    function _dateNameList($listName, $list = null)
    {
        global $langDay_of_weekNames;
        global $langMonthNames;

        static $dayNameListLong ;
        static $dayNameListShort;
        static $monthNameListLong;
        static $monthNameListShort;

        if ( isset($langDay_of_weekNames) && isset($langMonthNames) )
        {
            $dayNameListLong = $langDay_of_weekNames['long'];
            $dayNameListShort = $langDay_of_weekNames['short'];
            $monthNameListLong = $langMonthNames['long'];
            $monthNameListShort = $langMonthNames['short'];
        }
        else
        {
            $dayNameListLong    = array('sunday','monday','tuesday','wednesday', 
                                        'thursday','friday','saturday');
            $dayNameListShort   = array('sun','mon','tue','wed', 
                                          'thu','fri','sat');
            $monthNameListLong  = array('january', 'february', 'march', 
                                        'april', 'may', 'june', 'july', 'august',
                                        'september', 'october', 'november', 
                                        'december');
            $monthNameListShort = array('jan', 'feb', 'mar', 'apr', 
                                        'may', 'jun', 'jul', 'aug', 'sept', 'oct', 
                                        'nov', 'dec');
        }

        if ($list) $$listName = $list;
        else       return $$listName;
    }
}


?>
