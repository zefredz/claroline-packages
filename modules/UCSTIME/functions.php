<?php // $Id$

/**
 * Server Time?
 *
 * @version     UCSTIME 0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCSTIME
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$seconds_displayed = get_conf( 'displaySeconds' ) ? 8 : 5;

ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        function serverTimeShift()
        {
            localTime = new Date();
            timeShift = localTime.getTime() - ' . time() .'*1000;
            setTimeout( serverTimeShift , ' . get_conf( 'refreshTime' ) . '*60000 );
        }
        function serverTimeDisplay()
        {
            localTime = new Date();
            serverTime = new Date ( localTime.getTime() - timeShift );
            $( "#serverTime" ).html( serverTime.toString().substr( 16 , ' . $seconds_displayed . ' ) );
            setTimeout( serverTimeDisplay , 1000 );
        }
        $(function()
        {
            serverTimeShift();
            serverTimeDisplay();
        });
    </script>');