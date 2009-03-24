<?php
/**
 * Convert a unix timestamp (in seconds) to a scorm 2004 time 
 *
 * @param integer $unixTime the number of seconds to convert to a scorm time
 * @return string scorm 2004 formatted time
 */
function unixToScormTime($unixTime)
{
    // Scorm time format : P[yY][mM][dD][T[hH][mM][s[.s]S]]
    $y = $m = $d = $h = $min = $s = 0;
    
    // negative time is a not possible
    $unixTime = max(0, $unixTime);
    
    // Scorm uses a centiseconds precision but we ignore it in this function as
    // we will mainly used second precision in scripts
    $y = floor( $unixTime / 31557600 );
    $unixTime %= 31557600;

    $m = floor( $unixTime / 2629800 );
    $unixTime %= 2629800;
    
    $d = floor( $unixTime / 86400 );
    $unixTime %= 86400;

    $h = floor( $unixTime / 3600 );
    $unixTime %= 3600;
    
    $min = floor( $unixTime / 60 );
    $unixTime %= 60;
    
    $s = $unixTime;
    //$centiSeconds %= 100; // not required
    
    // build ScormTime string
    $scormTime = 'P' 
    . ( $y > 0  ? $y.'Y':'' )
    . ( $m > 0  ? $m.'M':'' )
    . ( $d > 0  ? $d.'D':'' );

    if( $h > 0 || $min > 0 || $s > 0 )
    {
        $scormTime .= 'T'
        . ( $h > 0  ? $h.'H':'' )
        . ( $min > 0  ? $min.'M':'' )
        . ( $s > 0  ? $s.'S':'' );
    } 

    if( $scormTime == 'P' )
    {
        return 'PT0H0M0S';
    }
    else
    {
        return $scormTime;
    }
}

function scormToUnixTime( $scormTime )
{
    list($days,$hours) = split('T', $scormTime);
    $days = str_replace('P', '', $days);
    $hours = str_replace('T', '', $hours);
    
    $year = substr( $days, 0, strpos($days, 'Y'));    
    if( $year )
    {
        $days = substr( $days, strpos( $days, 'Y') + 1, strlen( $days ));
    }
    
    $month = substr( $days, 0, strpos( $days, 'M'));
    if( $month )
    {
        $days = substr( $days, strpos( $days, 'M') + 1, strlen( $days ));
    }
    
    $day = substr($days, 0, strpos( $days, 'D'));
    
    $hour = substr( $hours, 0, strpos( $hours, 'H'));    
    if( $hour )
    {
        $hours = substr( $hours, strpos( $hours, 'H') + 1, strlen( $hours));        
    }
    
    $min = substr( $hours, 0, strpos( $hours, 'M'));
    if( $min )
    {
        $hours = substr( $hours, strpos( $hours, 'M') + 1, strlen( $hours));
    }
    
    $sec = substr( $hours, 0, strpos( $hours, 'S' ));
    
    $time = (int) $year * 31557600
        +   (int) $month * 2629800
        +   (int) $day * 86400
        +   (int) $hour * 3600
        +   (int) $min * 60
        +   (int) $sec;
    
    return $time;    
}

function unixToDHMS( $unixTime )
{
    $d = floor( $unixTime / (86400 ));
    $unixTime %= 86400;
    
    $h = floor( $unixTime / 3600 );
    $unixTime %= 3600;
    
    $min = floor( $unixTime / 60 );
    $unixTime %= 60;
    
    $s = $unixTime;
    
    return str_pad( $h, 2, 0, STR_PAD_LEFT)
    .   ':'
    .   str_pad( $min, 2, 0, STR_PAD_LEFT)
    .   ':'
    .   str_pad( $s, 2, 0, STR_PAD_LEFT);
}
?>