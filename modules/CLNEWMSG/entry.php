<?php // $Id$

/**
 * New message notifier
 *
 * @version     CLNEWMSG 0.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLNEWMSG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CssLoader::getInstance()->load( 'bubble' , 'screen' );

if ( claro_is_user_authenticated() )
{
    ClaroHeader::getInstance()->addHtmlHeader( '
        <script type="text/javascript">
            function msgNotifier()
            {
                $.getJSON(
                    "' . get_module_url( 'CLNEWMSG' ) . '/index.php",
                    function( response ) {
                        if ( response.responseType && response.responseType == "success" && response.responseBody.newMsg > 0 ) { 
                            $("#newMsg").html(response.responseBody.contents);
                        }
                        else {
                            $("#newMsg").empty();
                        }
                    }
                );
                setTimeout( msgNotifier, '. get_conf( 'refreshTime' ) * 1000 .' );
            }
            $( function(){ msgNotifier(); } );
        </script>');
}

$claro_buffer->append( '<div id="newMsg"></div>' );

