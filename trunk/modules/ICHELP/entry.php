<?php
if( ! isset( $GLOBALS[ 'tlabelReq' ] ) || $GLOBALS[ 'tlabelReq' ] != 'ICHELP' )
{
    $claro_buffer->append( '<a style="font-weight: bold; color: #336699;" href="'. claro_htmlspecialchars( Url::Contextualize( get_module_url( 'ICHELP' ) . '/controller.php?from=' . base64_encode( $_SERVER[ 'REQUEST_URI' ] ) ) ) . '">Contacter le helpdesk iCampus</a>' );
}
?>