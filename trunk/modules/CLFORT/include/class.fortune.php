<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Fortune of the day server
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
     
    require_once dirname(__FILE__) . "/class.randomnumbergenerator.php";
    require_once dirname(__FILE__) . "/class.feeder.php";

    /**
     * Generate Bastard Operator From Hell server failure excuse
     *
     * @access public
     */
    class Fortune extends Feeder
    {
        var $fortunes;
        var $loadedFiles;
        var $separator;

        /**
         * Constructor
         * @access public
         * @param string excuseFilePath path to excuse file, optional
         *      if not given uses excuses file from the current directory
         */
        function Fortune( $srcFile = null, $separator = '%' )
        {
            $this->fortunes = array();
            $this->loadedFiles = array();
            $this->separator = $separator;

            $srcFile = ( ! empty( $srcFile ) ) 
                ? $srcFile
                : dirname(__FILE__) . "/commands.txt"
                ;
                
            /*if ( ! is_array( $srcFile ) )
            {
                $srcFile = array( $srcFile );
            }*/

            foreach ( $srcFile as $filePath )
            {
                $filePath = FORTUNE_DIRECTORY . '/' . $filePath;
                if ( !in_array($filePath, $this->loadedFiles) 
                    && file_exists( $filePath ) )
                {
                    // $filePath = realpath( $filePath );
                    $this->loadFile( $filePath );
                }
            }
        }

        function loadFile( $filePath )
        {
            $content = file_get_contents( $filePath );

            $fortunes = preg_split( '/'.$this->separator.'/', $content );

            $this->loadedFiles[] = $filePath;

            $this->fortunes = array_merge( $this->fortunes, $fortunes );
        }

        /**
         * Generate BOFH excuse
         * @access public
         */
        function generate()
        {
            $rng = new RandomNumberGenerator( true );
            $max = count( $this->fortunes ) - 1;
            
            $index = $rng->randomInteger( 0, $max );
            
            return trim( $this->fortunes[$index] );
        }
        
        /**
         * Test method
         * @access public
         * @static
         */
        function main( $filePathList = null )
        {
            if ( is_array( $filePathList )
                && !empty( $filePathList ) )
            {
                $bofh = new Fortune( $filePathList );
            }
            else
            {
                $bofh = new Fortune();
            }
            
            echo "<html>\n";
            echo "<head>\n";
            echo "<title>Fortune of the day</title>\n";
            echo "</head>\n";
            echo "<body>\n";
            echo "<p style=\"text-align: center;\"><span style=\"big\">";
            echo "<a href = \"".$_SERVER['PHP_SELF']."\">";
            echo "Fortune of the day server.";
            echo "</a></span></p>\n";
            echo "<hr />\n";
            echo "<h1>Fortune of the day:</h1>\n";
            echo "<pre style=\"text-align: left;font-size:100%; font-family: monospace\">\n";
            echo htmlentities( wordwrap( $bofh->generate(), 80 ) );
            echo "</pre>\n<hr />\n";
            echo "<p style=\"font-size:small;\">\n";
            echo "by Frederic Minne.</p>\n";
            echo "<p>download source code from <a href=\"fortunes.tar.gz\">here</a></p>";
            echo "</body>\n";
            echo "<html>\n";
        }
    }
?>
