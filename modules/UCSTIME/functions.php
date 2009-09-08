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


ClaroHeader::getInstance()->addHtmlHeader( '
    <script type="text/javascript">
        function serverTimeShift()
        {
            localTime = new Date();
            timeShift = localTime.getTime() - ' . time() .'*1000;
            setTimeout( serverTimeShift , 360000 );
        }
        function serverTimeDisplay()
        {
            localTime = new Date();
            serverTime = new Date ( localTime.getTime() + timeShift );
            $( "#serverTime" ).html( serverTime.getHours() + ":" + serverTime.getMinutes()) ;
            setTimeout( serverTimeDisplay , 1000 );
        }
        $(function()
        {
            serverTimeShift();
            serverTimeDisplay();
        });
    </script>');