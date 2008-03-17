<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.8 $Revision$
     * @copyright   2001-2008 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    function iso8601_date( $time = null )
    {
        if ( is_null( $time ) ) $time = time();
        
        return ( date('c') == 'c' 
            ? date('Y-m-d\TH:i:sO',$time) 
            : date('c', $time ) );
    }
    
    function iso8601_from_date( $date = null )
    {
        $time = is_null( $date )
            ? time()
            : strtotime( $date )
            ;
        
        return ( date('c') == 'c' 
            ? date('Y-m-d\TH:i:sO',$time) 
            : date('c', $time ) );
    }
    
    function datetime( $time = null )
    {
        if ( $time )
        {
            return date( "Y-m-d H:i:s", $time );
        }
        else
        {
            return date( "Y-m-d H:i:s" );
        }
    }
    
    function dateToDatetime( $date )
    {
        return date( "Y-m-d H:i:s", strtotime( $date ) );
    }
?>