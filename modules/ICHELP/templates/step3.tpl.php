<?php
if( $this->errorStatus )
{
    echo claro_html_button( claro_htmlspecialchars( $_SERVER[ 'PHP_SELF' ] ) , get_lang( 'OK' ) );
}
else
{
    echo claro_html_button( claro_htmlspecialchars( $this->backUrl ) , get_lang( 'OK' ) );
}