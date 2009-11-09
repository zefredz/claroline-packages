<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 1.2.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository().'UCONLINE.conf.php';
include_once dirname( __FILE__ ) . '/lib/login.listener.class.php';

JavascriptLoader::getInstance()->load( 'datetime' );

ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        function userOnline(){
            $.ajax( {
                url: "' . get_module_url( 'UCONLINE' ) . '/user_online.php",
                success: function( data ){
                    $( "#userOnline" ).html( data );
                }
            } );
            setTimeout( userOnline , ' . get_conf( 'UCONLINE_displayRefreshTime' ) * 1000 . ' );
        }
        function userTime(){
            var userDate = new Date();
            var serverDate = Date.fromDatetime( "'. date( "Y-m-d H:i:s" ) .'");
            var timeOffset = Math.round( ( userDate.getTime() - serverDate.getTime() ) / 1000 );
            document.cookie = "time_offset = " + timeOffset + ";path = /"
        }
        $( function(){ userOnline(); } );
        $( function(){ userTime(); } );
    </script>' );

$claroLoginListener = new LoginListener;

if( claro_is_user_authenticated() ) $claroLoginListener->insert_login_online();
$claroLoginListener->refresh_login_DB();
$claroLoginListener->addListener( "user_login", 'insert_login_online' );
$claroLoginListener->addListener( "user_logout", 'delete_login_online' );