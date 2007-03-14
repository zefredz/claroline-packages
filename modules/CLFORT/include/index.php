<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * Fortune of the day server main script
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
     * @depends class.feeder.php
     * @depends class.randomnumbergenerator.php
     *
     * @package misc.utils.fun
     */
     
    require_once "class.fortune.php";
    

    $fortuneDirectory = dirname(__FILE__) . "/fortune-files";

    $files = array ();

    if ($handle = opendir( $fortuneDirectory ))
    {
        while (false !== ($file = readdir($handle)))
        {
            if ( $file != '.' && $file != '..' && !is_dir( $file ) )
            {
                $files[] = realpath( $fortuneDirectory . "/" . $file );
            }
        }

        closedir($handle);
    }
    else
    {
        echo "<h1>Directory $fortuneDirectory not found!</h1>";
    }
    
    Fortune::main( $files );
?>
