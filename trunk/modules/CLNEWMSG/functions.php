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

if ( ! claro_is_user_authenticated() )
{
    $_SESSION[ 'start_time' ] = null;
}
elseif ( ! isset( $_SESSION[ 'start_time' ] ) || ! $_SESSION[ 'start_time' ] )
{
    $_SESSION[ 'start_time' ] = time();
}

CssLoader::getInstance()->load( 'bubble' , 'screen' );

if ( claro_is_user_authenticated() )
{
    ClaroHeader::getInstance()->addHtmlHeader( '
        <script type="text/javascript">
            function msgNotifier()
            {
                $.ajax({
                    url: "' . get_module_url( 'CLNEWMSG' ) . '/index.php",
                    success: function(data){
                        $("#newMsg").html(data);
                    }
                });
                setTimeout( msgNotifier, '. get_conf( 'refreshTime' ) * 1000 .' );
            }
            $( function(){ msgNotifier(); } );
        </script>');
}