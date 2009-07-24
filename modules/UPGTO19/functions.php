<?php

if ( claro_is_in_a_course() )
{
    require_once dirname(__FILE__) . '/lib/upgrade.lib.php';
    require_once dirname(__FILE__) . '/lib/upgradetasks.lib.php';
    require_once dirname(__FILE__) . '/lib/registry.lib.php';
    
    try
    {
        if ( PersistantVariableStorage::module('UPGTO19')->get('upgrade.main.done') == true
            && PersistantVariableStorage::module('UPGTO19')->get('upgrade.course.auto') == true )
        {
            try
            {
                $errorSteps = Upgrade_Course::execute(claro_get_current_course_id());
                
                if ( ! count( $errorSteps ) )
                {
                    Console::success( "UPGTO19::Upgrade successful for ".claro_get_current_course_id() );
                }
                else
                {
                    Console::warning( "UPGTO19::Upgrade failed for ".claro_get_current_course_id() . " at steps " . implode( ',', $errorSteps ) );
                }
            }
            catch (Exception $e )
            {
                Console::error( "UPGTO19::Exception in ".claro_get_current_course_id()." : {$e->getMessage()}" );
            }
        }
    }
    catch (Exception $e)
    {
        Console::error( $e->__toString() );
    }
}
