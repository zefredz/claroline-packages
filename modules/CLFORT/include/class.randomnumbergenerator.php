<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Random number generator
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

    /**
     * Generate random integers
     *
     * @access public
     */
    class RandomNumberGenerator
    {
        var $use_mt_rand;
        var $seed;
        
        /**
         * Constructor
         * @access public
         * @param boolean use_mt_rand use mt_rand instead of rand
         * @param float seed random number generator seed
         */
        function RandomNumberGenerator( $use_mt_rand = false, $seed = false )
        {
            if ( $seed === false )
            {
                $seed = $this->makeSeed();
            }
            
            $this->seed = $seed;

            $this->use_mt_rand = ( $use_mt_rand === true ) ? true : false;
            
            $this->init();
        }
        
        function init()
        {
            if( $this->use_mt_rand === true )
            {
                mt_srand( $this->seed );
            }
            else
            {
                srand( $this->seed );
            }
        }

        /**
         * Generate a float number based on microtime
         * @access public
         * @return float seed
         */
        function makeSeed()
        {
            list($usec, $sec) = explode(' ', microtime());
            return (float) $sec + ((float) $usec * 100000);
        }

        /**
         * Generate a random integer
         * @access public
         * @param int min optional minimal value
         * @param int max optional maximal value
         * @return int random integer
         */
        function randomInteger( $min = false, $max = false )
        {
            $randFunc = ( $this->use_mt_rand ) ? 'mt_rand' : 'rand';
            
            if( $min === false && $max === false )
            {
                return $randFunc();
            }
            elseif( is_int( $min ) && is_int( $max ) && $min < $max )
            {
                return $randFunc( $min, $max );
            }
            else
            {
                trigger_error( "Invalid arguments supplied to "
                    . __CLASS__ . "->" . __FUNCTION__
                    , E_USER_ERROR
                    );
            }
        }
    }
?>
