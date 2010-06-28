<?php

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

$claroline->notification->addListener( 'cllp_path_created',      'modificationDefault' );
$claroline->notification->addListener( 'cll_path_visible',      'modificationDefault' );
$claroline->notification->addListener( 'cllp_path_invisible',    'modificationDelete' );
$claroline->notification->addListener( 'cllp_path_deleted',      'modificationDelete' );

$claroline->notification->addListener( 'cllp_resource_created',      'modificationDefault' );
$claroline->notification->addListener( 'cll_resource_visible',      'modificationDefault' );
$claroline->notification->addListener( 'cllp_resource_invisible',    'modificationDelete' );
$claroline->notification->addListener( 'cllp_resource_deleted',      'modificationDelete' );

?>