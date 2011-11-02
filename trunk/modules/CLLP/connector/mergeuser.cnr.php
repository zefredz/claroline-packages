<?php // $Id$

/**
 * Connector used to merge CLLP data from 2 users
 *
 * @version 1.9 $Revision$
 * @copyright (c) 2001-2008 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author Dimitri Rambout <dim@claroline.net>
 * @package MergeUser
 */
class CLLP_MergeUser implements Module_MergeUser
{
    /**
     * Merge the data from 2 users in a course
     *
     * @param int $uidToRemove User id that need to be removed
     * @param int $uidToKeep User id that need to be kept
     * @param int $courseId Course id concerned by the merging
     */
    public function mergeCourseUsers( $uidToRemove, $uidToKeep, $courseId )
    {
        $tblList[] = 'lp_path';
        $tblList[] = 'lp_item';
        $tblList[] = 'lp_attempt';
        $tblList[] = 'lp_item_attempt';
        $tblList[] = 'lp_item_blockcondition';
        
        $moduleCourseTbl = get_module_course_tbl( $tblList, $courseId );
        
        // Update lp_attempt
        // TODO: check if we can have multiple attempt for item per user
        $sql = "UPDATE `{$moduleCourseTbl['lp_attempt']}`
                SET   user_id = ".(int)$uidToKeep."
                WHERE user_id = ".(int)$uidToRemove;

        if ( ! claro_sql_query($sql) )
        {
            throw new Exception("Cannot update lp_attempt in {$courseId}");
        }
        
    }
    
    public function mergeUsers( $uidToRemove, $uidToKeep )
    {
        // empty
    }
}

