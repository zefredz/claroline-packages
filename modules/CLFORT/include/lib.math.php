<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * Random number generator - Mathematic Functions Library
     *
     * @version 1.0
     *     
     * @copyright 2004-2005 Frederic Minne
     * This program is under the terms of the GNU GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @license http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE (GPL)
     *
     * @author Frederic Minne <zefredz@gmail.com>
     *
     * @package math.utils
     */
    
    function make_seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

    function get_random_integer( $min = null, $max = null, $seed = null )
    {
        $seed = ( is_null( $seed ) ) ? make_seed() : $seed;
        
        srand( $seed );
        
        if( is_int( $min ) && is_int( $max ) && $min < $max )
        {
            return rand( $min, $max );
        }
        elseif( is_null( $min ) && is_null( $max ) )
        {
            return rand();
        }
        else
        {
            trigger_error( "Invalid arguments supplied to " . __FUNCTION__
                , E_USER_ERROR
                );
        }
    }
    
    function get_next_index( $current, $min, $max )
    {
        $candidate = $current + 1;
        
        $range = $max - $min;
        
        while( $candidate > $max )
        {
            $candidate = $candidate - $range;
        }
        
        return $candidate;
    }
    
    function get_previous_index( $current, $min, $max )
    {
        $candidate = $current - 1;
        
        $range = $max - $min;
        
        while( $candidate < $min )
        {
            $candidate = $candidate + $range;
        }
        
        return $candidate;
    }
?>
