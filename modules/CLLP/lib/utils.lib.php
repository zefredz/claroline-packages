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
?>