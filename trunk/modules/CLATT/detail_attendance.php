<?php  // $Id$

/**
 * CLATT tool
 * List of attendance
 * 
 * @version     2.0
 * @author      Lambert Jérôme <lambertjer@gmail.com>
 * @author 		Philippe Dekimpe
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2.0
 * @package     CLATT
 *
 */

/*=====================================================================
   Initialisation
  =====================================================================*/

$tlabelReq = 'CLATT';

require '../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() || !get_init('is_authenticated')) claro_disp_auth_form(true);

//claro_set_display_mode_available(true);

/*=====================================================================
   Library
  =====================================================================*/
require_once get_path('incRepositorySys')  . '/lib/course_user.lib.php';
require_once 'lib/attendance.lib.php';

/*=====================================================================
   Config
  =====================================================================*/
  
$nameTools = ucfirst(get_lang('attendance')); 

include claro_get_conf_repository() . 'CLATT.conf.php';

$is_allowedToEdit = claro_is_allowed_to_edit();

/*=====================================================================
   Check input
  =====================================================================*/

$cmd = array_key_exists( 'cmd', $_REQUEST ) ? $_REQUEST['cmd'] : null;
$order = array_key_exists( 'order', $_REQUEST ) ? $_REQUEST['order'] : 'nom';
$idList = array_key_exists( 'id', $_REQUEST ) ? (int)$_REQUEST['id'] : 0 ;

/*=====================================================================
   Command
  =====================================================================*/
$dialogBox = new DialogBox();

$userMenu = array();

$userList = get_course_user_list($order);

if($is_allowedToEdit)
{
    if ($cmd == 'exSave')
    {
        $flag = true;
        foreach ( $userList as $thisUser )
        {
        	if(isset($_POST['attendance_'. $thisUser['user_id'].'']))
        	{
        		$attendanceUser = $_POST['attendance_'. $thisUser['user_id']];
        	}
        	else $attendanceUser = '';
        	
        	if(isset($_POST['comment_'. $thisUser['user_id'].'']))
        	{
        		$commentUser = $_POST['comment_'. $thisUser['user_id']];
        	}
        	else $commentUser = '';
        	
        	if (!empty($attendanceUser) || !empty($commentUser))
        	    if (!set_attendance($thisUser['user_id'], $idList,$attendanceUser,$commentUser))
                    $flag = false;
        }
        if ($flag) $dialogBox->success(get_lang('List of attendance saved'));
        else $dialogBox->error(get_lang('List of attendance not saved'));    
    }
        
        
	if( $cmd == 'exExport' )
    {
        require_once( dirname(__FILE__) . '/lib/exportAttList.lib.php');

        // contruction of XML flow
        $csv = export_attendance_list(NULL,0,0,$idList);
		
        if( !empty($csv) )
        {
            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="'.claro_get_current_course_id().'_attendancelist.csv"');
            echo $csv;
            exit;
        }
    }
}

// COMMAND MENU
if($is_allowedToEdit && get_conf('allow_export_csv'))
{
	// Export CSV file of attendance
    $userMenu[] = claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                        $_SERVER['PHP_SELF'] . '?cmd=exExport&id=' . $idList ))
                                     , '<img src="' . get_icon_url('export') . '" alt="" />'
                                     . ucfirst(get_lang('export attendance list'))
                                     );
}


/*=====================================================================
   Display section
  =====================================================================*/
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('List of attendance'), Url::Contextualize(get_module_url('CLATT') . '/index.php') );
ClaroBreadCrumbs::getInstance()->append(claro_date('d/m/Y',get_date($idList)) . ' - '  . get_title($idList));

$out = '';

$out .= '<script type="text/javascript">'
        .   'function changeAllCheckbox()
            {
                if( $("#checkAll").attr("checked") )
                {
                    $(".checkAll").attr("checked", true);
                }
                else
                {
                    $(".checkAll").attr("checked", false);
                }
            }
            '
        .   '</script>';

$out .= $dialogBox->render(); 

$out .= claro_html_menu_horizontal($userMenu);

// Header of the table
$out .= '<table class="claroTable emphaseLine" width="100%" cellpadding="2" cellspacing="1" '
       .' border="0" summary="' . ucfirst(get_lang('course users list')) . '">' . "\n";
