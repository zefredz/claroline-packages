<?php 

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package ICEPC
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

function epc_get_current_acad_year()
{
    $currentMonth = (int) date ( 'n' );
    
    if ( $currentMonth < 9 )
    {
        return (int) date ( 'Y' ) - 1;
    }
    else
    {
        if ( claro_debug_mode() )
        {
            return (int) date ( 'Y' ) - 1;
        }
        else
        {
            return date ( 'Y' );
        }
    }
}

function epc_add_userlist_helper( $epcSearchFor, $epcAcadYear, $epcSearchString )
{
    $epcService = new EpcStudentListService (
            get_conf ( 'epcServiceUrl' ),
            get_conf ( 'epcServiceUser' ),
            get_conf ( 'epcServicePassword' )
    );
    
    if ( 'course' == $epcSearchFor )
    {
        $users = $epcService->getStudentsInCourse ( $epcAcadYear, $epcSearchString ); // LBIO1111A' );
    }
    else
    {
        $users = $epcService->getStudentsInProgram ( $epcAcadYear, $epcSearchString );
    }
    
    if ( !empty ( $users ) )
    {
    
        $platformUserList = new Claro_PlatformUserList();
        $platformUserList->registerUserList($users->getIterator());

        $epcClass = new EpcClass("epc_{$epcSearchFor}{$epcAcadYear}{$epcSearchString}");

        if ( !$epcClass->associatedClassExists() )
        {
            $epcClass->createAssociatedClass();
        }
       
        $claroClass = $epcClass->getAssociatedClass();
        
        class_add_userlist_helper( $platformUserList->getValidUserIdList () , $claroClass, true );
    }
}
