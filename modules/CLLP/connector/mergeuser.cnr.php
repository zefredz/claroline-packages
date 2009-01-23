<?php

class CLLP_MergeUser implements Module_MergeUser
{
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
            throw new Exception("Cannot update lp_attempt in {$thisCourseCode}");
        }
        
    }
    
    public function mergeUsers( $uidToRemove, $uidToKeep )
    {
        // empty
    }
}