$out .= '<caption class="header">' . claro_date('d/m/Y',get_date($idList)) . ' - '  . get_title($idList) . '</caption>';
$out .= '<th></th>' . "\n";
 
$out    .=   '<thead>' . "\n"
        .    '<tr class="headerX" align="center" valign="top">'."\n"
        .    '<th>' .claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                               $_SERVER['PHP_SELF'] . '?order=nom&id='. $idList .'' ))
                                            , get_lang('Last name')) . '</th>' . "\n"
        .    '<th>' . claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                               $_SERVER['PHP_SELF'] . '?order=prenom&id='. $idList .'' ))
                                            , get_lang('First name')) . '</th>'."\n"
        .    '<th>' . get_lang('Attendance') 
        .      ' <input type="checkbox" name="checkAll" id="checkAll" 
        					onchange="changeAllCheckbox();"  /></th>'."\n";

$out .= '<th>' . get_lang('Comment') .  '</th>'."\n";

$out .= '</tr></thead>';

// Content of the table
$out .='<tbody>';

$out .= '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
$out .= '<input type="hidden" name="id" value=' . $idList . ' >';


$attendanceUsers = get_attendance_users($idList);
$i = 0;
foreach ($userList as $thisUser) 
{
    $attendance ='';
    $comment ='';
    foreach ($attendanceUsers as $attendanceUser) {
    	if ($attendanceUser['user_id']==$thisUser['user_id'])
    	{
    	    $attendance  = $attendanceUser['attendance'];
    	    $comment = $attendanceUser['comment'];
    	}
    }
    
    
    $i++;
    if ($is_allowedToEdit || ($thisUser['user_id'] == claro_get_current_user_id()))
    {
        $out .= '<tr align="center" valign="top">'."\n"
            . '<td align="left">'
            . '<img src="' . get_icon_url('user') . '" alt="" />'."\n"
            . '<small>' . $i . '</small>'."\n"
            . '&nbsp;';
    
            $out .= htmlspecialchars( ucfirst(strtolower($thisUser['nom']) ) ) .'</td>';
            
            $out .= '<td>' . htmlspecialchars( $thisUser['prenom'] ) . '</td>';

    	if($is_allowedToEdit)
    	{
    		$out .= '<td>';
    		
    	    $out .= get_lang('Present') . '<input type="radio" class="checkAll" name="attendance_'.$thisUser['user_id'].'" value="present" ';		
    		
    	    if($attendance=='present')
    		{
    			$out .= 'checked > ';
    		}
    		else $out .= '> ';

    		$out .= get_lang('Partially present') . '<input type="radio" name="attendance_'.$thisUser['user_id'].'" value="partial" ';		
    		
    		if($attendance=='partial')
    		{
    			$out .= 'checked > ';
    		}
    		else $out .= '> ';
    		
    		$out .= get_lang('Absent') . '<input type="radio" name="attendance_'.$thisUser['user_id'].'" value="absent" ';		
    		if($attendance=='absent')
    		{
    			$out .= 'checked > ';
    		}
    		else $out .= '> '; 
    		
    		$out .= get_lang('Excused') . '<input type="radio" name="attendance_'.$thisUser['user_id'].'" value="excused" ';
    		if($attendance=='excused')
    		{
    			$out .= 'checked > ';
    		}
    		else $out .= '> '; 
    		
    		$out .= '</td>' . "\n";
    		$out .= '<td><input type="text" value="'.$comment.'" name="comment_'.$thisUser['user_id'].'"/></td>';
    	}
    	elseif ($thisUser['user_id'] == claro_get_current_user_id())
        {
            $out .= '<td>' . htmlspecialchars(get_lang($attendance)) . '</td>'."\n";
            $out .= '<td>' . htmlspecialchars($comment) . '</td>'."\n";
        }
    
        $out .= '</tr>'."\n";
    }
}
$out .= '</tbody>' . "\n"
    .    '</table>' . "\n";

if($is_allowedToEdit)
{
	$out .= '<input type="hidden" name="cmd" value="exSave"/>';
	$out .= '<input type="submit" value="Valider"/></form>';
}
    
$claroline->display->body->appendContent($out);

echo $claroline->display->render();