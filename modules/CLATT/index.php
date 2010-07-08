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

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form(true);

//claro_set_display_mode_available(true);

/*=====================================================================
   Library
  =====================================================================*/

require_once get_path('incRepositorySys')  . '/lib/form.class.php';
require_once get_path('incRepositorySys')  . '/lib/utils/input.lib.php';
require_once get_path('incRepositorySys')  . '/lib/form.lib.php';
require_once 'lib/attendance.lib.php';

/*=====================================================================
   Config
  =====================================================================*/
//include claro_get_conf_repository() . 'CLATT.conf.php';

$is_allowedToEdit = claro_is_allowed_to_edit();
/*
 * CHECK INPUT
 */

$cmd = array_key_exists( 'cmd', $_REQUEST ) ? $_REQUEST['cmd'] : null;
$idList = array_key_exists( 'idList', $_REQUEST ) ? (int)$_REQUEST['idList'] : 0 ;

if(isset($_POST['yearDateBegin']))
	$dateBegin = claro_sql_escape($_POST['yearDateBegin'])
	                . '-' .claro_sql_escape($_POST['monthDateBegin'])
	                . '-' .claro_sql_escape($_POST['dayDateBegin'])
                    . ' 00:00:00';
else $dateBegin = 0;

if(isset($_POST['yearDateEnd']))
	$dateEnd = claro_sql_escape($_POST['yearDateEnd'])
	                . '-' .claro_sql_escape($_POST['monthDateEnd'])
	                . '-' .claro_sql_escape($_POST['dayDateEnd'])
                    . ' 00:00:00';
else $dateEnd = 0;

if(isset($_POST['yearDateToAdd']))
	$dateAtt = claro_sql_escape($_POST['yearDateToAdd'])
	                . '-' .claro_sql_escape($_POST['monthDateToAdd'])
	                . '-' .claro_sql_escape($_POST['dayDateToAdd'])
                    . ' 00:00:00';
else $dateAtt = NULL;

if(isset($_POST['noDate']) && $_POST['noDate']='noDate')
	$dateAtt = NULL;

if(isset($_REQUEST['titleAtt']) && !empty($_REQUEST['titleAtt']))
	$titleAtt = $_REQUEST['titleAtt'];
else $titleAtt = NULL;

/*=====================================================================
   Command
  =====================================================================*/

$dialogBox = new DialogBox();
	
$userMenu = Array();

$out = '';

