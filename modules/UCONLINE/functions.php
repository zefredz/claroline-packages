<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 0.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository().'UCONLINE.conf.php';
include_once dirname(__FILE__) . '/lib/login.listener.class.php';

ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        function localTime(){
            localTime = new Date();
            timeOffset = localTime.getTimezoneOffset();
            document.cookie = "time_offset = " + timeOffset + ";path = /"
        }
        $( function(){ localTime(); } );
    </script>');

$claroLoginListener = new LoginListener;

if( claro_is_user_authenticated() ) $claroLoginListener->insert_login_online();
$claroLoginListener->refresh_login_DB();
$claroLoginListener->addListener( "user_login", 'insert_login_online' );
$claroLoginListener->addListener( "user_logout", 'delete_login_online' );