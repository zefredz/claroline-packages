<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * @version 1.0.0
 *
 * @version 1.8 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLOOVOO
 *
 * @author Wanjee <wanjee.be@gmail.com>
 *
 */

class OovooLink
{
    private $id;
    private $course_id;
    private $username;
    
    private $template;
    private $theme;
    
    
    private $tbl;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $course_id string course code 
     */ 
    public function __construct($course_id = null)
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
        $this->username = '';
        
        $this->template = get_conf('oovoo_template');
        $this->theme = get_conf('oovoo_theme');
        
        // table
        $module_tbl_names = array('oovoo_course');
        $module_tbl_names = get_module_main_tbl($module_tbl_names);
        
        $this->tbl = $module_tbl_names['oovoo_course'];
    }
    
    /**
     * Load data if any
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean is loading successfull ?
     */ 
    public function load()
    {        
        $sql = "SELECT `id`,
                    `username`
            FROM `".$this->tbl."`
            WHERE `course_id` = '".addslashes($this->course_id)."'";

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            $this->id = $data['id'];
            $this->username = $data['username'];

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
    public function save()
    {
        if( $this->id == -1 )
        {
            // insert
            $sql = "INSERT INTO `".$this->tbl."`
                    SET `course_id` = '".addslashes($this->course_id)."',
                        `username` = '".addslashes($this->username)."'";

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
            $sql = "UPDATE `".$this->tbl."`
                    SET `course_id` = '".addslashes($this->course_id)."',
                        `username` = '".addslashes($this->username)."'
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
    public function delete()
    {
        $sql = "DELETE FROM `".$this->tbl."`
                WHERE `id` = " . (int) $this->id;
                
        if( claro_sql_query($sql) == false ) return false;
        
        $this->id = -1;
        $this->username = '';
        return true;
    }
    
    /**
     * Provide html output required to display status notifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string html code
     */
    public function render()
    {
        $html = '';

        if( $this->course_id != -1 )
        {
            if( !empty($this->username) )
            {
                $html .= '<span id="oovooContainer">';
                
                switch( $this->template )
                {
                    case '1' :
                        // template 1 is full
                        $html .= '<img src="http://www.oovoo.com/images/template/Full/ooVooMe/theme'.$this->theme.'/True/'.$this->username.'.gif" alt="" usemap="#map" border="0" >'
                        . '<map name="map">'
                        . '<area shape="rect" coords="10,87,98,112" href="oovoo:?call?'.$this->username.'">'
                        . '<area shape="rect" coords="10,116,98,140" href="oovoo:?add?'.$this->username.'">'
                        . '<area shape="rect" coords="27,143,80,158" href="http://www.oovoo.com/download" target="_blank">'
                        . '</map>';
                        break;
                    case '2' :
                        $html .= '<img src="http://www.oovoo.com/images/template/Advance/ooVooMe/theme'.$this->theme.'/True/'.$this->username.'.gif" alt="" usemap="#map" border="0" >'
                        . '<map name="map">'
                        . '<area shape="rect" coords="15,86,93,110" href="oovoo:?call?'.$this->username.'">'
                        . '<area shape="rect" coords="5,127,103,156" href="http://www.oovoo.com/download" target="_blank">'
                        . '</map>';
                        break;
                    case '3' :
                        $html .= '<img src="http://www.oovoo.com/images/template/Simple/ooVooMe/theme'.$this->theme.'/True/'.$this->username.'.gif" alt="" usemap="#map" border="0" >'
                        . '<map name="map">'
                        . '<area shape="rect" coords="47,13,120,37" href="oovoo:?call?'.$this->username.'">'
                        . '<area shape="rect" coords="21,46,120,76" href="http://www.oovoo.com/download" target="_blank">'
                        . '</map>';
                        break;
                    case '4' :
                        $html .= '<img src="http://www.oovoo.com/images/template/Base/ooVooMe/theme'.$this->theme.'/True/'.$this->username.'.gif" alt="" usemap="#map" border="0" >'
                        . '<map name="map">'
                        . '<area shape="rect" coords="17,2,92,26" href="oovoo:?call?'.$this->username.'">'
                        . '<area shape="rect" coords="7,30,104,60" href="http://www.oovoo.com/download" target="_blank">'
                        . '</map>';
                        break;
                    default :
                        $html .= '<span id="oovoo">'
                        . '<a href="oovoo:?call?'.htmlspecialchars($this->username).'" title="'.get_lang('Call %username', array('%username' => htmlspecialchars($this->username))).'">'
						. '<img src="'.get_module_url('CLOOVOO').'/img/oovoo.gif" alt="" style="vertical-align: text-bottom;" />' 
						. '&nbsp;'
                        . get_lang('Call course manager').'</a>'
                        . '</span>';
                        break;
                }
                
                
                if( claro_is_allowed_to_edit() )
                {
                    $html .= ' <a href="'.get_module_url('CLOOVOO').'/edit.php"><img src="'.get_icon_url('edit').'" alt="'.get_lang('Modify username').'" /></a>' . "\n";
                }
                $html .= '</span>' . "\n\n";
                
            }
            elseif( claro_is_allowed_to_edit() )
            {
                $html .= '<span id="oovooContainer">'
                . '<a href="'.get_module_url('CLOOVOO').'/edit.php" >'
                . '<img src="'.get_module_url('CLOOVOO').'/icon.gif" alt="" align="top" /> '
                . get_lang('Configure your ooVoo link')
                . '</a>' . "\n"
                . '</span>' . "\n";
            }
        }

        return $html;
    }
    
    
    // getter and setter
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($value)
    {
        $this->username = trim($value);
    }

}

?>
