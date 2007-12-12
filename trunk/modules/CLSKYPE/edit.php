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
 * @package CLSKYPE
 *
 * @author Sebastien Piraux
 *
 */

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_in_a_course() && !claro_is_allowed_to_edit() )
{
    claro_die( get_lang( "Not allowed" ) );
}

load_module_language('CLSKYPE');

// Include libraries
require get_path('includePath') . '/lib/embed.lib.php';
include_once get_module_path('CLSKYPE') . '/lib/skype.status.class.php';

// Initialise variables
$dialogBox = '';


// Parameters
$acceptedCmdList = array('exUpdate');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) $cmd = $_REQUEST['cmd'];
else                                                                         $cmd = null;


$skypeStatusNotifier = new SkypeStatus(claro_get_current_course_id());
$skypeStatusNotifier->load();

if( $cmd == 'exUpdate' )
{
    $newSkypeName = trim($_REQUEST['skypeName']);
    
    if( $newSkypeName == '' )
    {
        if( $skypeStatusNotifier->delete() )
        {
            $dialogBox .= get_lang('Skype status notifier successfully deactivated.');
        }
        else
        {
            $dialogBox .= get_lang('Cannot save change.');
        }        
    }
    else
    {
        // skype name must be empty to deactivate status notifier 
        $skypeStatusNotifier->setSkypeName($newSkypeName);

        if( $skypeStatusNotifier->save() )
        {
            $dialogBox .= get_lang('Skype name successfully changed to %skypeName.', array('%skypeName' => htmlspecialchars($_REQUEST['skypeName'])));
        }
        else
        {
            $dialogBox .= get_lang('Cannot save change.');
        }
    }
}


// Display section 
$output = new ClarolineScriptEmbed();

$html = '';

$html .= '<h2>' . get_lang('Skype status notifier') . '</h2>';

if( !empty($dialogBox) ) 
{
    $html .= claro_html_message_box($dialogBox) . '<br />';
}

// form

$html .= '<form action"'.$_SERVER['PHP_SELF'].'" method="post">' . "\n"
.    '<input type="hidden" name="cmd" value="exUpdate" />' . "\n"
.    '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />' . "\n"
.    '<label for="skypeName">'.get_lang('Skype name').' : </label>' . "\n"
.    '<input type="text" name="skypeName" id="skypeName" value="' . $skypeStatusNotifier->getSkypeName() . '" />' . "\n"
.    '<p><small>' . get_lang('Leave empty to deactivate status notifier.') . '</small></p>' . "\n"
.    '<p><small>' . get_lang('Do not forget to allow your status to be shown from your Skype client.') . '</small><br />'
.    '<img src="' . get_module_url('CLSKYPE') . '/img/privacy_shot.jpg" alt="'.get_lang('Skype options, Privacy panel.').'" /></p>' . "\n"
.    '<input type="submit" value="'.get_lang('Ok').'" />' . "\n"
.    '</form>' . "\n\n";

// Script output
$output->setContent($html);
$output->output();
?>
