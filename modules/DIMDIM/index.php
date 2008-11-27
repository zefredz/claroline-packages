<?php // $Id$
/**
 * CLAROLINE
 *
 * $Revision$
 * @version 1.9 Revision
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @author Sebastien Piraux - Revision 1.9 by Sokay Benjamin
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package DIMDIM
 *
 */

//Tool label
$tlabelReq = 'DIMDIM';

//load claroline kernel
require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

//tool name
$nameTools = get_lang('Video webconference');

//load used lib
FromKernel::uses('utils/input.lib');
FromKernel::uses('form.lib');

require_once dirname( __FILE__ ) . '/lib/DIMDIM.class.php';

//check
if ( !claro_is_tool_allowed() )
{
    if ( claro_is_in_a_course() )
    {
        claro_die( get_lang( "Not allowed" ) );
    }
    else
    {
        claro_disp_auth_form( true );
    }
}

claro_set_display_mode_available(true);
$is_allowedToEdit = claro_is_allowed_to_edit();

/*
 * init request vars
 */

$acceptedCmdList = array('rqEdit', 'exEdit', 'rqDelete', 'exDelete', 'exVisible', 'exInvisible', 'rqView');
$userInput = Claro_UserInput::getInstance();
$cmdTest = $userInput->get('cmd');
$confIdTest = $userInput->get('confId');

if( isset($cmdTest) && in_array($cmdTest, $acceptedCmdList) )   
{
    $cmd = $userInput->get('cmd');
}
else                                                                
{
    $cmd = null;
}

if( isset($cmdTest) && is_numeric($confIdTest) )
{
    $confId = (int) $userInput->get('confId');
}   
else  
{
    $confId = null;
}                                                              

/*
 * init other vars
 */

$conference = new Conference();

if( !is_null($confId) )
{
    if( !$conference->load($confId) )
    {
        $cmd = null;
        $confId = null;
    }
}

$conferenceList = new ConferenceList();

$message = '';
$dialogBox = new DialogBox();

/*
 * Admin only commands
 */

