<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
* CLAROLINE
*
* FIXME : move to annoucements module
*
* @version      1.9 $Revision$
* @copyright    (c) 2001-2008 Universite catholique de Louvain (UCL)
* @license      http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
* @package      
* @author       Claroline team <info@claroline.net>
*
*/

require_once get_module_path( 'CLDSC' ) . '/lib/courseDescription.lib.php';
require_once get_module_path( 'CLDSC' ) . '/lib/courseDescription.class.php';

uses('courselist.lib');

class CLDSC_Portlet extends Portlet
{
    public function __construct()
    {
        if (file_exists(claro_get_conf_repository() . 'CLDSC.conf.php'))
        {
            include claro_get_conf_repository() . 'CLDSC.conf.php';
        }
    }

    public function renderContent()
    {
        $is_allowedToEdit = claro_is_allowed_to_edit();
        $hasDisplayedItems = false;
        $output = '';
        /*
         * Load the description elements
         */
        $context = claro_get_current_course_id();
        

        $descList = course_description_get_item_list($context);
            
        
        if ( count($descList) )
        {
            
            foreach ( $descList as $thisDesc )
            {
                if (($thisDesc['visibility'] == 'INVISIBLE' && $is_allowedToEdit) || $thisDesc['visibility'] == 'VISIBLE')
                {
                    //modify style if the file is recently added since last login
                    /*if (claro_is_user_authenticated() && $claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), claro_get_current_group_id(), claro_get_current_tool_id(), $thisDesc['id']))
                    {
                        $cssItem = 'item hot';
                    }
                    else
                    {
                        $cssItem = 'item';
                    }*/
                    $cssItem = 'item';

                    $cssInvisible = '';
                    if ($thisDesc['visibility'] == 'INVISIBLE')
                    {
                        $cssInvisible = ' invisible';
                    }

                    $output .= '<div class="claroBlock">' . "\n"
                    .   '<h4 class="claroBlockHeader">'
                    .   '<span class="'. $cssItem . $cssInvisible .'" style="font-size:13px;">' . "\n"
                    ;

                    if ( trim($thisDesc['title']) == '' )
                        $output .= '&nbsp;';
                    else
                        $output .= htmlspecialchars($thisDesc['title']);
                        
                    $output .= '</span>' . "\n"
                    .   '</h4>' . "\n"
                    
                    .   '<div class="claroBlockContent">' . "\n"
                    .   '<a href="#" name="ann' . $thisDesc['id'] . '"></a>'. "\n"
        
                    .   '<div class="' . $cssInvisible . '" style="font-size:13px;">' . "\n"
                    .   claro_parse_user_text($thisDesc['content']) . "\n"
                    .   '</div>' . "\n"                    
                    ;
        
                    $output .= '</div>' . "\n" // claroBlockContent
                    .    '</div>' . "\n\n"; // claroBlock
                }
            }
       }
       else
       {
           $output = get_lang('This course is currently not described'); 
       }
       return $output;
    }

    public function renderTitle()
    {
        return get_lang('Course description');
    }
}
