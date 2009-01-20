<?php

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

try
{
    $tbl = get_module_main_tbl(array('icprint_actions','icprint_documents'));
    
    // Console::debug( var_export( $courseId, true ) );

    $sql = "UPDATE `{$tbl['icprint_actions']}`
    SET action_name = 'coursedelete',
    action_timestamp = NOW()
    WHERE action_course_id = " . Claroline::getDatabase()->quote($courseId);
    
    Console::debug( $sql );
    
    Claroline::getDatabase()->exec( $sql );
    
    $sql = "DELETE FROM `{$tbl['icprint_documents']}`
    WHERE document_course_id = " . Claroline::getDatabase()->quote($courseId);
    
    Console::debug( $sql );
    
    Claroline::getDatabase()->exec( $sql );
}
catch ( Exception $e )
{
    Console::error('ICPRINT : Cannot change action when deleting course in '
                   . var_export( Claro_Context::getCurrentContext(), true )
                   . ' : ' . $e->__toString()  );
}
