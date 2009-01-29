<?php // $Id$
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

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

$is_allowedToEdit = claro_is_course_manager();

if ( !claro_is_in_a_course() || !$is_allowedToEdit )
{
    claro_die( get_lang( "Not allowed" ) );
}

load_module_language('CLOOVOO');

// Include libraries
require get_path('includePath') . '/lib/embed.lib.php';
include_once get_module_path('CLOOVOO') . '/lib/oovoo.class.php';

// Initialise variables
$dialogBox = '';


// Parameters
$acceptedCmdList = array('exUpdate');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) $cmd = $_REQUEST['cmd'];
else                                                                         $cmd = null;


$oovooLink = new OovooLink(claro_get_current_course_id());
$oovooLink->load();

if( $cmd == 'exUpdate' )
{
    $oovooUsername = trim($_REQUEST['oovooUsername']);
    
    if( $oovooUsername == '' )
    {
        if( $oovooLink->delete() )
        {
            $dialogBox .= get_lang('ooVoo link successfully deactivated.');
        }
        else
        {
            $dialogBox .= get_lang('Cannot commit change.');
        }        
    }
    else
    {
        // skype name must be empty to deactivate status notifier 
        $oovooLink->setUsername($oovooUsername);

        if( $oovooLink->save() )
        {
            $dialogBox .= get_lang('ooVoo username successfully changed to %oovooUsername.', array('%oovooUsername' => htmlspecialchars($oovooUsername)));
        }
        else
        {
            $dialogBox .= get_lang('Cannot commit change.');
        }
    }
}


// Display section 
$output = new ClarolineScriptEmbed();

$html = '<small><a href="'.$clarolineRepositoryWeb . 'course/index.php?cid=' . htmlspecialchars(claro_get_current_course_id()).'">&lt;&lt; '.get_lang('Back').'</a></small>' . "\n";

$html .= claro_html_tool_title(get_lang('ooVoo link')) . '<br />';

if( !empty($dialogBox) ) 
{
    $html .= claro_html_message_box($dialogBox) . '<br />';
}

// form

$html .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post">' . "\n"
.    '<input type="hidden" name="cmd" value="exUpdate" />' . "\n"
.    '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />' . "\n"
.    '<label for="oovooUsername">'.get_lang('ooVoo username').' : </label>' . "\n"
.    '<input type="text" name="oovooUsername" id="oovooUsername" value="' . $oovooLink->getUsername() . '" />' . "\n"
.    '<p><small>' . get_lang('Leave empty to deactivate ooVoo link.') . '</small></p>' . "\n"
.    '<p><small>' . get_lang('To protect your privacy, the default setting is to allow incoming calls from your contacts only. If you want to allow others to call you by using your ooVoo link, then change your "Privacy" settings in the ooVoo "Settings" window.') . '</small></p>'
.    '<input type="submit" value="'.get_lang('Ok').'" />' . "\n"
.    '</form>' . "\n\n";

// Script output
$output->setContent($html);
$output->output();
?>