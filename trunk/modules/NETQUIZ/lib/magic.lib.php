<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Magic number functions
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     Kernel
     */

    function is_zip_file( $filePath )
    {
        $magic = get_magic_number( $filePath, 2 );

        return ($magic == "\x50\x4b");
    }

    function get_magic_number( $filePath, $bytes )
    {
        $handle = fopen( $filePath, 'r' );
        $magic = fgets( $handle, $bytes + 1 );
        fclose( $handle );

        return $magic;
    }
?>