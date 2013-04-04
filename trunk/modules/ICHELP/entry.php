<?php
if( ! isset( $GLOBALS[ 'tlabelReq' ] ) || $GLOBALS[ 'tlabelReq' ] != 'ICHELP' )
{
    $claro_buffer->append( '<a href="'. claro_htmlspecialchars( Url::Contextualize( get_module_url( 'ICHELP' ) . '/controller.php?from=' . base64_encode( $_SERVER[ 'REQUEST_URI' ] ) ) ) . '"><img src="' . get_icon_url( 'big' ) . '" /></a>' );
}
?>