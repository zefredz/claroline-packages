<?php

if( $_SERVER['PHP_SELF'] != get_module_url( 'ICHELP' ) . '/controller.php' )
{
    unset( $_SESSION[ 'ICHELP_data' ] );
}