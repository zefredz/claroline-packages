<?php // $Id$
/**
* Server Time?
*
* @version     UCSTIME 1.1.1 $Revision$ - Claroline 1.9
* @copyright   2001-2009 Universite Catholique de Louvain (UCL)
* @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
* @package     UCSTIME
* @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
*/

if ( count( get_included_files() ) == 1 ) die( '---' );

$seconds_displayed = get_conf( 'displaySeconds' ) ? 'HH:mm:ss' : 'HH:mm';

JavascriptLoader::getInstance()->load( 'datetime' );
JavascriptLoader::getInstance()->load( 'Date' );

ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        var timeShift;
        function serverTimeShift(){
            var _localDate = new Date();
            var _serverDate = Date.fromDatetime( "'. date( "Y-m-d H:i:s" ) .'" );
            timeShift = _localDate.getTime() - _serverDate.getTime();
        }
        function serverTimeDisplay(){
            var localDate = new Date();
            var serverDate = new Date();
            serverDate.setTime( localDate.getTime() - timeShift );
            $( "#serverTime" ).html( serverDate.format( "' . $seconds_displayed .'" ) );
            setTimeout( serverTimeDisplay , 1000 );
        }
        $(function()
        {
            serverTimeShift();
            serverTimeDisplay();
        });
    </script>');