if( $is_allowedToEdit )
{
    if( $cmd == 'exEdit' )
    {
        $conference->setTitle($userInput->get('title'));
        $conference->setDescription($userInput->get('description'));
        $conference->setWaitingArea($userInput->get('waitingArea'));
        $conference->setMaxUsers($userInput->get('maxUsers'));
        $conference->setDuration($userInput->get('duration'));
        $conference->setType($userInput->get('type'));
        $conference->setAttendeeMikes($userInput->get('attendeeMikes'));
        $conference->setNetwork($userInput->get('network'));
        $conference->setStartTime(mktime($userInput->get('startHour'),$userInput->get('startMinute'),0,$userInput->get('startMonth'),$userInput->get('startDay'),$userInput->get('startYear')) );

        if( $conference->validate() )
        {
            if( $insertedId = $conference->save() )
            {
                if( is_null($confId) )
                {
                    $dialogBox->success(get_lang('Conference successfully created'));
                    $confId = $insertedId;
                }
                else
                {
                    $dialogBox->success(get_lang('Conference successfully modified'));
                }
            }
            else
            {
                $dialogBox->success('test');
                $cmd = 'rqEdit';
            }

        }
        else
        {
            if( claro_failure::get_last_failure() == 'conference_no_title' )
            {
                $message = get_lang('Field \'%name\' is required', array('%name' => get_lang('Title')));
                $dialogBox->error($message);
            }

            if( claro_failure::get_last_failure() == 'conference_invalid_date' )
            {
                $message = get_lang('Date is in the past');
                $dialogBox->error($message);
            }
            $cmd = 'rqEdit';
        }
    }

    if( $cmd == 'rqEdit' )
    {
        // show form
        $message = "\n\n";

        if( !is_null($confId) )
        {
            $message .= '<strong>' . get_lang('Edit conference settings') . '</strong>' . "\n";
        }
        else
        {
            $message .= '<strong>' . get_lang('Create a new conference') . '</strong>' . "\n";
        }

        $message .= '<form action="' . $_SERVER['PHP_SELF'] . '?confId='.$confId.'" method="post">' . "\n"
        .    claro_form_relay_context()
        .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
        .    '<input type="hidden" name="cmd" value="exEdit" />' . "\n";

        // title
        $message .=    '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .    '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($conference->getTitle()).'" /><br />' . "\n";

        // description
        $message .=    '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
        .    '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($conference->getDescription()).'</textarea><br />';

        // startTime
        $message .= get_lang('Start date') . '<br />' . "\n"
        . claro_html_date_form('startDay', 'startMonth', 'startYear', $conference->getStartTime(), 'long')
        . ' - '
        . claro_html_time_form("startHour", "startMinute", $conference->getStartTime())
        . '<small>' . get_lang('(d/m/y hh:mm)') . '</small><br />' . "\n";

        // duration in hours
        $message .= '<label for="label_duration"  >'.get_lang('Duration').'</label><br />' . "\n" ;

        $durationValueList = array('1','2','3','4','5');

        $message .= '<select id="label_duration" name="duration">' . "\n";

        foreach ( $durationValueList as $durationValue )
        {
            if ( $durationValue == $conference->getDuration() )
            {
                $message .= '<option value="'. htmlspecialchars($durationValue) .'" selected="selected">' . $durationValue .'</option>' . "\n";
            }
            else
            {
                $message .= '<option value="'. htmlspecialchars($durationValue) .'" >' . $durationValue .'</option>' . "\n";
            }
        }

        $message .= '</select><br />' . "\n";

        // waiting area
        $message .= get_lang('Waiting Area') . '<br />' . "\n"
        . '<input id="label_waiting_area_TRUE"  type="radio" name="waitingArea" value="ENABLE"  '
        . ( $conference->getWaitingArea() == 'ENABLE' ? ' checked="checked" ':' ') . ' >'
        . '<label for="label_waiting_area_TRUE"  >' . get_lang('Yes') . '</label>' . '&nbsp;'
        . '<input id="label_waiting_area_FALSE" type="radio" name="waitingArea" value="DISABLE" '
        . ( $conference->getWaitingArea() == 'DISABLE' ? ' checked="checked" ': ' ') . ' >'
        . '<label for="label_waiting_area_FALSE" >' . get_lang('No') . '</label><br />' . "\n";

        // maximum users
        $message .= '<label for="label_max_users"  >'.get_lang('Maximum users').'</label><br />' . "\n" ;
        
        // display select dialogBox with accepted value
        $maxUserValueList = array('20', '40', '60', '80', '100', '200', '300', '400', '500');

        $message .= '<select id="label_max_users" name="maxUsers">' . "\n";

        foreach ( $maxUserValueList as $maxUserValue )
        {
            if ( $maxUserValue == $conference->getMaxUsers() )
            {
                $message .= '<option value="'. htmlspecialchars($maxUserValue) .'" selected="selected">' . $maxUserValue .'</option>' . "\n";
            }
            else
            {
                $message .= '<option value="'. htmlspecialchars($maxUserValue) .'" >' . $maxUserValue .'</option>' . "\n";
            }
        } // end foreach

        $message .= '</select><br />' . "\n";

        // attendee Mikes
        $message .= '<label for="label_attendeeMikes"  >'.get_lang('Attendee mikes').'</label><br />' . "\n" ;

        $attendeeMikesValueList = array('1','2','3','4','5');

        $message .= '<select id="label_attendeeMikes" name="attendeeMikes">' . "\n";

        foreach ( $attendeeMikesValueList as $attendeeMikesValue )
        {
            if ( $attendeeMikesValue == $conference->getAttendeeMikes() )
            {
                $message .= '<option value="'. htmlspecialchars($attendeeMikesValue) .'" selected="selected">' . $attendeeMikesValue .'</option>' . "\n";
            }
            else
            {
                $message .= '<option value="'. htmlspecialchars($attendeeMikesValue) .'" >' . $attendeeMikesValue .'</option>' . "\n";
            }
        }

        $message .= '</select><br />' . "\n";

        // type video and audio or audio only, default is audio
        $message .= '<label for="label_type"  >'.get_lang('Type').'</label><br />' . "\n" ;

        $typeValueList = array('AUDIO' => get_lang('Audio') , 'VIDEO' => get_lang('Audio/Video'));

        $message .= '<select id="label_type" name="type">' . "\n";

        foreach ( $typeValueList as $typeValue => $typeLabel )
        {
            if ( $typeValue == $conference->getType() )
            {
                $message .= '<option value="'. htmlspecialchars($typeValue) .'" selected="selected">' . $typeLabel .'</option>' . "\n";
            }
            else
            {
                $message .= '<option value="'. htmlspecialchars($typeValue) .'" >' . $typeLabel .'</option>' . "\n";
            }
        } // end foreach
        $message .= '</select><br />' . "\n";

        // network type
        $message .= '<label for="network_type"  >'.get_lang('Network').'</label><br />' . "\n" ;

        $networkValueList = array('DIALUP' => get_lang('Dial up') , 'CABLEDSL' => get_lang('Cable/Dsl'), 'LAN' => get_lang('LAN') );

        $message .= '<select id="label_network" name="network">' . "\n";

        foreach ( $networkValueList as $networkValue => $networkLabel )
        {
            if ( $networkValue == $conference->getNetwork() )
            {
                $message .= '<option value="'. htmlspecialchars($networkValue) .'" selected="selected">' . $networkLabel .'</option>' . "\n";
            }
            else
            {
                $message .= '<option value="'. htmlspecialchars($networkValue) .'" >' . $networkLabel .'</option>' . "\n";
            }
        } // end foreach

        $message .= '</select><br />' . "\n";

        $message .= '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
        .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
        .    claro_html_button($_SERVER['PHP_SELF'] . '?confId='.$confId, get_lang('Cancel'))
        .    '</form>' . "\n"
        ;
        $dialogBox->form($message);

    }

    if( $cmd == 'exDelete' )
    {
        if( $conference->delete() )
        {
            $dialogBox->success(get_lang('Conference succesfully deleted'));
        }
        else
        {
            $dialogBox->error(get_lang('Fatal error : cannot delete conference'));
        }
    }

    if( $cmd == 'rqDelete' )
    {
        $message = get_lang('Are you sure to delete conference "%conferenceTitle" ?', array('%conferenceTitle' => htmlspecialchars($conference->getTitle()) ));

        $message .= '<p>'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;confId='.$confId.'">' . get_lang('Yes') . '</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
        .    '</p>' . "\n";

        $dialogBox->question($message);
    }

    if( $cmd == 'exVisible' )
    {
        $conference->setVisible();

        $conference->save();
    }

    if( $cmd == 'exInvisible' )
    {
        $conference->setInvisible();

        $conference->save();
    }

}

