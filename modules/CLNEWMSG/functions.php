<?php // $Id$

if ( ! claro_is_user_authenticated() )
{
    $_SESSION[ 'start_time' ] = null;
}
elseif ( ! isset( $_SESSION[ 'start_time' ] ) || ! $_SESSION[ 'start_time' ] )
{
    $_SESSION[ 'start_time' ] = time();
}

