<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package GOOGLSCH
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

//$tlabelReq = 'GOOGLSCH';
$moduleLabel = 'GOOGLSCH';
$moduleUrl = get_module_url($moduleLabel);
include_once claro_get_conf_repository().'GOOGLSCH.conf.php';

// shorten another site url for better display (and google doesn't require the http)
if( get_conf('google_sch_on_other_site') != '' )    $anotherSiteUrl = str_replace('http://','',trim(get_conf('google_sch_on_other_site')));
else                                                $anotherSiteUrl = '';

/**
 * DISPLAY
 */
// It seems we cannot use style.css at this so I use an inline style
$searchBoxStyle =
     'background: #FCFCFC url(\''.$moduleUrl.'/googleSearch.png\') no-repeat 2px center;'
.    'padding-left: 21px;'
;


$html = "\n\n"

.	 '<!-- Google search -->' . "\n"
.	 '<form method="get" action="http://www.google.com/search" id="googleSearch">' . "\n"
.	 '<div style="padding: 10px 5px; margin: 5px 0;">' . "\n"
.	 '<input type="text" name="q" id="q" size="12" maxlength="255" value="" style="'.$searchBoxStyle.'" />' . "\n"
.	 '<input type="submit" value="' .get_lang('Ok'). '" />'
.    '<br />' . "\n"
;

if( get_conf('google_sch_on_this_campus') && get_conf('google_sch_on_other_site') )
{
    // search on www, campus, and another site
    $html .= '<input type="radio" name="sitesearch" id="wwwSearch" value="" />'. "\n"
    .        '<label for="wwwSearch">'. "\n"
    .        get_lang('On the web') . "\n"
    .        '</label>'. "\n"
    .        '<br />'. "\n"
    .        '<input type="radio" name="sitesearch" id="campusSearch" value="'.get_path('rootWeb').'" checked="checked" />'. "\n"
    .        '<label for="campusSearch">'.get_lang('Only on this campus').'</label>'. "\n"
    .        '<br />'. "\n"
    .        '<input type="radio" name="sitesearch" id="anotherSiteSearch" value="'.$anotherSiteUrl.'" />'. "\n"
    .        '<label for="anotherSiteSearch">'
    .        get_lang('On %siteName', array('%siteName' => $anotherSiteUrl))
    .        '</label>'. "\n"
    ;
}
else
{
    if( get_conf('google_sch_on_this_campus') )
    {
        // search on www, campus
        $html .= '<input type="checkbox" name="sitesearch" id="campusSearch" value="'.get_conf('rootWeb').'" checked="checked" /><label for="campusSearch">'.get_lang('Only on this campus').'</label>'. "\n";
    }
    elseif( get_conf('google_sch_on_other_site') )
    {
        // search on www, another site
        $html .= '<input type="checkbox" name="sitesearch" id="anotherSiteSearch" value="'.$anotherSiteUrl.'" checked="checked" /><label for="anotherSiteSearch">'.get_lang('On %siteName', array('%siteName' => $anotherSiteUrl)) . '</label>'. "\n";
    }
}

$html .= '</div>' . "\n"
.	 '</form>' . "\n"
.	 '<!-- Google search -->' . "\n\n"
;

$claro_buffer->append($html);

?>
