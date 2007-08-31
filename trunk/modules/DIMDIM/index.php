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

/*
 * init request vars
 */
$acceptedCmdList = array(   'rqEdit', 'exEdit' );
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
        $startTime = $_REQUEST['day'];
        
    	$conference->setTitle($_REQUEST['title']);
    	$conference->setDescription($_REQUEST['description']);
    	$conference->setWaitingArea($_REQUEST['waitingArea']);
    	$conference->setMaxUsers($_REQUEST['maxUsers']);
    	$conference->setDuration($_REQUEST['duration']);
    	$conference->setType($_REQUEST['type']);
    	$conference->setAttendeeMikes($_REQUEST['attendeeMikes']);
    	$conference->setNetwork($_REQUEST['network']);
    	$conference->setStartTime($startTime);


    	if( $conference->validate() )
        {
            if( $conference->save() )
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
        // viewmode
        .	 get_lang('Default view mode') . '&nbsp;<span class="required">*</span><br />' . "\n"
    	.	 '<input type="radio" name="viewMode" id="viewModeEmb" value="EMBEDDED" '.($conference->isFullscreen()?'':'checked="checked"').'>'
    	.	 '<label for="viewModeEmb">'.get_lang('Embedded').'</label><br />' . "\n"
    	.	 '<input type="radio" name="viewMode" id="viewModeFull" value="FULLSCREEN" '.($conference->isFullscreen()?'checked="checked"':'').'>'
    	.	 '<label for="viewModeFull">'.get_lang('Fullscreen').'</label>' . "\n"
    	.	 '<br /><br />'
        // charset : TODO

        .	 '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
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
    	$path->setVisible();

    	$path->save();
    }

    if( $cmd == 'exInvisible' )
    {
    	$path->setInvisible();

    	$path->save();
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
    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqCreate' . claro_url_relay_context('&amp;'),get_lang('Schedule a conference'));
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
    // display path name and tools to edit it
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
        echo '<td align="left">'
        .    '<a href="viewer/index.php?pathId='.$aConference['id'].'" title="'.htmlspecialchars(strip_tags($aConference['description'])).'">'
        .    '<img src="' . get_path('imgRepositoryWeb') . 'learnpath.gif" alt="" border="0" />'
        .    htmlspecialchars($aConference['title'])
        .    '</a>' . "\n"
        .    '</td>';
        
        // startTime
        echo '<td>'
        .    claro_disp_localised_date($dateFormatLong, $aConference['startTime'])
        .    '</td>';
        
        // duration
        echo '<td>'
        .    get_lang("%duration hours", array("%duration" => htmlspecialchars($aConference['duration'])
        .    '</td>';
        
        if( $is_allowedToEdit )
        {
            // edit
            echo '<td>' . "\n"
            .    '<a href="index.php?confId=' . $aConference['id'] . '">' . "\n"
            .    '<img src="' . get_path('imgRepositoryWeb') . 'edit.gif" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // delete
            echo '<td>' . "\n"
            .    '<a href="index.php?cmd=rqDelete&amp;confId=' . $aConference['id'] . '">' . "\n"
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
