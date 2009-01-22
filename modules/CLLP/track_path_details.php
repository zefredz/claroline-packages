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
 * @package CLLP
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
 */

$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
require_once dirname( __FILE__ ) . '/lib/path.class.php';
require_once dirname( __FILE__ ) . '/lib/attempt.class.php';
require_once dirname( __FILE__ ) . '/lib/item.class.php';
require_once get_path('incRepositorySys').'/lib/course_user.lib.php';
require_once get_path('incRepositorySys').'/lib/user.lib.php';

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['userId']) && is_numeric($_REQUEST['userId']) )   $userId = (int) $_REQUEST['userId'];
else                                                                  $userId = null;

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

if( !($is_allowedToEdit && !is_null($pathId) && !is_null($userId) ) )
{
    claro_die( get_lang('Not allowed') );
}
else
{
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
    .   '</tr>'."\n\n"
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
                        $progress = $itemAttempt->getScoreRaw() / $itemAttempt->getScoreMax() * 100;    
                    }
                    
                }
                $out .= '<td class="centerContent">' . $itemAttempt->getSessionTime() . '</td>'
                .   '<td class="centerContent">' . $itemAttempt->getTotalTime() . '</td>'
                ;
                
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
                
                $out .= '<td class="centerContent">' . $completionStatus . '</td>'
                .   '<td align="right">' . claro_html_progress_bar($progress, 1) .'</td>'
                .   '<td align="left">' . $progress . '%' . '</td>'
                ;
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
