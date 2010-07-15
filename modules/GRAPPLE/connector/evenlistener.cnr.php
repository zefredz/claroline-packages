<?php

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

$claroline->notification->addListener( 'grapple_path_created',      'modificationDefault' );
$claroline->notification->addListener( 'grapple_path_visible',      'modificationDefault' );
$claroline->notification->addListener( 'grapple_path_invisible',    'modificationDelete' );
$claroline->notification->addListener( 'grapple_path_deleted',      'modificationDelete' );

$claroline->notification->addListener( 'grapple_resource_created',      'modificationDefault' );
$claroline->notification->addListener( 'grapple_resource_visible',      'modificationDefault' );
$claroline->notification->addListener( 'grapple_resource_invisible',    'modificationDelete' );
$claroline->notification->addListener( 'grapple_resource_deleted',      'modificationDelete' );

?>