<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     PACKAGE_NAME
 */

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

FromKernel::uses('utils/time.lib');


class PrintServiceAction
{    
    public $id;
    public $documentId;
    public $courseId;
    public $userId;
    public $action;
    public $timestamp;
    
    public static function actionAdd( $doc )
    {
        $action = new PrintServiceAction;
        $action->action = 'add';
        $action->userId = claro_get_current_user_id();
        $action->courseId = claro_get_current_course_id();
        $action->timestamp = Claro_Utils_Time::timeToDatetime();
        $action->documentId = $doc->id;
        $action->documentHash = $doc->hash;
        $action->documentLocalPath = $doc->localPath;
        
        $action->document = $doc;
        
        return $action;
    }
    
    public static function actionDelete( $doc )
    {
        $action = new PrintServiceAction;
        $action->action = 'delete';
        $action->userId = claro_get_current_user_id();
        $action->courseId = claro_get_current_course_id();
        $action->timestamp = Claro_Utils_Time::timeToDatetime();
        $action->documentId = $doc->id;
        $action->documentHash = $doc->hash;
        $action->documentLocalPath = $doc->localPath;
        
        $action->document = $doc;
        
        return $action;
    }
    
    public static function actionModify( $doc )
    {
        $action = new PrintServiceAction;
        $action->action = 'modify';
        $action->userId = claro_get_current_user_id();
        $action->courseId = claro_get_current_course_id();
        $action->timestamp = Claro_Utils_Time::timeToDatetime();
        $action->documentId = $doc->id;
        $action->documentHash = $doc->hash;
        $action->documentLocalPath = $doc->localPath;
        
        $action->document = $doc;
        
        return $action;
    }
}