if($is_allowedToEdit)
{
	if( $cmd == 'rqAdd' )
	{
		$dialogBox->form( '<p>' . ucfirst(get_lang("add a list of attendance")) . '</p>'
            . '<table><form action="' . $_SERVER [ 'PHP_SELF' ] . '" method="post">' . "\n"
            . '<input name="cmd" type="hidden" value="exCreate" />' . "\n"
            . '<tr><td><label for="titleAtt">' . ucfirst(get_lang( 'Title for this list' )) . ': </label></td><td>'
			. '<input type="text" name="titleAtt" />'
            . '<br />' . "\n"
            . '<tr><td><label for="dateAtt">' . ucfirst(get_lang( 'Date for this list' )) . ': </label></td><td>'
			. claro_html_date_form('dayDateToAdd', 'monthDateToAdd', 'yearDateToAdd')
			. ' no date <input type="checkbox" name="noDate" value="noDate">'
            . '<br />' . "\n"
            . '<tr><td><input value="' . ucfirst(get_lang( 'continue' )) . '" type="submit" /></td><td>'
			. '</form></table>');
	}
	
	if ($cmd == 'exCreate')
	{
        if (isset($dateAtt) || isset($titleAtt))
        {
            if (create_attendanceList($dateAtt,$titleAtt))
            {
                $dialogBox->success('List of attendance created');
            }
            else 
            {
                $dialogBox->error('List of attendance no created');
            }
        }
        else
        {
            $dialogBox->warning('Date or title is required');
        }

	}
	
    if ($cmd == 'rqDelete')
    {
    	$dialogBox->form( '<p>' . get_lang("Delete a list of attendance") . '</p>'
        . '<table><form action="' . $_SERVER [ 'PHP_SELF' ] . '" method="post">' . "\n"
        . '<tr><td><label for="cmd">' . get_lang( ' Are you sure to delete this list of attendance : ' ) 
        . get_title($idList) . ' ' . claro_date('d/m/Y',get_date($idList)) .' </label></td>'
		. '<td><input type="checkbox" name="cmd" value="exDelete">'
		. '<input type="hidden" name="id" value="' . $idList . '"></td>'
        . '<br />' . "\n"
        . '<tr><td><input value="' . ucfirst(get_lang( 'continue' )) . '" type="submit" /></td><td>'
		. '</form></table>');
    }
	
    if( $cmd == 'exDelete')
    {
    	if($idList>0)
    	{
            if (del_attendanceList($idList))
    		$dialogBox->success(ucfirst(get_lang('List of attendance deleted')));
    	}
    	else
    	{
    		$dialogBox->error(get_lang('List of ttendance not deleted'));
    	}
    }
    
    if ($cmd == 'rqEdit')
    {
    	$dialogBox->form( '<p>' . get_lang("Add a list of attendance") . '</p>'
        . '<table><form action="' . $_SERVER [ 'PHP_SELF' ] . '" method="post">' . "\n"
        . '<input name="cmd" type="hidden" value="exEdit" />' . "\n"
        . '<input name="id" type="hidden" value="' . $idList . '" />' . "\n"
        . '<tr><td><label for="titleAtt">' . ucfirst(get_lang( 'Title for this list' )) . ': </label></td><td>'
		. '<input type="text" name="titleAtt" value="' . get_title($idList) . '"/>'
        . '<br />' . "\n"
        . '<tr><td><label for="dateAtt">' . ucfirst(get_lang( 'Date for this list' )) . ': </label></td><td>'
		. claro_html_date_form('dayDateToAdd', 'monthDateToAdd', 'yearDateToAdd',get_date($idList))
		. ' no date <input type="checkbox" name="noDate" value="noDate">'
        . '<br />' . "\n"
        . '<tr><td><input value="' . ucfirst(get_lang( 'continue' )) . '" type="submit" /></td><td>'
		. '</form></table>');
    }
    
    if ($cmd == 'exEdit' && $idList>0)
    {
        if(edit_attendanceList($idList,$dateAtt,$titleAtt) > 0)
    		$dialogBox->success(get_lang('Attendance saved'));
        else 
            $dialogBox->error(get_lang('Attendance not saved'));
    }
    
	if( $cmd == 'exExport')
    {
        require_once( dirname(__FILE__) . '/lib/exportAttList.lib.php');
		
        // contruction of XML flow
        $csv = export_attendance_list(NULL, $dateBegin, $dateEnd,$idList);
		
        if( !empty($csv) )
        {
            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="'.claro_get_current_course_id().'_attendancelist.csv"');
            echo $csv;
            exit();
        }
    }
    
	if( $cmd == 'rqExport')
    {
		
		$dialogBox->form( '<p>' .get_lang('select date') . '</p>'
            . '<table><form action="' . $_SERVER [ 'PHP_SELF' ] . '" method="post">' . "\n"
            . '<input name="cmd" type="hidden" value="exExport" />' . "\n"
            . '<tr><td><label for="dateToAdd">' . get_lang( 'start date' ) . ': </label></td><td>'
			. claro_html_date_form('dayDateBegin', 'monthDateBegin', 'yearDateBegin')
            . '</td></tr>' . "\n"
			. '<tr><td><label for="dateToAdd">' . get_lang( 'end date' ) . ': </label></td><td>'
			. claro_html_date_form('dayDateEnd', 'monthDateEnd', 'yearDateEnd')
            . '</td></tr>' . "\n"
            . '<tr><td>' . "\n"
            . '<input value="' . get_lang( 'continue' ) . '" type="submit" />'
            . '</td></tr>' . "\n"
			. '</form></table>');
    }
    
	// COMMAND MENU
    
	// Add a date for attendance
	$userMenu[] = claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                    $_SERVER['PHP_SELF'] . '?cmd=rqAdd' ))
                                 , get_lang('Add a list of attendance'));
                                 
    if(get_conf('allow_export_csv'))
    {
    	// Export CSV file of attendance
    	$userMenu[] = claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                        $_SERVER['PHP_SELF'] . '?cmd=exExport' ))
                                     , '<img src="' . get_icon_url('export') . '" alt="" />'
                                     . ucfirst(get_lang('export attendance list')));
                                     
        // Export CSV file of attendance with date choice
		$userMenu[] = claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                        $_SERVER['PHP_SELF'] . '?cmd=rqExport' ))
                                     , '<img src="' . get_icon_url('export') . '" alt="" />'
                                     . ucfirst(get_lang('export attendance list with date choice')));
                                     
        // Summary of attendance
		$userMenu[] = claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                        'summary.php?idList=' . $idList ))
                                     , '<img src="' . get_icon_url('summary') . '" alt="" />'
                                     . ucfirst(get_lang('Summary of attendance')));
    } 
}

