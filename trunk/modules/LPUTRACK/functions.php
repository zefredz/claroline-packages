<?php

    if ( count( get_included_files() ) == 1 ) die( '---' );

    require_once( get_module_path( 'LPUTRACK' ) . '/lib/learnPathTracking.listener.class.php' );

    $learnPathTrackingListener = new LearnPathTrackingListener();
    $learnPathTrackingListener->addListener( 'lp_user_module_progress_modified', 'updateModuleTracking' );
    $learnPathTrackingListener->addListener( 'lp_document_time_update', 'updateDocumentTrackingTime' );
