<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * String feeder
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
     * @package misc.utils
     */

    /**
     * Feeder
     *
     * Abstract string generator to feed scripts, generate random
     * web site content...
     *
     * @abstract
     * @access public
     */
    class Feeder
    {
        /**
         * Generate a string
         * @abstract
         * @return string
         */
        function generate()
        {
            trigger_error( "Call to undefined abstract method in "
                . __CLASS__ . "->" . __FUNCTION__
                , E_USER_ERROR
                );
        }
    }
?>
