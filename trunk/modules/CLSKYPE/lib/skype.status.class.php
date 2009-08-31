<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claro Team <cvs@claroline.net>
 *
 */

class SkypeStatus
{
    var $id;
    var $course_id;    
    var $skype_name;
    
    var $tblSkypeCourse;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $course_id string course code 
     */ 
    function SkypeStatus($course_id = null)
    {
        // id
        $this->id = (int) -1;
        // course_id
        if( $course_id == false )
        {
            $this->course_id = null;
        }
        else
        {
            $this->course_id = $course_id;
        }
        // name
        $this->skype_name = '';
        
        // table
        $module_tbl_names = array('skype_course');
        $module_tbl_names = get_module_main_tbl($module_tbl_names);
        
        $this->tblSkypeCourse = $module_tbl_names['skype_course'];
    }
    
    /**
     * Load data if any
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean is loading successfull ?
     */ 
    function load()
    {        
        $sql = "SELECT `id`,
                    `skype_name`
            FROM `".$this->tblSkypeCourse."`
            WHERE `course_id` = '".addslashes($this->course_id)."'";

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            $this->id = $data['id'];
            $this->skype_name = $data['skype_name'];

            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Save data
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or new item id
     */ 
    function save()
    {
        if( $this->id == -1 )
        {
            // insert
            $sql = "INSERT INTO `".$this->tblSkypeCourse."`
                    SET `course_id` = '".addslashes($this->course_id)."',
                        `skype_name` = '".addslashes($this->skype_name)."'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->id = (int) $insertedId;

                return $this->id;
            }
            else
            {
                return false;
            }
        }
        else
        {
            // update, main query
            $sql = "UPDATE `".$this->tblSkypeCourse."`
                    SET `course_id` = '".addslashes($this->course_id)."',
                        `skype_name` = '".addslashes($this->skype_name)."'
                    WHERE `id` = '".$this->id."'";

            // execute and return main query
            if( claro_sql_query($sql) )
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }
    
    /**
     * Delete row
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean result of delete query
     */
    function delete()
    {
        $sql = "DELETE FROM `".$this->tblSkypeCourse."`
                WHERE `id` = " . (int) $this->id;
                
        if( claro_sql_query($sql) == false ) return false;
        
        $this->id = -1;
        $this->skype_name = '';
        return true;        
    }
    
    /**
     * Provide html output required to display status notifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string html code
     */
    function output()
    {
        $html = '';

        if( $this->course_id != -1 )
        {
            if( !empty($this->skype_name) )
            {
                $html .= "\n\n"
                .    '<span id="skypeStatus">' . "\n"
                .    '<!-- Skype "My status" button http://www.skype.com/go/skypebuttons -->' . "\n"
                .    '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>' . "\n"
                .    '<a href="skype:'.$this->skype_name.'?call"><img src="http://mystatus.skype.com/smallclassic/'.$this->skype_name.'"'
                .    ' style="border: none;" width="114" height="20" alt="'.get_lang('Skype status of course administrator').'" /></a>' . "\n";
                
                if( claro_is_allowed_to_edit() )
                {
                    $html .= '<a href="'.get_module_url('CLSKYPE').'/edit.php"><img src="'.get_icon_url('edit').'" alt="'.get_lang('Modify').'" /></a>' . "\n";
                }
                
                $html .= '</span>' . "\n\n";             
            }
            elseif( claro_is_allowed_to_edit() )
            {
                $html .= '<a href="'.get_module_url('CLSKYPE').'/edit.php" >'
                .    '<img src="'.get_module_url('CLSKYPE').'/icon.png" alt="" align="top" />'
                .    get_lang('Configure Skype status notifier')
                .    '</a>' . "\n";
            }
        }

        return $html;
    }
    
    
    // getter and setter
    function getSkypeName()
    {
        return $this->skype_name;
    }
    
    function setSkypeName($value)
    {
        $this->skype_name = trim($value);
    }

}

?>