if( $cmd == 'rqView' )
{
    $message = '<strong>'.$conference->getTitle().'</strong>' . "\n"
    .    '<blockquote>'.$conference->getDescription().'</blockquote>' . "\n";

    if( $is_allowedToEdit )
    {
        // teacher
        $message .= '<a href="'.$conference->buildUrl(true).'" target="_blank">'.get_lang('Join conference as administrator').'</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="'.$conference->buildUrl().'" target="_blank">'.get_lang('Join conference as attendee').'</a>' . "\n";
    }
    elseif( time() < $conference->getStartTime() )
    {
        // conference not yet available
        $message .= get_lang('Conference is not yet available.  Will start on %startTime',
        array('%startTime' => claro_disp_localised_date($dateTimeFormatLong, $conference->getStartTime()))) . "\n";
    }
    elseif( $conference->getStartTime() < ( time() - ( $conference->getDuration() + 1 ) * 3600 ) )
    {
        // conference has ended (startTime + duration + 1 hour)
        $message .= get_lang('Conference is finished and closed') . "\n";
    }
    else
    {
        // conference is available to students
        $message .= '<a href="'.$conference->buildUrl().'" target="_blank">'.get_lang('Join conference').'</a>' . "\n";
    }
    $dialogBox->form($message);

}

//add breadcrumb
ClaroBreadCrumbs::getInstance()->setCurrent($nameTools,get_module_entry($tlabelReq));

//add title
$claroline->display->body->appendContent(claro_html_tool_title($nameTools));

