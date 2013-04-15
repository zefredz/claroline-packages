<?php

if ( claro_is_in_a_course() )
{
    require_once dirname(__FILE__) . '/lib/upgrade.lib.php';
    require_once dirname(__FILE__) . '/lib/upgradetasks.lib.php';
    require_once dirname(__FILE__) . '/lib/registry.lib.php';
    
    try
    {
        if ( PersistantVariableStorage::module('UPGTOOL')->get('upgrade.main.done') == true
            && PersistantVariableStorage::module('UPGTOOL')->get('upgrade.course.auto') == true )
        {
            try
            {
                $course = Upgrade_CourseDatabase::getCourse( claro_get_current_course_id() );
                
                if ( $course )
                {
                    if ( $course['status'] == 'pending' )
                    {
                        $errorSteps = Upgrade_Course::execute( $course );
                        
                        if ( ! count( $errorSteps ) )
                        {
                            Console::success( "UPGTOOL::Upgrade successful for ".claro_get_current_course_id() );
                        }
                        else
                        {
                            Console::warning( "UPGTOOL::Upgrade failed for ".claro_get_current_course_id() . " at steps " . implode( ',', $errorSteps ) );
                        }
                    }
                    else
                    {
                        pushClaroMessage( "UPGTOOL::Upgrade already done for ".claro_get_current_course_id() . " with status " . $course['status'], 'info' );
                    }
                }
                else
                {
                    pushClaroMessage("The course " . htmsplecialchars($cid) . " does not need to be upgraded", 'info' );
                }
            }
            catch (Exception $e )
            {
                Console::error( "UPGTOOL::Exception in ".claro_get_current_course_id()." : {$e->getMessage()}" );
            }
        }
    }
    catch (Exception $e)
    {
        Console::error( $e->__toString() );
    }
}
