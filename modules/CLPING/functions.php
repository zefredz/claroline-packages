<?php // $Id$

/**
 * Ping
 *
 * @version     CLPING 1.0 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

if ( claro_is_user_authenticated() )
{
    ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        function ping(){
            $.ajax( {
                url: "' . get_module_url( 'CLPING' ) . '/ping.php",
                success: function( data ){
                }
            } );
            setTimeout( ping , ' . 30 * 1000 . ' );
        }
        $( function(){ ping(); } );
    </script>' );
}
