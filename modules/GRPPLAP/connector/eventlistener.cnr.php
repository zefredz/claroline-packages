<?php // $Id$

require_once( get_module_path( 'GRPPLAP' ) . '/lib/grapple.listener.class.php' );

$grappleListener = new grappleListener();

$grappleListener->addListener( 'user_login', 'userLogin' );

?>