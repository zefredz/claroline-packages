<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . __FILE__ . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Output Buffering Class
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     Output
     */

    class Output_Buffer
    {
        var $contents = '';
        
        function send()
        {
            echo $this->contents;
        }
        
        function clean()
        {
            $this->contents = '';
        }
        
        function flush()
        {
            $this->send();
            // force I/O flush
            flush();
            $this->clean();
        }
        
        function append( $str )
        {
            $this->contents .= $str;
        }
        
        function replace( $str )
        {
            $this->contents = $str;
        }
        
        function getContents()
        {
            return $this->contents;
        }
    }
?>