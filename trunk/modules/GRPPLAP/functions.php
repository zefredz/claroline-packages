<?php

if ( count( get_included_files() ) == 1 ) die( '---' );

if ( claro_is_in_a_course() )
{
    require_once( get_module_path( 'GRPPLAP' ) . '/lib/grapple.listener.class.php' );
    require_once( get_module_path( 'GRAPPLE' ) . '/lib/grapple.class.php' );
    
    $grappleListener = new grappleListener();
    
    $grappleListener->addListener( 'user_login', 'userLogin' );
    
    $grappleListener->addListener( 'user_added_to_course', 'studentEnrollment' );
    $grappleListener->addListener( 'user_role_change', 'roleChange' );
    
    $grappleListener->addListener( 'user_created', 'userRegistration' );
}