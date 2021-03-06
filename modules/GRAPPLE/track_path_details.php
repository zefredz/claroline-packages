<?php

/**
 * CLAROLINE
 *
 * @version 1.9 $Revision$
 *
 * @copyright (c) 2001-2009 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package GRAPPLE
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
 */

$tlabelReq = 'GRAPPLE';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
require_once dirname( __FILE__ ) . '/lib/path.class.php';
require_once dirname( __FILE__ ) . '/lib/attempt.class.php';
require_once dirname( __FILE__ ) . '/lib/item.class.php';
require_once get_path('incRepositorySys').'/lib/course_user.lib.php';
require_once get_path('incRepositorySys').'/lib/user.lib.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'rqClearProgression', 'exClearProgression',
                            'rqClearThisProgression', 'exClearThisProgression' );

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                            $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['userId']) && is_numeric($_REQUEST['userId']) )   $userId = (int) $_REQUEST['userId'];
else                                                                  $userId = claro_get_current_user_id();

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;

$path = new path();

if( !is_null($pathId) )
{
    if( !$path->load($pathId) )
    {
        $cmd = null;
        $pathId = null;
    }
}

$is_allowedToEdit = claro_is_allowed_to_edit();

$dialogBox = new DialogBox();

if( is_null($pathId) )
{
    claro_die( get_lang('Not allowed') );
}
elseif( !$is_allowedToEdit && $userId != claro_get_current_user_id() )
{
    claro_die( get_lang('Not allowed'));
}
else
{
    if( $is_allowedToEdit )
    {
        switch( $cmd )
        {
            case 'rqClearProgression' :
                {
                    $htmlConfirmDelete = get_lang( 'Are you sure that you want to clear all the progression of this user ?' )
                    .    '<br /><br />'
                    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exClearProgression&amp;pathId='.$pathId.'&userId='.$userId.'">' . get_lang('Yes') . '</a>'
                    .    '&nbsp;|&nbsp;'
                    .    '<a href="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'&userId='.$userId.'">' . get_lang('No') . '</a>'
                    ;
                    $dialogBox->question( $htmlConfirmDelete );
                }
                break;
            case 'exClearProgression' :
                {
                    if( $path->clearProgression( $userId ) )
                    {
                        $dialogBox->success( get_lang( 'Progression cleared successfully for this user.' ) );
                    }
                    else
                    {
                        $dialogBox->error( get_lang( 'Unable to clear the progression for this user.' ) );
                    }
                }
                break;
            case 'rqClearThisProgression' :
                {
                    $htmlConfirmDelete = get_lang( 'Are you sure that you want to clear all this progression ?' )
                    .    '<br /><br />'
                    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exClearThisProgression&amp;pathId='.$pathId.'&userId='.$userId.'&itemId=' .$itemId . '">' . get_lang('Yes') . '</a>'
                    .    '&nbsp;|&nbsp;'
                    .    '<a href="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'&userId='.$userId.'">' . get_lang('No') . '</a>'
                    ;
                    $dialogBox->question( $htmlConfirmDelete );
                }
                break;
            case 'exClearThisProgression' :
                {
                    if( $path->clearProgression( $userId, $itemId ) )
                    {
                        $dialogBox->success( get_lang( 'Progression cleared successfully for this user.' ) );
                    }
                    else
                    {
                        $dialogBox->error( get_lang( 'Unable to clear the progression for this user.' ) );
                    }
                }
                break;
        }
    }
    $itemList = new PathItemList($pathId);
    $itemListArray = $itemList->getFlatList();
    
    //-- Content
    $cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'clpages', 'screen');
    
    $out = '';
    
    $nameTools = get_lang('Learning paths tracking');

    
    ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), './index.php'.claro_url_relay_context('?') );
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, './track_path.php?pathId=' . $pathId.claro_url_relay_context('&amp;') );
    
    $titleTab['mainTitle'] = $nameTools;
    $titleTab['subTitle'] = htmlspecialchars( $path->getTitle() );
    
    $out .= claro_html_tool_title($titleTab);

    $out .= $dialogBox->render();
    
    // display user informations
    $uDetails = user_get_properties($userId);
    $out .= '<div>'
    .   get_lang('User') .' : <br />'."\n"
    .   '<ul>'."\n"
    .   '<li>'.get_lang('Last name').' : '.$uDetails['lastname'].'</li>'."\n"
    .   '<li>'.get_lang('First name').' : '.$uDetails['firstname'].'</li>'."\n"
    .   '<li>'.get_lang('Email').' : '.$uDetails['email'].'</li>'."\n"
    .   '</ul>'."\n"
    .   '</div>' . "\n\n"
    ;
    
    $out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
    .   '<thead>' . "\n"
    .   '<tr class="headerX" align="center" valign="top">'
    .   '<th>'.get_lang('Item').'</th>'."\n"
    .   '<th>'.get_lang('Last session time').'</th>'."\n"
    .   '<th>'.get_lang('Total time').'</th>'."\n"
    .   '<th>'.get_lang('Module status').'</th>'."\n"
    .   '<th colspan="2">'.get_lang('Progress').'</th>'."\n"
    ;
    if( $is_allowedToEdit )
    {
        $out .= '<th style="width: 15%;">' . get_lang( 'Clear progression' ) . '</th>' . "\n"
        ;
    }
    $out .=   '</tr>'."\n\n"
    .   '<tbody>'."\n\n"
    ;
    
    if(!empty($itemListArray) && is_array($itemListArray) )
    {
        foreach($itemListArray as $anItem)
        {
            $out .= '<tr '.($anItem['visibility'] == 'INVISIBLE' ? 'class="invisible"' : '').'>'
            .   '<td align="left" style="padding-left:'.(5 + $anItem['deepness']*10).'px;">'
            .   '<img src="'.(($anItem['type'] == 'CONTAINER')? get_icon_url('chapter'): get_icon_url('item')).'" alt="" />'
            .   '&nbsp;' . $anItem['title']
            .   '</td>' . "\n";
            if( $anItem['type'] == 'CONTAINER' )
            {
                $out .= '<td colspan="5">&nbsp;</td>';
            }
            else            
            {
                $progress = 0;
                $attempt = new attempt();
                if( $attempt->load($pathId, $userId ) )
                {
                    $itemAttempt = new itemAttempt();
                    if( $itemAttempt->load( $attempt->getId(), $anItem['id']) )
                    {                        
                        $progress = $itemAttempt->getScoreMax() ? $itemAttempt->getScoreRaw() / $itemAttempt->getScoreMax() * 100 : 0;
                        $out .= '<td class="centerContent">' . unixToDHMS(scormToUnixTime($itemAttempt->getSessionTime())) . '&nbsp;</td>'
                        .   '<td class="centerContent">' . unixToDHMS(scormToUnixTime($itemAttempt->getTotalTime())) . '&nbsp;</td>'
                        ;
                    }
                    else
                    {
                       $out .= '<td class="centerContent">&nbsp;</td>' . "\n"
                       .    '<td class="centerContent">&nbsp;</td>' . "\n"
                       ;
                    }
                }
                else
                {
                    $out .= '<td class="centerContent">&nbsp;</td>' . "\n"
                    .    '<td class="centerContent">&nbsp;</td>' . "\n"
                    ;
                }
                
                if( isset( $itempAttempt ) && !is_null( $itemAttempt ) )
                {
                   switch( $itemAttempt->getCompletionStatus() )
                    {
                        case 'NOT ATTEMPTED' : $completionStatus = get_lang('Not attempted'); break;
                        case 'PASSED' : $completionStatus = get_lang('Passed'); break;
                        case 'FAILED' : $completionStatus = get_lang('Failed'); break;
                        case 'COMPLETED' : $completionStatus = get_lang('Complete'); break;
                        case 'BROWSED' : $completionStatus = get_lang('Browsed'); break;
                        case 'INCOMPLETE' : $completionStatus = get_lang('Incomplete'); break;
                        default : $completionStatus = get_lang('Unknow');
                    } 
                }
                else
                {
                    $completionStatus = get_lang('Unknow');
                }                
                
                $out .= '<td class="centerContent">' . $completionStatus . '</td>'
                .   '<td align="right">' . claro_html_progress_bar($progress, 1) .'</td>'
                .   '<td align="left">' . $progress . '%' . '</td>'
                ;
                if( $is_allowedToEdit )
                {
                    $out .= '<td style="text-align: center;"><a href="track_path_details.php?cmd=rqClearThisProgression&pathId=' . $pathId . '&userId=' . $userId . '&itemId=' . $anItem['id'] .'"><img src="' . get_icon_url( 'delete' ) . '" alt="' . get_lang('Delete') . '" /></a></td>'
                    ;
                }
            }
            $out .= '</tr>'
            ;
        }
    }    
    
    $out .= '</tbody>' . "\n\n"
    .   '</table>' . "\n\n"
    ;
    
    
    
    $claroline->display->body->appendContent($out);

    echo $claroline->display->render();
}

?>