//add display dialogBox content to the body 
$claroline->display->body->appendContent($dialogBox->render());

$conferenceListArray = $conferenceList->load();

$cmdMenu = array();

if($is_allowedToEdit)
{
    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqEdit' . claro_url_relay_context('&amp;'),get_lang('Schedule a conference'));
}

$claroline->display->body->appendContent(claro_html_menu_horizontal($cmdMenu ));

$confList = '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n"
.    '<th>' . get_lang('Conference') . '</th>' . "\n"
.    '<th>' . get_lang('Date') . '</th>' . "\n"
.    '<th>' . get_lang('Duration') . '</th>' . "\n";

if( $is_allowedToEdit )
{
    // display conference name and tools to edit it
    // titles
    $confList .= '<th>' . get_lang('Modify') . '</th>' . "\n"
    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
    .    '<th>' . get_lang('Visibility') . '</th>' . "\n";
}

$confList .=
'</tr>' . "\n"
.    '</thead>' . "\n";

$displayedConfCount = 0;

if( !empty($conferenceListArray) && is_array($conferenceListArray) )
{
    $confList .= '<tbody>' . "\n";

    foreach( $conferenceListArray as $aConference )
    {
        // do not display to student if conf is not visible
        if( $aConference['visibility'] == 'INVISIBLE' && !$is_allowedToEdit ) continue;

        $displayedConfCount++;

        $confList.= '<tr align="center"' . (($aConference['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";

        // title
        $confList .=  '<td align="left">'
        .    '<a href="index.php?cmd=rqView&amp;confId='.$aConference['id'].'" title="'.htmlspecialchars(strip_tags($aConference['description'])).'">'
        .    htmlspecialchars($aConference['title'])
        .    '</a>' . "\n"
        .    '</td>';


        // startTime
        $confList .= '<td>'
        .    claro_disp_localised_date($dateTimeFormatLong, $aConference['startTime'])
        .    '</td>';

        // duration
        $confList .= '<td>'
        .    get_lang("%duration hour(s)", array("%duration" => htmlspecialchars($aConference['duration'])))
        .    '</td>';

            
        if( $is_allowedToEdit )
        {
            // edit
            $confList .= '<td>' . "\n"
            .    '<a href="index.php?cmd=rqEdit&amp;confId=' . $aConference['id'] . '">' . "\n"
            .    '<img src="'. get_icon_url('edit').'" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // delete
            $confList .= '<td>' . "\n"
            .    '<a href="index.php?cmd=rqDelete&amp;confId=' . $aConference['id']. '" onclick="javascript:return confirmDeleteConference('.$aConference['id'].',\''.$aConference['title'].'\')">' . "\n"
            .    '<img src="' . get_icon_url('delete').'" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";
            
            // visible/invisible
            if( $aConference['visibility'] == 'VISIBLE' )
            {
                $confList .= '<td>' . "\n"
                .    '<a href="index.php?cmd=exInvisible&amp;confId=' . $aConference['id'] . '">' . "\n"
                .    '<img src="' . get_icon_url('visible').'" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
            else
            {
                $confList .= '<td>' . "\n"
                .    '<a href="index.php?cmd=exVisible&amp;confId=' . $aConference['id'] . '">' . "\n"
                .    '<img src="' . get_icon_url('invisible').'" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
        }

        $confList .= '</tr>' . "\n\n";
    }

    $confList .=  '</tbody>' . "\n";
}

if( $displayedConfCount == 0 )
{
    $confList .= '<tfoot>' . "\n"
    .    '<tr>' . "\n"
    .    '<td align="center" colspan="8">' . get_lang('No conference scheduled') . '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '</tfoot>' . "\n";
}

$confList .= '</table>' . "\n";

$claroline->display->header->addHtmlHeader('<script type="text/javascript">

function confirmDeleteConference(id,title) {
    if(confirm("'.get_lang('Are you sure to delete conference').'"+" "+title+" ?")){
        window.location="index.php?cmd=exDelete&confId=" +id;
        return false;
    }
    else {
        return false;
    }
    
}

</script>');

$claroline->display->body->appendContent($confList);

//return body html required
echo $claroline->display->render();

?>