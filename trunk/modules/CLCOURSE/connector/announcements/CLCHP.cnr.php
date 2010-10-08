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

require_once get_module_path( 'CLANN' ) . '/lib/announcement.lib.php';
//require_once('../../claroline/inc/lib/core/notify.lib.php');

uses('courselist.lib');

class CLANN_Portlet extends Portlet
{
    public function __construct()
    {
        if (file_exists(claro_get_conf_repository() . 'CLANN.conf.php'))
        {
            include claro_get_conf_repository() . 'CLANN.conf.php';
        }
    }

    public function renderContent()
    {

        //$context = claro_get_current_context(CLARO_CONTEXT_COURSE);
        $sessionCourse = '';
        if (isset($_SESSION['courseSessionCode'][claro_get_current_course_id()]))
        {
            $sessionCourse = $_SESSION['courseSessionCode'][claro_get_current_course_id()];
        }
        
        if (! empty($sessionCourse))
        {
             $context[CLARO_CONTEXT_COURSE] = $sessionCourse;
        }
        else
        {
            $context[CLARO_CONTEXT_COURSE] = claro_get_current_course_id();
        }
        
        $announcementList = announcement_get_item_list($context);

        $output = '';

        if($announcementList)
        {
                $is_allowedToEdit = claro_is_allowed_to_edit();
                foreach ( $announcementList as $thisAnnouncement)
                {
                    // get the status of the announcements to manage the display the visible or invisible img
                    if ($is_allowedToEdit)
                    {
                        //  Manage Visibility
                        $cmd = $_GET['cmd'];
                        $id = $_GET['id'];
                        $wat = $_GET['wat'];

                        if ( ('mkShow' == $cmd || 'mkHide' == $cmd) && ($thisAnnouncement['id'] == $id) && $wat == 'ann')
                        {
                            if ('mkShow' == $cmd )
                            {
                                $visibility = 'SHOW';
                                $thisAnnouncement['visibility']='SHOW';
                            }
                            if ('mkHide' == $cmd )
                            {
                                $visibility = 'HIDE';
                                $thisAnnouncement['visibility']='HIDE';
                            }
                            announcement_set_item_visibility($id,$visibility);
                            $autoExportRefresh = TRUE;

                        }

                        //  Display Visibility
                        if ($thisAnnouncement['visibility']=='SHOW')
                        {
                            $cssInvisible = '';
                            $display_img = '<a href="' . htmlspecialchars(Url::Contextualize( $_SERVER['PHP_SELF'] . '?wat=ann&amp;cmd=mkHide&amp;id=' . $thisAnnouncement['id'] )) . '">'
                            .    '<img src="' . get_icon_url('visible') . '" alt="' . get_lang('Visible').'" />'
                            .    '</a>' . "\n"
                            ;
                        }
                        else
                        {
                            $cssInvisible = ' invisible';
                            $display_img = '<a href="' . htmlspecialchars(Url::Contextualize( $_SERVER['PHP_SELF'] . '?wat=ann&amp;cmd=mkShow&amp;id=' . $thisAnnouncement['id'] )) . '">'
                            .    '<img src="' . get_icon_url('invisible') . '" alt="' . get_lang('Invisible') . '" />'
                            .    '</a>' . "\n"
                            ;
                        }
                    }
                    else
                    {
                        $display_img = '';
                    }

                    if ($thisAnnouncement['visibility'] == 'SHOW' || $is_allowedToEdit)
                    {
                        $output .= '<div class="claroBlockContent"><span class="item '.$cssInvisible.'" style="font-size:13px;">'
                        .    '<b>' . $thisAnnouncement['title'] .'</b>'
                        .    ' - '
                        .    strip_tags($thisAnnouncement['content']) . "\n"
                        .    '<br>'
                        .    $display_img
                        .    '</span></div>' . "\n"
                        ;
                    }
                }
        }
        else
        {
            $output .= "\n"
            .    get_lang('No announcement') . "\n"
            ;
        }
        return $output;
    }

    public function renderTitle()
    {
        return get_lang('Latest announcements');
    }
}
