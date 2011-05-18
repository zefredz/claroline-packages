<?php // $Id$

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

