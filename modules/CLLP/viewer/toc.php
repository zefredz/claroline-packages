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
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

Claroline::getInstance()->setDisplayType(Claroline::FRAME);

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
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/CLLP.lib.php';
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/item.class.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


// prepare html header
$htmlHeaders = '<link rel="stylesheet" type="text/css" href="' . get_module_url('CLLP') . '/css/cllp.css" media="screen, projection, tv" />' . "\n"
.     '<script type="text/javascript">' . "\n"
.     '    var lpHandler = window.parent.lpHandler;' . "\n"
.    '  function exitConfirmation() ' . "\n"
.    '  {' . "\n"
.    '    if( confirm(\''.clean_str_for_javascript(get_lang('Are you sure to leave this learning path ?')).'\'))' . "\n" 
.    '    {return true;}' . "\n"
.    '    else '. "\n"
.    '    {return false;}' . "\n"
.    '  }' . "\n"
.     '</script>' . "\n";


/*
 * Output
 */

$html = '';

// Navigation
$html .= "\n" . '<div id="navigation">' . "\n";

//- previous and next links
$html .= '';


$html .= '<p>' . "\n"
//- back to list
.    '<a href="'.get_module_url('CLLP').'/index.php" title="'.get_lang('Back to list').'" target="_top" onClick="return exitConfirmation();">'
.    '<img src="'.get_icon_url('go-home').'" alt="'.get_lang('Back to list').'" />'
.    '</a>' . "\n"
//- tracking
.     '&nbsp;&nbsp;'
.    '<a href="'.get_module_url('CLLP').'/'.(claro_is_allowed_to_edit() ? 'track_path.php?' : 'track_path_details.php?').'pathId='.$pathId.'" title="'.get_lang('View statistics').'" target="_top" onClick="return exitConfirmation();">'
.    '<img src="'.get_icon_url('statistics').'" alt="'.get_lang('View statistics').'" />'
.    '</a>' . "\n"
//- previous and next buttons
.     '&nbsp;&nbsp;'
.    '<a href="#" title="'.get_lang('Previous').'" onClick="lpHandler.goPrevious(); return false;" id="goPrevious">'
.    '<img src="'.get_icon_url('go_left').'" alt="'.get_lang('Previous').'" />'
.    '</a>' . "\n"
.    '<a href="#" title="'.get_lang('Next').'" onClick="lpHandler.goNext(); return false;" id="goNext">'
.    '<img src="'.get_icon_url('go_right').'" alt="'.get_lang('Next').'" />'
.    '</a>' . "\n"
//- full screen switch
.     '&nbsp;&nbsp;'
.    '<a href="#" title="'.get_lang('Fullscreen').'" onClick="lpHandler.setFullscreen(); return false;">'
.    '<img src="'.get_icon_url('view-fullscreen').'" alt="'.get_lang('Fullscreen').'" />'
.    '</a>' . "\n"
.    '<a href="#" title="'.get_lang('Embedded').'" onClick="lpHandler.setEmbedded(); return false;">'
.    '<img src="'.get_icon_url('view-embedded').'" alt="'.get_lang('Embedded').'" />'
.    '</a>' . "\n"

.    '</p>' . "\n\n"
.    '</div>' . "\n";

// table of content
$html .= "\n" . '<div id="table_of_content">' . "\n" . '</div>' . "\n";

$html .= "\n" .'<hr />' . "\n";

$html .= '<div style="padding: 10px 5px;" class="claroCmd">' . "\n"
// links - back to list
.    '<div style="text-align: left;">' . "\n"
.    '<a href="'.get_module_url('CLLP').'/index.php" title="'.get_lang('Back to list').'" target="_top" onClick="return exitConfirmation();">'
.    '<img src="'.get_icon_url('go-home').'" alt="'.get_lang('Back to list').'" />'
.    '&nbsp;' . get_lang('Back to list')
.    '</a>' . "\n"
.    '</div>' . "\n"
// links - tracking
.    '<div style="text-align: left;">' . "\n"
.    '<a href="'.get_module_url('CLLP').'/'.(claro_is_allowed_to_edit() ? 'track_path.php?' : 'track_path_details.php?').'pathId='.$pathId.'" title="'.get_lang('View statistics').'" target="_top" onClick="return exitConfirmation();">'
.    '<img src="'.get_icon_url('statistics').'" alt="'.get_lang('View statistics').'" />'
.    '&nbsp;' . get_lang('View statistics')
.    '</a>' . "\n"
.    '</div>' . "\n"
// links - Previous
.    '<div style="text-align: left;">' . "\n"
.    '<a href="#" title="'.get_lang('Previous').'" onClick="lpHandler.goPrevious(); return false;" id="goPrevious">'
.    '<img src="'.get_icon_url('go_left').'" alt="'.get_lang('Previous').'" />'
.    '&nbsp;' . get_lang('Previous')
.    '</a>' . "\n"
.    '</div>' . "\n"
// links - Next
.    '<div style="text-align: left;">' . "\n"
.    '<a href="#" title="'.get_lang('Next').'" onClick="lpHandler.goNext(); return false;" id="goNext">'
.    '<img src="'.get_icon_url('go_right').'" alt="'.get_lang('Next').'" />'
.    '&nbsp;' . get_lang('Next')
.    '</a>' . "\n"
.    '</div>' . "\n"
// links - Fullscreen
.    '<div style="text-align: left;">' . "\n"
.    '<a href="#" title="'.get_lang('Fullscreen').'" onClick="lpHandler.setFullscreen(); return false;">'
.    '<img src="'.get_icon_url('view-fullscreen').'" alt="'.get_lang('Fullscreen').'" />'
.    '&nbsp;' . get_lang('Fullscreen')
.    '</a>' . "\n"
.    '</div>' . "\n"
// links - Embedded
.    '<div style="text-align: left;">' . "\n"
.    '<a href="#" title="'.get_lang('Embedded').'" onClick="lpHandler.setEmbedded(); return false;">'
.    '<img src="'.get_icon_url('view-embedded').'" alt="'.get_lang('Embedded').'" />'
.    '&nbsp;' . get_lang('Embedded')
.    '</a>' . "\n"
.    '</div>' . "\n"
//
.   '</div>'
;

// debug messages
$html .= "\n"
.   '<div id="lp_debug">' ."\n"
.   '</div>' . "\n\n";


// output
$claroline->display->banner->hide();
$claroline->display->body->hideClaroBody();
$claroline->display->footer->hide();

$claroline->display->body->setContent($html);

$claroline->display->header->addHtmlHeader($htmlHeaders);


echo $claroline->display->render();

