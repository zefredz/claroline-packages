<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLJCHAT
 *
 * @author Sébastien Piraux
 *
 */
$tlabelReq = 'CLJCHAT';

require_once '../../claroline/inc/claro_init_global.inc.php';

require_once dirname(__FILE__) . '/lib/cljchat.lib.php';

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) 
{
    if( !isset($_REQUEST['cmd']) ) claro_disp_auth_form(true);
    else                           header("Location: ./index.php");
}
    
claro_set_display_mode_available(true);

$is_allowedToEdit = claro_is_allowed_to_edit();

event_access_tool(claro_get_current_tool_id(), claro_get_current_course_tool_data('label'));

/*
 * init request vars
 */
$acceptedCmdList = array('rqRefresh', 'rqAdd', 'rqFlush', 'rqLogs', 'rqArchive');
if ( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                             $cmd = null;

/*
 * init other vars
 */
// define module table names
$tblNameList = array(
    'jchat'
);

// convert to Claroline course table names
$tbl_jchat_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() ); 
$tblJchat = $tbl_jchat_names['jchat'];

$tbl_mdb_names = claro_sql_get_main_tbl();
$tblUser = $tbl_mdb_names['user'];

if( !isset($_SESSION['jchat_connectionTime']) )
{
	// to avoid displaying message that were sent before arrival on the chat
	$_SESSION['jchat_connectionTime'] = time(); // should not be reset 
}

if( !isset($_SESSION['jchat_lastReceivedMsg']) )
{
	// to add a visual effect when lines are added
	// (this var is reset each time new messages are received)
	$_SESSION['jchat_lastReceivedMsg'] = time();	 
}

/*
 * On the fly install
 */

install_module_in_course( 'CLJCHAT', claro_get_current_course_id() ) ;

/*
 * Admin only commands
 */

if( $cmd == 'rqLogs' && $is_allowedToEdit )
{
    header('Content-type: text/xml');
    // get all message
	echo jchat_display_message_list(false);
	
    return;	
}

if( $cmd == 'rqArchive' && $is_allowedToEdit )
{
	// Prepare archive file content
	$htmlContentHeader = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n"
    .    '<html>' . "\n"
    .    '<head>' . "\n"
    .    '<title>' . get_lang('Chat') . '</title>'
    .    '</head>' . "\n"
    .    '<body>' . "\n\n";

    $htmlContentFooter = '</body>' . "\n\n"
    .    '</html>' . "\n";

    
    $htmlContent = claro_parse_user_text( jchat_display_message_list(false) );
    
    $htmlContent = $htmlContentHeader . $htmlContent . $htmlContentFooter; 
    
    
    // filepath
    $courseDir = claro_get_course_path() .'/document';
    $baseWorkDir = get_path('coursesRepositorySys') . $courseDir;

    // Try to determine a filename that does not exist anymore
    // in the directory where the chat file will be stored

    $chatDate = 'chat.'.date('Y-m-j').'_';
    $i = 1;
    
    while ( file_exists($baseWorkDir.'/'.$chatDate.$i.'.html') ) $i++;

    $chatFilename = $baseWorkDir.'/'. $chatDate.$i.'.html';
    
    $fp = fopen($chatFilename, 'w');

    if( fwrite($fp, $htmlContent) )
    {
        $downloadLink = '<a href="'.get_module_url('CLDOC').'/document.php'.claro_url_relay_context('?').'">' . basename($chatFilename) . '</a>';
        
        echo get_lang('%chat_filename is now in the document tool. (<em>This file is visible</em>)',array('%chat_filename' => $downloadLink));
        return;
    }
    else
    {
        echo get_lang('Store failed');
        return;
    }    
}

/*
 * Other commands
 */
if( $cmd == 'rqAdd' )
{
	jchat_add_message($_REQUEST['message']);
	
	// always request refresh to have a response for ajax call
	$cmd = 'rqRefresh';
}

if( $cmd == 'rqRefresh' )
{
    header('Content-type: text/xml');
	echo jchat_display_message_list();
	
    return;
}


		   
$cmdMenu = array();
if( $is_allowedToEdit )
{
	$cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqLogs' . claro_url_relay_context('&amp;')
                                    , get_lang('Show logs')
                                    , array('onClick' => "$.ajax({url: 'index.php?cmd=rqLogs', success: function(response){displayLogs(response)}, dataType: 'html'}); return false;")                                    
                                    );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqArchive' . claro_url_relay_context('&amp;')
                                    , get_lang('Store Chat')                                   
                                    , array('onClick' => "$.ajax({url: 'index.php?cmd=rqArchive', success: function(response){showMsg(response)}, dataType: 'html'}); return false;")
                                    );				
}
		   
/*
 * Output
 */
 
//-- Headers 
// jquery javascript library
$htmlHeadXtra[] = '<script src="./js/jquery.js" type="text/javascript"></script>' . "\n";

// init var with values from get_conf before including tool library
$htmlHeadXtra[] = '<script type="text/javascript">' . "\n"
.    "var refreshRate = " . (get_conf('refresh_display_rate',5)*1000) . "\n"
.	 "</script>";

// tool javascript library
$htmlHeadXtra[] = '<script src="./js/cljchat.js" type="text/javascript"></script>' . "\n";

// tool css
$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="./cljchat.css" media="screen, projection, tv" />' . "\n";
 
//-- Content 
$nameTools = get_lang('Chat');

$noQUERY_STRING = true; // to remove parameters in the last breadcrumb link

include  get_path('includePath') . '/claro_init_header.inc.php';

echo claro_html_tool_title($nameTools);

echo '<p id="cl_jchat_messageBox"></p>' . "\n"
.    '<div id="cl_jchat_chatarea">'.	 "\n"
.	 '</div>' . "\n";

// display form
echo '<form action="index.php?cmd=rqAdd" id="cl_jchat_form" method="GET" />' . "\n"
.    claro_form_relay_context() . "\n"
.    '<img src="'.get_module_url('CLJCHAT').'/img/loading.gif" alt="'.get_lang('Loading...').'" id="cl_jchat_loading" width="16" height="16" />' . "\n"
.    '<input id="cl_jchat_msg" type="text" name="message" maxlength="200" size="80" />' . "\n"
.    '<input type="submit" name="Submit" value=" &gt;&gt; " />' . "\n"
.    '</form>' . "\n"

.    claro_html_menu_horizontal($cmdMenu) . "\n"

.    '<div id="cl_jchat_archives">'
.    '<h3><span><a href="#" onclick="hideLogs();return false;">'.get_lang('Close').'</a></span>Logs</h3>'
.    '<div id="cl_jchat_archives_content"></div>' . "\n" 
.    '</div>' . "\n";



include  get_path('includePath') . '/claro_init_footer.inc.php';

?>