/*=====================================================================
   Display section
  =====================================================================*/
//ClaroBreadCrumbs::getInstance()->prepend( get_lang('Attendance'), Url::Contextualize(get_module_url('CLATT') . '/index.php') );

$nameTools = get_lang('List of attendance');

$out .= claro_html_tool_title($nameTools);

$out .= $dialogBox->render(); 

$out .= claro_html_menu_horizontal($userMenu);

$out .= '<table class="claroTable emphaseLine" width="100%" cellpadding="2" cellspacing="1" '
.    ' border="0" summary="' . ucfirst(get_lang('course users list for attendance')) . '">' . "\n";

$out .= '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">'."\n";


$lists = get_attendance_course_list();

if (isset($lists))
{

    $out .=  '<th>' . get_lang('Date');
    $out.= '</th>'."\n";

	$out.= '<th>' . get_lang('Title');
    $out.= '</th>'."\n";
    
    if($is_allowedToEdit)
    {
    	$out .=    '<th>' . get_lang('Edit') . '</th>'."\n";
    	$out .=    '<th>' . get_lang('Export') . '</th>'."\n";
        $out .=    '<th>' . get_lang('Delete') . '</th>'."\n";
    }

    $out .= '</tr></thead><tbody>';
    
    foreach ($lists as $thisList)
    {
    	if($is_allowedToEdit)
		{
			$out .='<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		    $out .= '<tr align="center" valign="top">'."\n";
			$out .= '<td>';
			if ($thisList['date_att'] != '0')
			$out .=claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                       'detail_attendance.php?cmd=detaile&id='.$thisList['id'].'' ))
                                    , claro_date("d/m/Y",$thisList['date_att']));
            $out .= '</td>';
			$out.= '<td>' . claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                       'detail_attendance.php?cmd=detail&id='.$thisList['id'].'' ))
                                    , $thisList['title']);
			$out .= '</td>';
			$out.= '<td>' . claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                           $_SERVER['PHP_SELF'] . '?cmd=rqEdit&id='.$thisList['id'].'' ))
                        , '<img src="' . get_icon_url('edit') . '" alt="" />');
			$out .= '</td>';
			
	        $out.= '<td>' . claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                    $_SERVER['PHP_SELF'] . '?cmd=exExport&id='.$thisList['id'].'' ))
                                 , '<img src="' . get_icon_url('export') . '" alt="" />');
            $out .= '</td>';                              
			$out .= '<td>'.claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                       $_SERVER['PHP_SELF'] . '?cmd=rqDelete&id='.$thisList['id'].'' ))
                                    , '<img src="' . get_icon_url('delete') . '" alt="'.get_lang("delete").'" />').'</td></tr>'."\n";
		} 
		else
		{
			$out .= '<tr align="center" valign="top">'."\n";
			$out .= '<td>'.claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                       'detail_attendance.php?id='.$thisList['id'].'' ))
                                    , claro_date("d/m/Y",$thisList['date_att']));
			$out .= '</td>';
			$out .= '<td>'.claro_html_cmd_link( htmlspecialchars(Url::Contextualize(
                                       'detail_attendance.php?id='.$thisList['id'].'' ))
                                    , $thisList['title']);
			$out .= '</td>';
			$out .= '</tr>'."\n";
		}
    }
}

$out .= '</tbody>' . "\n"
.    '</table>' . "\n";

$claroline->display->body->appendContent($out);

echo $claroline->display->render();