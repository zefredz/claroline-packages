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

if ( ! claro_is_in_a_course() || ! claro_is_course_manager()) claro_disp_auth_form(true);

//claro_set_display_mode_available(true);

/*=====================================================================
   Library
  =====================================================================*/
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

/*=====================================================================
   Command
  =====================================================================*/

$dialogBox = new DialogBox();
	
$userMenu = Array();

$out = '';

if( $cmd == 'rqSummary')
{
	
	$dialogBox->form( '<p>' . ucfirst(get_lang("select date")) . '</p>'
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

/*=====================================================================
   Display section
  =====================================================================*/
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('List of attendance'), Url::Contextualize(get_module_url('CLATT') . '/index.php') );

if($is_allowedToEdit)
{
    $nameTools = get_lang('Summary of attendance');

    $out .= claro_html_tool_title($nameTools);
    
    $out .= $dialogBox->render(); 
    
    // Header of the table
    $out .= '<table class="claroTable emphaseLine" width="100%" cellpadding="2" cellspacing="1" '
           .' border="0" summary="' . ucfirst(get_lang('Summar')) . '">' . "\n";
     
    $out    .=   '<thead>' . "\n"
            .    '<tr class="headerX" align="center" valign="top">'."\n"
            .    '<th>' . get_lang('Last name') .  '</th>' . "\n"
            .    '<th>' . get_lang('First name') . '</th>'."\n"
            .    '<th>' . get_lang('Presence')  . '</th>'."\n"
            .    '<th>' . get_lang('Partial presence')  . '</th>'."\n"
            .    '<th>' . get_lang('Absent')  . '</th>'."\n"
            .    '<th>' . get_lang('Excused')  . '</th>'."\n";
    
    $out .= '</tr></thead>';
    
    // Content of the table
    $out .='<tbody>';
    $summary = get_summary_attendance($dateBegin,$dateEnd);
    
    foreach ($summary as $user)
    {
        $out .= '<tr>' . "\n";
        $out .= '<td>' . $user['nom'] . '</td>' . "\n"
                . '<td>' . $user['prenom'] . '</td>' . "\n"
                . '<td><center>' . $user['present'] . '</center></td>' . "\n"
                . '<td><center>' . $user['partial'] . '</center></td>' . "\n"
                . '<td><center>' . $user['absent'] . '</center></td>' . "\n"
                . '<td><center>' . $user['excused'] . '</center></td>' . "\n";
        $out .= '</tr>' . "\n";
    }
    
    $out .= '</tbody>' . "\n"
        	. '</table>' . "\n";
}
else $out = get_lang('Not allowed');
        
$claroline->display->body->appendContent($out);

echo $claroline->display->render();

?>