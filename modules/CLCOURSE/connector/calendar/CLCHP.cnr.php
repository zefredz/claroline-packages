<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
* CLAROLINE
*
* User desktop : MyCalendar portlet
* FIXME : move to calendar module
*
* @version      1.9 $Revision$
* @copyright    (c) 2001-2008 Universite catholique de Louvain (UCL)
* @license      http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
* @package      
* @author       Claroline team <info@claroline.net>
*
*/

require_once get_module_path( 'CLCAL' ) . '/lib/agenda.lib.php';

class CLCAL_Portlet extends Portlet
{
    public function renderContent()
    {

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
        
        $orderDirection ='ASC';
        $eventList = agenda_get_item_list($context,$orderDirection);
        $output = '';
        if ($eventList)
        {
            $is_allowedToEdit = claro_is_allowed_to_edit();
            foreach ( $eventList as $thisEvent )
            {
            
                if (('HIDE' == $thisEvent['visibility'] && $is_allowedToEdit)
                    || 'SHOW' == $thisEvent['visibility'])
                {
                    //modify style if the event is recently added since last login
                    /*if (claro_is_user_authenticated()
                        && $claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), claro_get_current_group_id(), claro_get_current_tool_id(), $thisEvent['id']))
                    {
                        $cssItem = 'item hot';
                    }
                    else
                    {
                        $cssItem = 'item';
                    }*/
                     $cssItem = 'item';

//////////////////////////////////////////
                    if ($is_allowedToEdit)
                    {
                        //  Manage Visibility
                        $cmd = $_GET['cmd'];
                        $id = $_GET['id'];
                        $wat = $_GET['wat'];

                        if ( ('mkShow' == $cmd || 'mkHide' == $cmd) && ($thisEvent['id'] == $id) && $wat == 'cal')
                        {
                            if ($cmd == 'mkShow')
                            {
                                $visibility = 'SHOW';
                                $thisEvent['visibility'] = 'SHOW';
                            }

                            if ($cmd == 'mkHide')
                            {
                                $visibility = 'HIDE';
                                $thisEvent['visibility'] = 'HIDE';
                            }
                            agenda_set_item_visibility($id, $visibility);
                            $autoExportRefresh = TRUE;

                        }

                        //  Display Visibility
                        if ($thisEvent['visibility']=='SHOW')
                        {
                            $cssInvisible = '';
                            $display_img = '<a href="' . htmlspecialchars(Url::Contextualize( $_SERVER['PHP_SELF'] . '?wat=cal&amp;cmd=mkHide&amp;id=' . $thisEvent['id'] )) . '">'
                            .    '<img src="' . get_icon_url('visible') . '" alt="' . get_lang('Visible').'" />'
                            .    '</a>' . "\n"
                            ;
                        }
                        else
                        {
                            $cssInvisible = ' invisible';
                            $display_img = '<a href="' . htmlspecialchars(Url::Contextualize( $_SERVER['PHP_SELF'] . '?wat=cal&amp;cmd=mkShow&amp;id=' . $thisEvent['id'] )) . '">'
                            .    '<img src="' . get_icon_url('invisible') . '" alt="' . get_lang('Invisible') . '" />'
                            .    '</a>' . "\n"
                            ;
                        }
                    }
                    else
                    {
                        $display_img = '';
                    }

                    /*
                     * Display the event date
                     */
                    $output .= '<div class="claroBlockContent">' . "\n"
                    .   '<span class="'. $cssItem . $cssInvisible .'" style="font-size:13px;">' . "\n"
                    .    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'), strtotime($thisEvent['day']))) . ' '
                    .    ucfirst( strftime( get_locale('timeNoSecFormat'), strtotime($thisEvent['hour']))) . ' '
                    .    ( empty($thisEvent['lasting']) ? '' : get_lang('Lasting') . ' : ' . $thisEvent['lasting'] ) . ' '
                    .    ( empty($thisEvent['location']) ? '' : get_lang('Location') . ' : ' . $thisEvent['location'] )
                    .    ( empty($thisEvent['content']) ? '<br>' : $thisEvent['content'] )
                    .    $display_img                    
                    .   '</span>' . "\n"
                    
                    /*
                     * Display the event content
                     */
                    .   '<a href="#" name="event' . $thisEvent['id'] . '"></a>'. "\n"
                    .    ( empty($thisEvent['title']  ) ? '' : '<strong>' . htmlspecialchars($thisEvent['title']) . '</strong>' . "\n" )
                    .   '</div>' . "\n"
                    ;
                }
            }
            $output .= '</div>' . "\n\n"; // claroBlock
        }
        else
        {
            $output = get_lang('No event to display');
        }
        return $output;
    }

    public function renderTitle()
    {
        return get_lang('Agenda');
    }
}
