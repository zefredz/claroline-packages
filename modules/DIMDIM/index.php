<?php // $Id$
/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package DIMDIM
 *
 * @author Sebastien Piraux
 *
 */
 
$tlabelReq = 'DIMDIM';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

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

/*
 * On the fly install
 */

install_module_in_course( 'DIMDIM', claro_get_current_course_id() ) ;

require_once dirname( __FILE__ ) . '/lib/DIMDIM.class.php';
require_once get_path('incRepositorySys') . '/lib/form.lib.php';

/*
 * init request vars
 */
 
$acceptedCmdList = array('rqEdit', 'exEdit', 'exDelete', 'exVisible', 'exInvisible');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                            $cmd = null;

if( isset($_REQUEST['confId']) && is_numeric($_REQUEST['confId']) )   $confId = (int) $_REQUEST['confId'];
else                                                                  $confId = null;

/*
 * init other vars
 */

$conference = new conference(); 

if( !is_null($confId) )
{
    if( !$conference->load($confId) )
    {
        $cmd = null;
        $confId = null;
    }
}

$conferenceList = new conferenceList();

claro_set_display_mode_available(true);

$is_allowedToEdit = claro_is_allowed_to_edit();

$dialogBox = '';

/*
 * Admin only commands
 */

if( $is_allowedToEdit )
{
    if( $cmd == 'exEdit' )
    {       
    	$conference->setTitle($_REQUEST['title']);
    	$conference->setDescription($_REQUEST['description']);
    	$conference->setWaitingArea($_REQUEST['waitingArea']);
    	$conference->setMaxUsers($_REQUEST['maxUsers']);
    	$conference->setDuration($_REQUEST['duration']);
    	$conference->setType($_REQUEST['type']);
    	$conference->setAttendeeMikes($_REQUEST['attendeeMikes']);
    	$conference->setNetwork($_REQUEST['network']);   	
    	$conference->setStartTime(mktime($_REQUEST['startHour'],$_REQUEST['startMinute'],0,$_REQUEST['startMonth'],$_REQUEST['startDay'],$_REQUEST['startYear']) );

    	if( $conference->validate() )
        {
            if( $insertedId = $conference->save() )
            {
            	if( is_null($confId) )
                {
                    $dialogBox .= get_lang('Conference successfully created');
                    $confId = $insertedId;
                }
                else
                {
                	$dialogBox .= get_lang('Conference successfully modified');
                }
            }
            else
            {
                // sql error in save() ?
                $cmd = 'rqEdit';
            }

        }
        else
        {
            if( claro_failure::get_last_failure() == 'conference_no_title' )
            {
                $dialogBox .= '<p>' . get_lang('Field \'%name\' is required', array('%name' => get_lang('Title'))) . '</p>';
            }
            
            if( claro_failure::get_last_failure() == 'conference_invalid_date' )
            {
                $dialogBox .= '<p>' . get_lang('Date is in the past') . '</p>';
            }            
            $cmd = 'rqEdit';
        }
    }

    if( $cmd == 'rqEdit' )
    {
    	// show form
        $dialogBox .= "\n\n";

        if( !is_null($confId) )
        {
        	$dialogBox .= '<strong>' . get_lang('Edit conference settings') . '</strong>' . "\n";
        }
        else
        {
        	$dialogBox .= '<strong>' . get_lang('Create a new conference') . '</strong>' . "\n";
        }

        $dialogBox .= '<form action="' . $_SERVER['PHP_SELF'] . '?confId='.$confId.'" method="post">' . "\n"
        .    claro_form_relay_context()
        .	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
        .	 '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

        // title
        .	 '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .	 '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($conference->getTitle()).'" /><br />' . "\n"

        // description
        .	 '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
        .	 '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($conference->getDescription()).'</textarea><br />'

        // waiting area
        . get_lang('Waiting Area') . '<br />' . "\n"
        . '<input id="label_waiting_area_TRUE"  type="radio" name="waitingArea" value="ENABLE"  '
        . ( $conference->getWaitingArea() == 'ENABLE' ? ' checked="checked" ':' ') . ' >'
        . '<label for="label_waiting_area_TRUE"  >' . get_lang('Yes') . '</label>' . '&nbsp;'
        . '<input id="label_waiting_area_FALSE" type="radio" name="waitingArea" value="DISABLE" '
        . ( $conference->getWaitingArea() == 'DISABLE' ? ' checked="checked" ': ' ') . ' >'
        . '<label for="label_waiting_area_FALSE" >' . get_lang('No') . '</label><br />' . "\n";

        // maximum users

        $dialogBox .= '<label for="label_max_users"  >'.get_lang('Maximum users').'</label><br />' . "\n" ;
       
        // display select box with accepted value

        $maxUserValueList = array('20', '40', '60', '80', '100', '200', '300', '400', '500');

        $dialogBox .= '<select id="label_max_users" name="maxUsers">' . "\n";

        foreach ( $maxUserValueList as $maxUserValue )
        {
            if ( $maxUserValue == $conference->getMaxUsers() )
            {
                $dialogBox .= '<option value="'. htmlspecialchars($maxUserValue) .'" selected="selected">' . $maxUserValue .'</option>' . "\n";
            }
            else
            {
                $dialogBox .= '<option value="'. htmlspecialchars($maxUserValue) .'" >' . $maxUserValue .'</option>' . "\n";
            }
        } // end foreach

        $dialogBox .= '</select><br />' . "\n";

        // duration in hours
        
        $dialogBox .= '<label for="label_duration"  >'.get_lang('Duration').'</label><br />' . "\n" ;

        $durationValueList = array('1','2','3','4','5');

        $dialogBox .= '<select id="label_duration" name="duration">' . "\n";

        foreach ( $durationValueList as $durationValue )
        {
            if ( $durationValue == $conference->getDuration() )
            {
                $dialogBox .= '<option value="'. htmlspecialchars($durationValue) .'" selected="selected">' . $durationValue .'</option>' . "\n";
            }
            else
            {
                $dialogBox .= '<option value="'. htmlspecialchars($durationValue) .'" >' . $durationValue .'</option>' . "\n";
            }
        }

        $dialogBox .= '</select><br />' . "\n";

        // type video and audio or audio only, default is audio
        
        $dialogBox .= '<label for="label_type"  >'.get_lang('Type').'</label><br />' . "\n" ;

        $typeValueList = array('AUDIO' => get_lang('Audio') , 'VIDEO' => get_lang('Audio/Video'));

        $dialogBox .= '<select id="label_type" name="type">' . "\n";

        foreach ( $typeValueList as $typeValue => $typeLabel )
        {
            if ( $typeValue == $conference->getType() )
            {
                $dialogBox .= '<option value="'. htmlspecialchars($typeValue) .'" selected="selected">' . $typeLabel .'</option>' . "\n";
            }
            else
            {
                $dialogBox .= '<option value="'. htmlspecialchars($typeValue) .'" >' . $typeLabel .'</option>' . "\n";
            }
        } // end foreach

        $dialogBox .= '</select><br />' . "\n";

        // attendee Mikes
        
        $dialogBox .= '<label for="label_attendeeMikes"  >'.get_lang('Attendee mikes').'</label><br />' . "\n" ;

        $attendeeMikesValueList = array('1','2','3','4','5');

        $dialogBox .= '<select id="label_attendeeMikes" name="attendeeMikes">' . "\n";

        foreach ( $attendeeMikesValueList as $attendeeMikesValue )
        {
            if ( $attendeeMikesValue == $conference->getAttendeeMikes() )
            {
                $dialogBox .= '<option value="'. htmlspecialchars($attendeeMikesValue) .'" selected="selected">' . $attendeeMikesValue .'</option>' . "\n";
            }
            else
            {
                $dialogBox .= '<option value="'. htmlspecialchars($attendeeMikesValue) .'" >' . $attendeeMikesValue .'</option>' . "\n";
            }
        }

        $dialogBox .= '</select><br />' . "\n";

        // network type
        $dialogBox .= '<label for="network_type"  >'.get_lang('Network').'</label><br />' . "\n" ;

        $networkValueList = array('DIALUP' => get_lang('Dial up') , 'CABLEDSL' => get_lang('Cable/Dsl'), 'LAN' => get_lang('LAN') );

        $dialogBox .= '<select id="label_network" name="network">' . "\n";

        foreach ( $networkValueList as $networkValue => $networkLabel )
        {
            if ( $networkValue == $conference->getNetwork() )
            {
                $dialogBox .= '<option value="'. htmlspecialchars($networkValue) .'" selected="selected">' . $networkLabel .'</option>' . "\n";
            }
            else
            {
                $dialogBox .= '<option value="'. htmlspecialchars($networkValue) .'" >' . $networkLabel .'</option>' . "\n";
            }
        } // end foreach

        $dialogBox .= '</select><br />' . "\n";        

        // startTime
        $dialogBox .= get_lang('Start date') . '<br />' . "\n" 
        . claro_html_date_form('startDay', 'startMonth', 'startYear', $conference->getStartTime(), 'long') 
        . ' - ' 
        . claro_html_time_form("startHour", "startMinute", $conference->getStartTime())
    	. '<small>' . get_lang('(d/m/y hh:mm)') . '</small><br />' . "\n";

        $dialogBox .= '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
        .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
        .    claro_html_button($_SERVER['PHP_SELF'] . '?confId='.$confId, get_lang('Cancel'))
        .    '</form>' . "\n"
        ;

    }

    if( $cmd == 'exDelete' )
    {
    	if( $conference->delete() )
    	{
    		$dialogBox .= get_lang('Conference succesfully deleted');
    	}
    	else
    	{
    		$dialogBox .= get_lang('Fatal error : cannot delete conference');
    	}
    }

    if( $cmd == 'rqDelete' )
    {
        $dialogBox .= get_lang('Are you sure to delete conference "%conferenceTitle" ?', array('%conferenceTitle' => htmlspecialchars($conference->getTitle()) ));

        $dialogBox .= '<p>'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;confId='.$confId.'">' . get_lang('Yes') . '</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
        .    '</p>' . "\n";
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


//-- prepare list to display
$conferenceListArray = $conferenceList->load();

/*
 * Output
 */

//-- Content
$nameTools = get_lang('Video webconference');

include  get_path('includePath') . '/claro_init_header.inc.php';

echo claro_html_tool_title($nameTools);

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

$cmdMenu = array();
if($is_allowedToEdit)
{
    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqEdit' . claro_url_relay_context('&amp;'),get_lang('Schedule a conference'));
}

echo '<p>'
.    claro_html_menu_horizontal( $cmdMenu )
.    '</p>';

echo '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n"
.    '<th>' . get_lang('Conference') . '</th>' . "\n"
.    '<th>' . get_lang('Date') . '</th>' . "\n"    
.    '<th>' . get_lang('Duration') . '</th>' . "\n";

if( $is_allowedToEdit )
{
    // display conference name and tools to edit it
    // titles
    echo '<th>' . get_lang('Modify') . '</th>' . "\n"
    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
    .    '<th>' . get_lang('Visibility') . '</th>' . "\n";
}

echo '</tr>' . "\n"
.    '</thead>' . "\n";

echo '<tbody>' . "\n";

if( !empty($conferenceListArray) && is_array($conferenceListArray) )
{ 
    foreach( $conferenceListArray as $aConference )
    {
        // do not display to student if conf is not visible
        if( $aConference['visibility'] == 'INVISIBLE' && !$is_allowedToEdit ) break;
        
        echo '<tr align="center"' . (($aConference['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";
        
        // title
        // TODO : add link to join conference
        echo '<td align="left">'
        .    '<a href="index.php?cmd=rqEdit&amp;confId='.$aConference['id'].'" title="'.htmlspecialchars(strip_tags($aConference['description'])).'">'
        .    htmlspecialchars($aConference['title'])
        .    '</a>' . "\n"
        .    '</td>';
        
        
        // startTime        
        echo '<td>'
        .    claro_disp_localised_date($dateTimeFormatLong, $aConference['startTime'])
        .    '</td>';
        
        // duration
        echo '<td>'
        .    get_lang("%duration hours", array("%duration" => htmlspecialchars($aConference['duration'])))
        .    '</td>';
        
        if( $is_allowedToEdit )
        {
            // edit
            echo '<td>' . "\n"
            .    '<a href="index.php?cmd=rqEdit&amp;confId=' . $aConference['id'] . '">' . "\n"
            .    '<img src="' . get_path('imgRepositoryWeb') . 'edit.gif" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // delete
            // TODO add js confirmation
            echo '<td>' . "\n"
            .    '<a href="index.php?cmd=exDelete&amp;confId=' . $aConference['id'] . '">' . "\n"
            .    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" border="0" alt="' . get_lang('delete') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // visible/invisible
            if( $aConference['visibility'] == 'VISIBLE' )
            {
                echo '<td>' . "\n"
    	        .    '<a href="index.php?cmd=exInvisible&amp;confId=' . $aConference['id'] . '">' . "\n"
    	        .    '<img src="' . get_path('imgRepositoryWeb') . 'visible.gif" border="0" alt="' . get_lang('Make invisible') . '" />' . "\n"
    	        .    '</a>'
    	        .    '</td>' . "\n";
            }
            else
            {
    			echo '<td>' . "\n"
    	        .    '<a href="index.php?cmd=exVisible&amp;confId=' . $aConference['id'] . '">' . "\n"
    	        .    '<img src="' . get_path('imgRepositoryWeb') . 'invisible.gif" border="0" alt="' . get_lang('Make visible') . '" />' . "\n"
    	        .    '</a>'
    	        .    '</td>' . "\n";
            }
         }

        echo '</tr>' . "\n\n";
    }
    echo '</tbody>' . "\n";
}
else
{
    echo '<tfoot>' . "\n"
    .    '<tr>' . "\n"
    .    '<td align="center" colspan="8">' . get_lang('No conference scheduled') . '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '</tfoot>' . "\n";
}

include  get_path('includePath') . '/claro_init_footer.inc.php';

?>
