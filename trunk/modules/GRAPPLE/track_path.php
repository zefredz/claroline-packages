<?php // $Id$
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
require_once get_path('incRepositorySys').'/lib/course_user.lib.php';

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

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

if( !($is_allowedToEdit && !is_null( $pathId ) ) )
{
    claro_die( get_lang('Not allowed') );
}
else
{
    //-- Content
    $cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'clpages', 'screen');
    
    $out = '';
    
    $nameTools = get_lang('Learning paths tracking');

    
    ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), './index.php'.claro_url_relay_context('?') );
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, './track_path.php?pathId='.$pathId.claro_url_relay_context('&amp;') );
    
    $titleTab['mainTitle'] = $nameTools;
    $titleTab['subTitle'] = htmlspecialchars( $path->getTitle() );
    
    $out .= claro_html_tool_title($titleTab);

    $out .= $dialogBox->render();
    
    $out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
    .   '<thead>' . "\n"
    .   '<tr class="headerX" align="center" valign="top">'
    .   '<th>'.get_lang('Student').'</th>'."\n"
    .   '<th colspan="2">'.get_lang('Progress').'</th>'."\n"
    .   '</tr>'."\n\n"
    .   '<tbody>'."\n\n";
    
    $usersList = claro_get_course_user_list();
    
    foreach( $usersList as $key => $user )
    {
        $lastname[$key] = strtolower($user['nom']);
        $firstname[$key] = strtolower($user['prenom']);
    }    
    
    array_multisort( $lastname, SORT_ASC, $firstname, SORT_ASC, $usersList);
    
    foreach( $usersList as $user)
    {
        $out .= '<tr>'
        .   '<td>'
        .   '<a href="track_path_details.php?pathId='.  $pathId .'&userId='. $user['user_id'] .'">'
        .   htmlspecialchars(get_lang('%firstname %lastname', array( '%firstname' => $user['prenom'], '%lastname' => $user['nom'])))
        .   '</a></td>';
        
        // load user progression path
        $totalProgress = 0;
        $i = 0;
        //load Attempt
        $thisAttempt = new Attempt();
        $thisAttempt->load( $path->getId(), $user['user_id'] );
        $lpProgress = $thisAttempt->getProgress();
        
        $out .=    '<td align="right" >' . "\n"
        .    claro_html_progress_bar($lpProgress, 1)
        .    '</td>' . "\n"
        .    '<td align="left">' . "\n"
        .    '<small>'
        .   '<a href="track_path_details.php?pathId='.  $pathId .'&userId='. $user['user_id'] .'">'
        .    $lpProgress . '%' . "\n"
        .   '</a>'
        .    '</small>' . "\n"
        .    '</td>' . "\n"
        .   '</tr>' . "\n"
        ;
    }
    
    $out .= '</tbody>' . "\n\n"
    .   '</table>' . "\n\n";
    
    $claroline->display->body->appendContent($out);

    echo $claroline->display->render();
    
}



?>